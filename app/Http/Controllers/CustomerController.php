<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Artist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Review;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function home()
    {
        $user = Auth::user();
        $activeAppointment = Appointment::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'in-progress'])
            ->with(['service', 'artist'])
            ->orderBy('created_at', 'desc')
            ->first();

        $upcomingCount = Appointment::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'in-progress'])
            ->count();
            
        $totalCount = Appointment::where('user_id', $user->id)->count();
            
        $popularServices = Service::take(3)->get();
        
        // Fetch portfolio images for each category to show in hover
        $categoryImages = Artist::all()->groupBy('category')->map(function($artists) {
            return $artists->pluck('portfolio_images')->flatten()->filter()->take(5)->values();
        });
            
        return view('customer.home', compact('user', 'activeAppointment', 'upcomingCount', 'totalCount', 'popularServices', 'categoryImages'));
    }

    public function book(Request $request)
    {
        $artist_id = $request->query('artist_id');
        $services = Service::all();
        $artists = Artist::with(['activeQueue.service'])->get();
        $selectedArtist = $artist_id ? Artist::with(['activeQueue.service'])->find($artist_id) : null;
        $userActiveTicket = Auth::check() ? \App\Models\QueueEntry::where('user_id', Auth::id())->today()->active()->with(['artist', 'service'])->first() : null;
        return view('customer.book', compact('services', 'artists', 'selectedArtist', 'userActiveTicket'));
    }

    public function appointments()
    {
        $user = Auth::user();
        
        $activeTicket = \App\Models\QueueEntry::where('user_id', $user->id)
            ->today()
            ->active()
            ->with(['service', 'artist'])
            ->first();

        $upcoming = Appointment::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'in-progress'])
            ->with(['service', 'artist'])
            ->orderBy('appointment_date', 'asc')
            ->get();
            
        $history = Appointment::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'cancelled'])
            ->with(['service', 'artist'])
            ->orderBy('appointment_date', 'desc')
            ->get();

        return view('customer.appointments', compact('upcoming', 'history', 'activeTicket'));
    }


    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'artist_id' => 'required|exists:artists,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
            'total_price' => 'required|numeric',
        ]);

        $appointmentTimeFormatted = Carbon::parse($validated['appointment_time'])->format('H:i');

        $appointmentService = app(\App\Services\AppointmentService::class);
        if (!$appointmentService->isArtistAvailable($validated['artist_id'], $validated['appointment_date'], $appointmentTimeFormatted)) {
            return response()->json([
                'success' => false,
                'message' => 'The selected artist is not available at this time. Please choose another time slot.'
            ], 400);
        }

        $appointment = Appointment::create([
            'user_id' => Auth::id(),
            'client_name' => Auth::user()->name,
            'phone_number' => 'N/A', 
            'service_id' => $validated['service_id'],
            'artist_id' => $validated['artist_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $appointmentTimeFormatted,
            'total_price' => $validated['total_price'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment booked successfully!',
            'redirect' => route('customer.appointments')
        ]);
    }

    public function getAvailableSlots(Request $request)
    {
        $artistId = $request->query('artist_id');
        $date = $request->query('date');
        
        if (!$date) {
            return response()->json([]);
        }

        $dateCarbon = Carbon::parse($date);
        $dayOfWeek = $dateCarbon->dayOfWeek; 
        
        // Monday (1) - Closed
        if ($dayOfWeek == 1) {
            return response()->json([
                'slots' => [],
                'message' => 'The shop is closed on Mondays. Please select another date.'
            ]);
        }

        // Check for leave/blocked schedule
        if ($artistId) {
            $isBlockedFullDay = \App\Models\ScheduleRequest::where('artist_id', $artistId)
                ->where('date', $date)
                ->where('status', 'approved')
                ->where('type', 'close')
                ->whereNull('time')
                ->exists();

            if ($isBlockedFullDay) {
                // Find next available date
                $nextDate = $dateCarbon->copy();
                $found = false;
                for ($i = 1; $i <= 30; $i++) {
                    $nextDate->addDay();
                    if ($nextDate->dayOfWeek == 1) continue; // Skip shop closed days
                    
                    $isStillBlocked = \App\Models\ScheduleRequest::where('artist_id', $artistId)
                        ->where('date', $nextDate->format('Y-m-d'))
                        ->where('status', 'approved')
                        ->where('type', 'close')
                        ->whereNull('time')
                        ->exists();
                    
                    if (!$isStillBlocked) {
                        $found = true;
                        break;
                    }
                }

                $returnMsg = $found 
                    ? "No available slots for this date. The artist is on leave and will be back on " . $nextDate->format('M d, Y') . "."
                    : "No available slots for this date. The artist is currently on leave.";

                return response()->json([
                    'slots' => [],
                    'message' => $returnMsg
                ]);
            }
        }

        // Sunday (0) - 1:00 PM to 8:00 PM
        // Tue-Sat (2-6) - 10:00 AM to 8:00 PM
        $startHour = ($dayOfWeek == 0) ? 13 : 10;
        $endHour = 20;

        $allSlots = [];
        for ($i = $startHour; $i <= $endHour; $i++) {
            $allSlots[] = sprintf('%02d:00', $i);
        }

        $appointmentService = app(\App\Services\AppointmentService::class);
        $availableSlots = [];
        foreach ($allSlots as $time) {
            // Check if slot is in the past (if booking for today)
            $slotDateTime = Carbon::parse("$date $time");
            if ($slotDateTime->isPast()) {
                continue;
            }

            if ($appointmentService->isArtistAvailable($artistId, $date, $time)) {
                $availableSlots[] = [
                    'time' => $time,
                    'label' => Carbon::parse($time)->format('g:i A')
                ];
            }
        }
        
        return response()->json($availableSlots);
    }

    public function artists(Request $request)
    {
        $category = $request->query('category');
        
        $artists = Artist::when($category && $category !== 'All', function($query) use ($category) {
            $query->where('category', $category);
        })->get();

        $categories = Artist::distinct()->pluck('category');

        return view('customer.artists', compact('artists', 'categories', 'category'));
    }

    public function artistPortfolio($id)
    {
        $artist = Artist::with(['reviews.user', 'portfolioItems'])->findOrFail($id);
        return view('customer.artist-portfolio', compact('artist'));
    }

    public function services()
    {
        $services = \App\Models\Service::all();
        $categories = \App\Models\Service::distinct()->pluck('category');
        return view('customer.services', compact('services', 'categories'));
    }

    public function settings()
    {
        $user = Auth::user();
        return view('customer.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old picture if exists
            if ($user->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
            }

            // Explicitly store in profiles directory
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profiles', $filename, 'public');
            
            $user->profile_picture = $path;
        }

        $user->name = $validated['name'];
        if ($user->email !== $validated['email']) {
            $user->email = $validated['email'];
            $user->email_verified_at = null;
            $user->sendEmailVerificationNotification();
        }
        $user->phone = $validated['phone'];
        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.'])->withInput();
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        return back()->with('success', 'Password updated successfully!');
    }

    public function rescheduleAppointment(Request $request, $id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required',
        ]);

        $appointmentTimeFormatted = Carbon::parse($validated['appointment_time'])->format('H:i');

        $appointmentService = app(\App\Services\AppointmentService::class);
        if (!$appointmentService->isArtistAvailable($appointment->artist_id, $validated['appointment_date'], $appointmentTimeFormatted, $appointment->id)) {
            return response()->json([
                'success' => false,
                'message' => 'The selected artist is not available at this time. Please choose another time slot.'
            ], 400);
        }

        $appointment->update([
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $appointmentTimeFormatted,
            'status' => 'pending' // Reset to pending for re-approval
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment rescheduled successfully!'
        ]);
    }

    public function cancelAppointment($id)
    {
        $appointment = Appointment::where('user_id', Auth::id())->findOrFail($id);
        
        $appointment->update(['status' => 'cancelled_by_customer']);

        return response()->json([
            'success' => true,
            'message' => 'Appointment cancelled successfully!'
        ]);
    }

    public function submitReview(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'artist_id' => 'required|exists:artists,id',
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this appointment
        $existing = Review::where('appointment_id', $validated['appointment_id'])->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this session.'
            ], 400);
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'artist_id' => $validated['artist_id'],
            'appointment_id' => $validated['appointment_id'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        // Update Artist average rating
        $artist = Artist::find($validated['artist_id']);
        $avg = Review::where('artist_id', $artist->id)->avg('rating');
        $artist->update(['rating' => $avg]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully! Thank you for your feedback.'
        ]);
    }

    public function joinQueue(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $artist = Artist::findOrFail($validated['artist_id']);

        if ($artist->live_status === 'off') {
            return response()->json([
                'success' => false,
                'message' => $artist->name . ' is currently off-duty and not accepting queue entries.'
            ], 422);
        }

        // Check if customer already has an active ticket with this artist today
        $existing = \App\Models\QueueEntry::where('user_id', $user->id)
            ->where('artist_id', $artist->id)
            ->today()
            ->active()
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'You already hold a live queue ticket (#'.$existing->queue_number.') for '.$artist->name.'.',
                'ticket' => $existing->load(['artist', 'service']),
                'already_queued' => true
            ]);
        }

        $today = now()->toDateString();
        $dailyCount = \App\Models\QueueEntry::where('artist_id', $artist->id)
            ->where('queue_date', $today)
            ->count();
        $queueNumber = 'Q-' . str_pad($dailyCount + 1, 3, '0', STR_PAD_LEFT);

        $ticket = \App\Models\QueueEntry::create([
            'artist_id' => $artist->id,
            'service_id' => $validated['service_id'],
            'user_id' => $user->id,
            'client_name' => $user->name,
            'phone_number' => $user->phone ?? 'N/A',
            'queue_date' => $today,
            'queue_number' => $queueNumber,
            'status' => 'waiting',
            'notes' => $validated['notes'] ?? null,
        ]);

        $ticket->estimated_wait_minutes = $ticket->computeEstimatedWait();
        $ticket->save();

        try {
            broadcast(new \App\Events\QueueUpdated(
                $artist->id,
                'joined',
                $ticket,
                $artist->current_queue_length,
                $artist->estimated_wait_minutes
            ))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined ' . $artist->name . '\'s live queue! Your number is #' . $queueNumber . '.',
            'ticket' => $ticket->load(['artist', 'service']),
            'redirect' => route('customer.appointments')
        ]);
    }

    public function cancelQueue(Request $request, $id)
    {
        $ticket = \App\Models\QueueEntry::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $ticket->status = 'cancelled';
        $ticket->save();

        $artist = $ticket->artist;
        if ($artist) {
            try {
                broadcast(new \App\Events\QueueUpdated(
                    $artist->id,
                    'cancelled',
                    $ticket,
                    $artist->current_queue_length,
                    $artist->estimated_wait_minutes
                ))->toOthers();
            } catch (\Throwable $e) {
                \Log::warning('Broadcast failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your queue ticket #' . $ticket->queue_number . ' has been cancelled.'
        ]);
    }

    public function getQueueTicket(Request $request)
    {
        $ticket = \App\Models\QueueEntry::where('user_id', Auth::id())
            ->today()
            ->active()
            ->with(['artist', 'service'])
            ->first();

        if (!$ticket) {
            return response()->json(['success' => false, 'ticket' => null]);
        }

        return response()->json([
            'success' => true,
            'ticket' => [
                'id' => $ticket->id,
                'queue_number' => $ticket->queue_number,
                'artist_name' => $ticket->artist ? $ticket->artist->name : '',
                'artist_id' => $ticket->artist_id,
                'artist_status' => $ticket->artist ? $ticket->artist->live_status : 'available',
                'service_name' => $ticket->service ? $ticket->service->name : '',
                'status' => $ticket->status,
                'position' => $ticket->position,
                'estimated_wait_minutes' => $ticket->computeEstimatedWait(),
            ]
        ]);
    }

    public function getArtistQueueInfo($id)
    {
        $artist = Artist::findOrFail($id);
        return response()->json([
            'success' => true,
            'artist_id' => $artist->id,
            'artist_name' => $artist->name,
            'live_status' => $artist->live_status,
            'queue_length' => $artist->current_queue_length,
            'estimated_wait_minutes' => $artist->estimated_wait_minutes,
        ]);
    }
}

