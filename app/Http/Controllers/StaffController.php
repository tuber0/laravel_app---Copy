<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artist;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    private function getArtist()
    {
        return Auth::user()->artist ?? Artist::where('email', Auth::user()->email)->first();
    }

    public function dashboard()
    {
        $artist = $this->getArtist();
        
        $todayAppointments = [];
        $appointments = [];
        if ($artist) {
            $todayAppointments = Appointment::with(['service'])
                ->where('artist_id', $artist->id)
                ->where('appointment_date', now()->toDateString())
                ->whereIn('status', ['confirmed', 'in-progress'])
                ->orderBy('appointment_time')
                ->get();

            $appointments = Appointment::with(['service', 'user'])
                ->where('artist_id', $artist->id)
                ->whereIn('status', ['pending', 'confirmed', 'in-progress', 'completed'])
                ->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'desc')
                ->get();
        }

        return view('staff.dashboard', compact('artist', 'todayAppointments', 'appointments'));
    }

    public function appointments(Request $request)
    {
        $artist = $this->getArtist();
        $todayAppointments = [];
        $scheduleRequests = collect();
        $activeQueue = collect();
        $services = \App\Models\Service::orderBy('name')->get();
        
        if ($artist) {
            $todayAppointments = Appointment::with(['service', 'user'])
                ->where('artist_id', $artist->id)
                ->where('appointment_date', now()->toDateString())
                ->whereIn('status', ['confirmed', 'in-progress'])
                ->orderBy('appointment_time')
                ->get();

            $scheduleRequests = \App\Models\ScheduleRequest::where('artist_id', $artist->id)
                ->orderBy('date', 'desc')
                ->get();

            $activeQueue = \App\Models\QueueEntry::with(['service', 'user'])
                ->where('artist_id', $artist->id)
                ->today()
                ->active()
                ->orderBy('id')
                ->get();
        }

        return view('staff.appointments', compact('artist', 'todayAppointments', 'scheduleRequests', 'activeQueue', 'services'));
    }

    public function updateLiveStatus(Request $request)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return response()->json(['success' => false, 'message' => 'Artist profile not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:available,busy,on_break,off'
        ]);

        $artist->live_status = $validated['status'];
        $artist->save();

        try {
            broadcast(new \App\Events\ArtistStatusUpdated(
                $artist->id,
                $artist->name,
                $artist->live_status,
                $artist->current_queue_length,
                $artist->estimated_wait_minutes
            ))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'status' => $artist->live_status,
            'message' => 'Status changed to ' . ucfirst(str_replace('_', ' ', $artist->live_status))
        ]);
    }

    public function updateWorkingHours(Request $request)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return back()->with('error', 'Artist profile not found.');
        }

        $hours = $request->input('working_hours', []);
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $sanitized = [];

        foreach ($days as $day) {
            $isWorking = isset($hours[$day]['is_working']) && in_array($hours[$day]['is_working'], [1, '1', 'true', true], true);
            $sanitized[$day] = [
                'is_working' => $isWorking,
                'start' => $hours[$day]['start'] ?? '09:00',
                'end' => $hours[$day]['end'] ?? '18:00',
            ];
        }

        $artist->working_hours = $sanitized;
        $artist->save();

        return back()->with('success', 'Weekly working hours updated successfully.');
    }

    public function updateQueueAction(Request $request, $id)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return response()->json(['success' => false, 'message' => 'Artist not found'], 404);
        }

        $ticket = \App\Models\QueueEntry::where('artist_id', $artist->id)->findOrFail($id);
        $action = $request->input('action');

        if ($action === 'call') {
            $ticket->called_at = now();
        } elseif ($action === 'start') {
            $ticket->status = 'in_service';
            $ticket->service_started_at = now();
            $artist->live_status = 'busy';
            $artist->save();
        } elseif ($action === 'complete') {
            $ticket->status = 'completed';
            $ticket->completed_at = now();
            $remaining = \App\Models\QueueEntry::where('artist_id', $artist->id)
                ->today()
                ->where('status', 'in_service')
                ->where('id', '!=', $ticket->id)
                ->exists();
            if (!$remaining) {
                $artist->live_status = 'available';
                $artist->save();
            }
        } elseif ($action === 'skip') {
            $ticket->status = 'no_show';
        } elseif ($action === 'cancel') {
            $ticket->status = 'cancelled';
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid action'], 400);
        }

        $ticket->save();

        try {
            broadcast(new \App\Events\QueueUpdated(
                $artist->id,
                $action,
                $ticket,
                $artist->current_queue_length,
                $artist->estimated_wait_minutes
            ))->toOthers();

            broadcast(new \App\Events\ArtistStatusUpdated(
                $artist->id,
                $artist->name,
                $artist->live_status,
                $artist->current_queue_length,
                $artist->estimated_wait_minutes
            ))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Queue updated: ' . ucfirst($action),
            'ticket' => $ticket
        ]);
    }

    public function addQueueWalkin(Request $request)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return response()->json(['success' => false, 'message' => 'Artist not found'], 404);
        }

        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'service_id' => 'required|exists:services,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $today = now()->toDateString();
        $dailyCount = \App\Models\QueueEntry::where('artist_id', $artist->id)
            ->where('queue_date', $today)
            ->count();
        $queueNumber = 'Q-' . str_pad($dailyCount + 1, 3, '0', STR_PAD_LEFT);

        $ticket = \App\Models\QueueEntry::create([
            'artist_id' => $artist->id,
            'service_id' => $validated['service_id'],
            'user_id' => null,
            'client_name' => $validated['client_name'],
            'phone_number' => $validated['phone_number'] ?? null,
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
            'message' => 'Walk-in added to queue #' . $queueNumber,
            'ticket' => $ticket->load('service')
        ]);
    }

    public function getQueueData(Request $request)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return response()->json(['success' => false, 'message' => 'Artist not found'], 404);
        }

        $activeQueue = \App\Models\QueueEntry::with(['service', 'user'])
            ->where('artist_id', $artist->id)
            ->today()
            ->active()
            ->orderBy('id')
            ->get()
            ->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'queue_number' => $ticket->queue_number,
                    'client_name' => $ticket->client_name,
                    'phone_number' => $ticket->phone_number,
                    'service_name' => $ticket->service ? $ticket->service->name : 'General Service',
                    'service_duration' => $ticket->service ? $ticket->service->duration_minutes : 30,
                    'status' => $ticket->status,
                    'position' => $ticket->position,
                    'estimated_wait_minutes' => $ticket->computeEstimatedWait(),
                    'called_at' => $ticket->called_at ? $ticket->called_at->format('g:i A') : null,
                    'service_started_at' => $ticket->service_started_at ? $ticket->service_started_at->format('g:i A') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'artist' => [
                'id' => $artist->id,
                'name' => $artist->name,
                'live_status' => $artist->live_status,
                'queue_length' => $activeQueue->count(),
                'estimated_wait_minutes' => $artist->estimated_wait_minutes,
            ],
            'queue' => $activeQueue
        ]);
    }


    public function calendar()
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return response()->json([]);
        }

        // 1. Fetch Appointments
        $appointments = Appointment::where('artist_id', $artist->id)
            ->whereIn('status', ['confirmed', 'in-progress'])
            ->with(['service'])
            ->get()
            ->map(function ($apt) {
                return [
                    'id'    => 'apt_' . $apt->id,
                    'title' => $apt->service->name . ' - ' . $apt->client_name,
                    'start' => $apt->appointment_date . 'T' . $apt->appointment_time,
                    'color' => '#D4AF37'
                ];
            });

        // 2. Fetch Schedule Requests (Approved and Pending)
        $requests = \App\Models\ScheduleRequest::where('artist_id', $artist->id)
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->map(function ($req) {
                $prefix = ($req->status === 'pending') ? 'PENDING: ' : '';
                $typeLabel = ($req->type === 'close') ? 'OFF' : 'OPEN';
                $timePart = $req->time ? 'T' . $req->time : '';
                
                return [
                    'id'    => 'req_' . $req->id,
                    'title' => $prefix . $typeLabel . ' (' . ($req->reason ?: 'No reason') . ')',
                    'start' => $req->date . $timePart,
                    'allDay' => empty($req->time),
                    'color' => ($req->type === 'close') ? '#dc3545' : '#28a745',
                    'textColor' => '#ffffff'
                ];
            });

        $events = $appointments->concat($requests);

        return response()->json($events);
    }

    public function submitScheduleRequest(Request $request)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return back()->with('error', 'Artist profile not found.');
        }

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'nullable', // Null means all day
            'type' => 'required|in:open,close',
            'reason' => 'nullable|string|max:500'
        ]);

        \App\Models\ScheduleRequest::create([
            'artist_id' => $artist->id,
            'date' => $validated['date'],
            'time' => $validated['time'] ?: null,
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'status' => 'pending'
        ]);

        return back()->with('success', 'Your schedule change request has been submitted for administrative approval.');
    }

    public function portfolio()
    {
        $artist = $this->getArtist();
        if ($artist) {
            $artist->load('portfolioItems');
        }
        return view('staff.portfolio', compact('artist'));
    }

    public function updatePortfolio(Request $request)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return back()->with('error', 'No professional Artist profile is linked to your account. Please contact your Administrator.');
        }

        // Lock in the relationship if it was found via email fallback
        if (!$artist->user_id) {
            $artist->user_id = auth()->id();
            $artist->save();
        }

        $validated = $request->validate([
            'bio' => 'nullable|string',
            'specialty' => 'nullable|string|max:255',
            'years_of_experience' => 'nullable|integer|min:0|max:80',
            'skills' => 'nullable|string',
            'certifications' => 'nullable|string',
            'languages' => 'nullable|string',
            'starting_rate' => 'nullable|numeric|min:0',
            'social_instagram' => 'nullable|string|max:255',
            'social_facebook' => 'nullable|string|max:255',
            'preferred_styles' => 'nullable|string',
            'primary_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480', // 20MB Max
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
            'gallery_titles.*' => 'nullable|string|max:255',
            'gallery_captions.*' => 'nullable|string|max:500',
            'remove_gallery' => 'nullable|array',
            'delete_portfolio_items' => 'nullable|array',
        ]);

        $specialtyArray = [];
        if (!empty($validated['specialty'])) {
            $specs = explode(',', $validated['specialty']);
            foreach ($specs as $s) {
                if (trim($s) !== '') {
                    $specialtyArray[] = trim($s);
                }
            }
        }

        $parseTags = function($input) {
            if (empty($input)) return [];
            if (is_array($input)) return $input;
            return array_values(array_filter(array_map('trim', explode(',', $input))));
        };

        // Handle Primary Image Upload
        if ($request->hasFile('primary_image')) {
            if ($artist->image_url && !\Illuminate\Support\Str::startsWith($artist->image_url, 'http')) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($artist->image_url)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($artist->image_url);
                }
            }
            $file = $request->file('primary_image');
            $filename = time() . '_primary_' . $file->getClientOriginalName();
            $path = $file->storeAs('artists_portfolio', $filename, 'public');
            $artist->image_url = $path;
            $artist->profile_photo = $path;
        }

        // Handle Deleting Existing PortfolioItems
        if (!empty($validated['delete_portfolio_items'])) {
            $itemsToDelete = \App\Models\PortfolioItem::where('artist_id', $artist->id)
                ->whereIn('id', $validated['delete_portfolio_items'])
                ->get();
            foreach ($itemsToDelete as $pItem) {
                if ($pItem->image_path && !\Illuminate\Support\Str::startsWith($pItem->image_path, 'http')) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($pItem->image_path)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($pItem->image_path);
                    }
                }
                $pItem->delete();
            }
        }

        // Handle Legacy Gallery Image Uploads and Removals
        $portfolioArray = is_array($artist->portfolio_images) ? $artist->portfolio_images : [];

        // 1. Remove requested images
        if (!empty($validated['remove_gallery'])) {
            foreach ($validated['remove_gallery'] as $toRemove) {
                if (!\Illuminate\Support\Str::startsWith($toRemove, 'http')) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($toRemove)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($toRemove);
                    }
                }
                $portfolioArray = array_values(array_filter($portfolioArray, function($img) use ($toRemove) {
                    return $img !== $toRemove;
                }));
            }
        }

        // 2. Add newly uploaded files to both portfolio_items table and portfolioArray
        if ($request->hasFile('gallery_images')) {
            $galleryTitles = $request->input('gallery_titles', []);
            $galleryCaptions = $request->input('gallery_captions', []);
            $currentMaxOrder = (int) \App\Models\PortfolioItem::where('artist_id', $artist->id)->max('sort_order');

            foreach ($request->file('gallery_images') as $idx => $galleryFile) {
                $filename = time() . '_' . uniqid() . '_' . $galleryFile->getClientOriginalName();
                $path = $galleryFile->storeAs('artists_portfolio', $filename, 'public');
                $portfolioArray[] = $path;

                \App\Models\PortfolioItem::create([
                    'artist_id' => $artist->id,
                    'staff_artist_id' => $artist->id,
                    'image_path' => $path,
                    'title' => $galleryTitles[$idx] ?? ($artist->name . ' Work #' . ($idx + 1)),
                    'category' => $artist->category,
                    'caption' => $galleryCaptions[$idx] ?? null,
                    'sort_order' => $currentMaxOrder + $idx + 1,
                ]);
            }
        }

        $artist->bio = $validated['bio'] ?? $artist->bio;
        $artist->specialty = $specialtyArray;
        $artist->specialties = $specialtyArray;
        $artist->years_of_experience = $validated['years_of_experience'] ?? $artist->years_of_experience;
        $artist->skills = $parseTags($validated['skills'] ?? $artist->skills);
        $artist->certifications = $parseTags($validated['certifications'] ?? $artist->certifications);
        $artist->languages = $parseTags($validated['languages'] ?? $artist->languages);
        $artist->preferred_styles = $parseTags($validated['preferred_styles'] ?? $artist->preferred_styles);
        $artist->starting_rate = $validated['starting_rate'] ?? $artist->starting_rate;
        $artist->social_links = [
            'instagram' => $validated['social_instagram'] ?? ($artist->social_links['instagram'] ?? ''),
            'facebook' => $validated['social_facebook'] ?? ($artist->social_links['facebook'] ?? ''),
        ];
        $artist->portfolio_images = $portfolioArray;
        $artist->save();

        return back()->with('success', 'Your professional portfolio and gallery have been updated successfully!');
    }

    public function settings()
    {
        $user = auth()->user();
        return view('staff.settings', compact('user'));
    }

    public function updateSettings(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->profile_picture)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile_picture);
            }
            $file = $request->file('profile_picture');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('profiles', $filename, 'public');
            $user->profile_picture = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? $user->phone;
        $user->save();

        return back()->with('success', 'Staff profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'The provided password does not match your current password.'])->withInput();
        }

        $user->password = \Illuminate\Support\Facades\Hash::make($validated['new_password']);
        $user->save();

        return back()->with('success', 'Staff password updated securely!');
    }

    public function updateStatus(Request $request, $id)
    {
        $artist = $this->getArtist();
        if (!$artist) {
            return response()->json(['success' => false, 'message' => 'Artist profile not found'], 403);
        }

        $appointment = Appointment::where('artist_id', $artist->id)->findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:confirmed,in-progress,completed'
        ]);

        $appointment->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated to ' . $validated['status']
        ]);
    }
}
