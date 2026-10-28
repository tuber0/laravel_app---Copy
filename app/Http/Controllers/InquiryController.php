<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;
use App\Models\InquiryMessage;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\InAppNotification;
use App\Models\Artist;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\QueueEntry;
use App\Events\InquiryEvent;
use App\Events\QuotationEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InquiryController extends Controller
{
    private function getStaffArtist()
    {
        return Auth::user()->artist ?? Artist::where('email', Auth::user()->email)->first();
    }

    /**
     * Resolve or find an appropriate Service model based on the inquiry and quotation.
     * Ensures services like "Custom Tattoo", "Body Piercing", "Barber & Styling"
     * are correctly matched or created instead of falling back to "Classic Haircut".
     */
    private function resolveServiceFromInquiry(Inquiry $inquiry, ?Quotation $quotation = null): Service
    {
        $serviceName = trim($inquiry->service_type ?? '');

        // If empty, try to get service name from the first quotation item description
        if ($serviceName === '' && $quotation && $quotation->items && $quotation->items->isNotEmpty()) {
            $serviceName = trim($quotation->items->first()->description ?? '');
        }

        if ($serviceName === '') {
            $serviceName = 'Custom Service';
        }

        // 1. Try exact match (case-insensitive)
        $service = Service::whereRaw('LOWER(name) = ?', [strtolower($serviceName)])->first();
        if ($service) {
            return $service;
        }

        // 2. Try partial match on service name
        $service = Service::where('name', 'like', '%' . $serviceName . '%')->first();
        if ($service) {
            return $service;
        }

        // 3. Determine category based on service name or artist
        $lower = strtolower($serviceName);
        if (str_contains($lower, 'tattoo')) {
            $category = 'Tattoo';
        } elseif (str_contains($lower, 'pierc')) {
            $category = 'Piercing';
        } elseif (str_contains($lower, 'braid') || str_contains($lower, 'cornrow') || str_contains($lower, 'styling')) {
            $category = 'Hair Styling';
        } elseif (str_contains($lower, 'barber') || str_contains($lower, 'cut') || str_contains($lower, 'fade') || str_contains($lower, 'beard') || str_contains($lower, 'hair')) {
            $category = 'Barbering';
        } else {
            $category = $inquiry->artist->category ?? ($quotation && $quotation->artist ? $quotation->artist->category : 'Others');
        }

        // 4. Create or fetch the matching service
        $price = $quotation ? (int)$quotation->total_amount : 0;
        $duration = $quotation && $quotation->estimated_duration ? $quotation->estimated_duration : '60 min';

        return Service::firstOrCreate(
            ['name' => $serviceName],
            [
                'category' => $category,
                'price' => $price,
                'duration' => $duration,
                'description' => 'Custom booking from inquiry #' . $inquiry->id,
            ]
        );
    }

    // ==========================================
    // CUSTOMER METHODS
    // ==========================================

    public function customerIndex()
    {
        $inquiries = Inquiry::where('user_id', Auth::id())
            ->with(['artist', 'latestQuotation'])
            ->latest()
            ->paginate(10);

        return view('customer.inquiries.index', compact('inquiries'));
    }

    public function customerCreate(Request $request)
    {
        $artists = Artist::orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        $selectedArtistId = $request->query('artist_id');
        $selectedArtist = $selectedArtistId ? Artist::find($selectedArtistId) : null;

        return view('customer.inquiries.create', compact('artists', 'services', 'selectedArtist', 'selectedArtistId'));
    }

    public function customerStore(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'service_type' => 'required|string|max:100',
            'description' => 'required|string|min:10',
            'preferred_date' => 'nullable|date|after_or_equal:today',
            'budget' => 'nullable|numeric|min:0',
            'reference_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $imagePaths = [];
        if ($request->hasFile('reference_images')) {
            foreach ($request->file('reference_images') as $image) {
                $path = $image->store('inquiries/references', 'public');
                $imagePaths[] = $path;
            }
        }

        $inquiry = Inquiry::create([
            'user_id' => Auth::id(),
            'artist_id' => $validated['artist_id'],
            'service_type' => $validated['service_type'],
            'description' => $validated['description'],
            'reference_images' => !empty($imagePaths) ? $imagePaths : null,
            'preferred_date' => $validated['preferred_date'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'status' => 'pending',
        ]);

        // In-app notification for artist
        $artist = Artist::find($validated['artist_id']);
        if ($artist) {
            $artistUser = $artist->user ?? \App\Models\User::where('email', $artist->email)->first();
            if ($artistUser) {
                InAppNotification::send(
                    $artistUser->id,
                    'New Inquiry Received',
                    Auth::user()->name . ' sent an inquiry for ' . $inquiry->service_type . '.',
                    route('staff.inquiries.show', $inquiry->id),
                    'quote'
                );
            }

            // Real-time broadcast
            try {
                broadcast(new InquiryEvent(
                    $inquiry->id,
                    $artist->id,
                    Auth::id(),
                    'created',
                    'New Custom Inquiry',
                    Auth::user()->name . ' submitted a custom inquiry.'
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Broadcast failed for InquiryEvent: ' . $e->getMessage());
            }
        }

        Log::info("Inquiry #{$inquiry->id} created by user " . Auth::id() . " for artist {$artist->id}");

        return redirect()->route('customer.inquiries.show', $inquiry->id)
            ->with('success', 'Your inquiry has been submitted! The artist will review and send you a custom quotation soon.');
    }

    public function customerShow($id)
    {
        $inquiry = Inquiry::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['artist', 'messages.user', 'quotations.items'])
            ->firstOrFail();

        return view('customer.inquiries.show', compact('inquiry'));
    }

    public function customerSendMessage(Request $request, $id)
    {
        $inquiry = Inquiry::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('inquiries/attachments', 'public');
        }

        $msg = InquiryMessage::create([
            'inquiry_id' => $inquiry->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        // Notify artist
        $artist = $inquiry->artist;
        if ($artist) {
            $artistUser = $artist->user ?? \App\Models\User::where('email', $artist->email)->first();
            if ($artistUser) {
                InAppNotification::send(
                    $artistUser->id,
                    'New Message on Inquiry #' . $inquiry->id,
                    Auth::user()->name . ': ' . \Illuminate\Support\Str::limit($request->message, 60),
                    route('staff.inquiries.show', $inquiry->id) . '#message-' . $msg->id,
                    'info',
                    $msg->id
                );
            }

            try {
                broadcast(new InquiryEvent(
                    $inquiry->id,
                    $artist->id,
                    Auth::id(),
                    'message_sent',
                    'New Message',
                    Auth::user()->name . ': ' . \Illuminate\Support\Str::limit($request->message, 50)
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Broadcast failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Message sent successfully.');
    }

    public function customerRespondQuotation(Request $request, $id)
    {
        $quotation = Quotation::where('user_id', Auth::id())
            ->where('id', $id)
            ->with(['inquiry.artist', 'items'])
            ->firstOrFail();

        $validated = $request->validate([
            'action' => 'required|in:accept,decline,revision_requested',
            'revision_notes' => 'required_if:action,revision_requested|nullable|string|max:1000',
            'conversion_type' => 'nullable|in:appointment,queue,none',
            'appointment_date' => 'required_if:conversion_type,appointment|nullable|date|after_or_equal:today',
            'appointment_time' => 'required_if:conversion_type,appointment|nullable',
        ]);

        $inquiry = $quotation->inquiry;
        $artist = $quotation->artist;

        if ($validated['action'] === 'accept') {
            $quotation->status = 'accepted';
            $inquiry->status = 'accepted';

            // Optional Conversion: Create Appointment
            if (($validated['conversion_type'] ?? '') === 'appointment' && !empty($validated['appointment_date']) && !empty($validated['appointment_time'])) {
                // Find matching service or dynamically create inquiry service (e.g. "Custom Tattoo")
                $service = $this->resolveServiceFromInquiry($inquiry, $quotation);

                $appointment = Appointment::create([
                    'user_id' => Auth::id(),
                    'client_name' => Auth::user()->name,
                    'phone_number' => Auth::user()->phone ?? 'N/A',
                    'service_id' => $service ? $service->id : 1,
                    'artist_id' => $artist->id,
                    'appointment_date' => $validated['appointment_date'],
                    'appointment_time' => Carbon::parse($validated['appointment_time'])->format('H:i'),
                    'total_price' => $quotation->total_amount,
                    'status' => 'confirmed',
                    'duration' => $quotation->estimated_duration ?? ($service->duration ?? '60 min'),
                    'notes' => 'Converted from Quotation #' . $quotation->id . ' (Inquiry #' . $inquiry->id . ')',
                ]);

                $quotation->appointment_id = $appointment->id;
            } 
            // Optional Conversion: Create Live Queue Ticket
            elseif (($validated['conversion_type'] ?? '') === 'queue') {
                $service = $this->resolveServiceFromInquiry($inquiry, $quotation);
                $today = now()->toDateString();
                $lastNumber = QueueEntry::where('queue_date', $today)->max('queue_number') ?? 0;
                $queueNumber = $lastNumber + 1;

                $waitEstimate = $artist ? $artist->estimated_wait_minutes : 30;

                $ticket = QueueEntry::create([
                    'artist_id' => $artist->id,
                    'service_id' => $service ? $service->id : 1,
                    'user_id' => Auth::id(),
                    'queue_number' => $queueNumber,
                    'client_name' => Auth::user()->name,
                    'phone_number' => Auth::user()->phone ?? 'N/A',
                    'queue_date' => $today,
                    'status' => 'waiting',
                    'estimated_wait_minutes' => $waitEstimate,
                    'notes' => 'Converted from Quotation #' . $quotation->id . ' (₱' . number_format($quotation->total_amount, 2) . ')',
                ]);

                $quotation->queue_entry_id = $ticket->id;
            }

            $quotation->save();
            $inquiry->save();

            // Notify artist
            $artistUser = $artist->user ?? \App\Models\User::where('email', $artist->email)->first();
            if ($artistUser) {
                InAppNotification::send(
                    $artistUser->id,
                    'Quotation Accepted! 🎉',
                    Auth::user()->name . ' accepted Quotation #' . $quotation->id . ' (₱' . number_format($quotation->total_amount, 2) . ').',
                    route('staff.inquiries.show', $inquiry->id) . '#quotation-' . $quotation->id,
                    'success'
                );
            }

            try {
                broadcast(new QuotationEvent(
                    $quotation->id,
                    $inquiry->id,
                    $artist->id,
                    Auth::id(),
                    'accepted',
                    'Quotation Accepted',
                    Auth::user()->name . ' accepted the quotation for ₱' . number_format($quotation->total_amount, 2)
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Broadcast failed: ' . $e->getMessage());
            }

            Log::info("Quotation #{$quotation->id} accepted by customer " . Auth::id());

            return back()->with('success', 'Quotation accepted successfully!' . ($quotation->appointment_id ? ' Your appointment has been booked!' : ($quotation->queue_entry_id ? ' You have joined the live queue!' : '')));

        } elseif ($validated['action'] === 'revision_requested') {
            $quotation->status = 'revision_requested';
            $quotation->revision_notes = $validated['revision_notes'];
            $quotation->save();

            $inquiry->status = 'pending';
            $inquiry->save();

            // Post revision note to inquiry message thread
            $revMsg = InquiryMessage::create([
                'inquiry_id' => $inquiry->id,
                'user_id' => Auth::id(),
                'message' => 'Requested Revision on Quotation #' . $quotation->id . ': ' . $validated['revision_notes'],
            ]);

            // Notify artist
            $artistUser = $artist->user ?? \App\Models\User::where('email', $artist->email)->first();
            if ($artistUser) {
                InAppNotification::send(
                    $artistUser->id,
                    'Revision Requested on Quotation #' . $quotation->id,
                    Auth::user()->name . ' requested revisions: ' . \Illuminate\Support\Str::limit($validated['revision_notes'], 60),
                    route('staff.inquiries.show', $inquiry->id) . '#message-' . $revMsg->id,
                    'warning',
                    $revMsg->id
                );
            }

            try {
                broadcast(new QuotationEvent(
                    $quotation->id,
                    $inquiry->id,
                    $artist->id,
                    Auth::id(),
                    'revision_requested',
                    'Revision Requested',
                    Auth::user()->name . ' requested changes to Quotation #' . $quotation->id
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Broadcast failed: ' . $e->getMessage());
            }

            Log::info("Quotation #{$quotation->id} revision requested by customer " . Auth::id());

            return back()->with('success', 'Revision requested. The artist has been notified.');

        } elseif ($validated['action'] === 'decline') {
            $quotation->status = 'declined';
            $quotation->save();

            $inquiry->status = 'declined';
            $inquiry->save();

            $artistUser = $artist->user ?? \App\Models\User::where('email', $artist->email)->first();
            if ($artistUser) {
                InAppNotification::send(
                    $artistUser->id,
                    'Quotation Declined',
                    Auth::user()->name . ' declined Quotation #' . $quotation->id . '.',
                    route('staff.inquiries.show', $inquiry->id) . '#quotation-' . $quotation->id,
                    'info'
                );
            }

            try {
                broadcast(new QuotationEvent(
                    $quotation->id,
                    $inquiry->id,
                    $artist->id,
                    Auth::id(),
                    'declined',
                    'Quotation Declined',
                    Auth::user()->name . ' declined Quotation #' . $quotation->id
                ))->toOthers();
            } catch (\Throwable $e) {
                Log::warning('Broadcast failed: ' . $e->getMessage());
            }

            Log::info("Quotation #{$quotation->id} declined by customer " . Auth::id());

            return back()->with('info', 'Quotation declined.');
        }

        return back();
    }

    // ==========================================
    // STAFF / ARTIST METHODS
    // ==========================================

    public function staffIndex(Request $request)
    {
        $artist = $this->getStaffArtist();
        if (!$artist) {
            return redirect()->route('staff.dashboard')->with('error', 'No artist profile associated with this account.');
        }

        $query = Inquiry::where('artist_id', $artist->id)
            ->with(['user', 'latestQuotation']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inquiries = $query->latest()->paginate(15);
        $pendingCount = Inquiry::where('artist_id', $artist->id)->where('status', 'pending')->count();
        $quotedCount = Inquiry::where('artist_id', $artist->id)->where('status', 'quoted')->count();
        $acceptedCount = Inquiry::where('artist_id', $artist->id)->where('status', 'accepted')->count();

        return view('staff.inquiries.index', compact('artist', 'inquiries', 'pendingCount', 'quotedCount', 'acceptedCount'));
    }

    public function staffShow($id)
    {
        $artist = $this->getStaffArtist();
        if (!$artist) {
            return redirect()->route('staff.dashboard')->with('error', 'No artist profile associated with this account.');
        }

        $inquiry = Inquiry::where('artist_id', $artist->id)
            ->where('id', $id)
            ->with(['user', 'messages.user', 'quotations.items'])
            ->firstOrFail();

        return view('staff.inquiries.show', compact('artist', 'inquiry'));
    }

    public function staffSendMessage(Request $request, $id)
    {
        $artist = $this->getStaffArtist();
        if (!$artist) {
            return back()->with('error', 'Unauthorized.');
        }

        $inquiry = Inquiry::where('artist_id', $artist->id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('inquiries/attachments', 'public');
        }

        $msg = InquiryMessage::create([
            'inquiry_id' => $inquiry->id,
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachment' => $attachmentPath,
        ]);

        // Notify client
        InAppNotification::send(
            $inquiry->user_id,
            'New Message from ' . $artist->name,
            $artist->name . ': ' . \Illuminate\Support\Str::limit($request->message, 60),
            route('customer.inquiries.show', $inquiry->id) . '#message-' . $msg->id,
            'info',
            $msg->id
        );

        try {
            broadcast(new InquiryEvent(
                $inquiry->id,
                $artist->id,
                $inquiry->user_id,
                'message_sent',
                'New Message',
                $artist->name . ': ' . \Illuminate\Support\Str::limit($request->message, 50)
            ))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Message sent to client.');
    }

    public function staffStoreQuotation(Request $request, $id)
    {
        $artist = $this->getStaffArtist();
        if (!$artist) {
            return back()->with('error', 'Unauthorized.');
        }

        $inquiry = Inquiry::where('artist_id', $artist->id)
            ->where('id', $id)
            ->firstOrFail();

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'estimated_duration' => 'nullable|string|max:100', // e.g. "2 hours", "3 sessions"
            'validity_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalAmount += ($item['quantity'] * $item['unit_price']);
        }

        $quotation = Quotation::create([
            'inquiry_id' => $inquiry->id,
            'artist_id' => $artist->id,
            'user_id' => $inquiry->user_id,
            'total_amount' => $totalAmount,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'validity_date' => $validated['validity_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        // Update inquiry status to quoted
        $inquiry->status = 'quoted';
        $inquiry->save();

        // Notify client
        InAppNotification::send(
            $inquiry->user_id,
            'Quotation Received! 📋',
            $artist->name . ' sent a quotation of ₱' . number_format($totalAmount, 2) . ' for your inquiry.',
            route('customer.inquiries.show', $inquiry->id) . '#quotation-' . $quotation->id,
            'quote'
        );

        try {
            broadcast(new QuotationEvent(
                $quotation->id,
                $inquiry->id,
                $artist->id,
                $inquiry->user_id,
                'created',
                'Quotation Ready',
                $artist->name . ' sent a quotation for ₱' . number_format($totalAmount, 2)
            ))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        Log::info("Quotation #{$quotation->id} created by artist {$artist->id} for inquiry #{$inquiry->id}");

        return back()->with('success', 'Quotation sent successfully to client.');
    }

    // ==========================================
    // ADMIN METHODS
    // ==========================================

    public function adminIndex(Request $request)
    {
        $query = Inquiry::with(['user', 'artist', 'latestQuotation']);

        if ($request->filled('artist_id')) {
            $query->where('artist_id', $request->artist_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $inquiries = $query->latest()->paginate(20);
        $artists = Artist::orderBy('name')->get();
        $totalInquiries = Inquiry::count();
        $pendingCount = Inquiry::where('status', 'pending')->count();
        $quotedCount = Inquiry::where('status', 'quoted')->count();
        $acceptedCount = Inquiry::where('status', 'accepted')->count();

        return view('admin.inquiries.index', compact('inquiries', 'artists', 'totalInquiries', 'pendingCount', 'quotedCount', 'acceptedCount'));
    }

    public function adminShow($id)
    {
        $inquiry = Inquiry::where('id', $id)
            ->with(['user', 'artist', 'messages.user', 'quotations.items'])
            ->firstOrFail();

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function adminSendMessage(Request $request, $id)
    {
        $inquiry = Inquiry::findOrFail($id);

        $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('inquiries/attachments', 'public');
        }

        $msg = InquiryMessage::create([
            'inquiry_id' => $inquiry->id,
            'user_id' => Auth::id(),
            'message' => '[Admin] ' . $request->message,
            'attachment' => $attachmentPath,
        ]);

        InAppNotification::send(
            $inquiry->user_id,
            'Message from Studio Admin',
            '[Admin]: ' . \Illuminate\Support\Str::limit($request->message, 60),
            route('customer.inquiries.show', $inquiry->id) . '#message-' . $msg->id,
            'info',
            $msg->id
        );

        return back()->with('success', 'Admin message sent to inquiry thread.');
    }

    public function adminStoreQuotation(Request $request, $id)
    {
        $inquiry = Inquiry::with('artist')->findOrFail($id);

        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'estimated_duration' => 'nullable|string|max:100',
            'validity_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $totalAmount = 0;
        foreach ($validated['items'] as $item) {
            $totalAmount += ($item['quantity'] * $item['unit_price']);
        }

        $quotation = Quotation::create([
            'inquiry_id' => $inquiry->id,
            'artist_id' => $inquiry->artist_id,
            'user_id' => $inquiry->user_id,
            'total_amount' => $totalAmount,
            'estimated_duration' => $validated['estimated_duration'] ?? null,
            'validity_date' => $validated['validity_date'] ?? null,
            'notes' => ($validated['notes'] ? $validated['notes'] . ' ' : '') . '(Created by Admin)',
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            QuotationItem::create([
                'quotation_id' => $quotation->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $inquiry->status = 'quoted';
        $inquiry->save();

        InAppNotification::send(
            $inquiry->user_id,
            'Quotation Received! 📋',
            'Studio sent a quotation of ₱' . number_format($totalAmount, 2) . ' for your inquiry.',
            route('customer.inquiries.show', $inquiry->id) . '#quotation-' . $quotation->id,
            'quote'
        );

        Log::info("Admin created Quotation #{$quotation->id} for inquiry #{$inquiry->id}");

        return back()->with('success', 'Quotation sent to client on behalf of artist.');
    }

    // ==========================================
    // NOTIFICATIONS API
    // ==========================================

    public function getNotifications()
    {
        $notifications = InAppNotification::where('user_id', Auth::id())
            ->latest()
            ->take(15)
            ->get();

        $unreadCount = InAppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function markNotificationRead($id)
    {
        $notif = InAppNotification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notif->is_read = true;
        $notif->save();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        InAppNotification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
