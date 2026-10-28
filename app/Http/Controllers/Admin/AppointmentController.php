<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Appointment;
use App\Services\AppointmentService;

class AppointmentController extends Controller
{
    protected $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    public function index(Request $request)
    {
        $sortBy = $request->query('sort', 'appointment_date');
        $order = $request->query('order', 'desc');
        
        $appointments = $this->appointmentService->getAppointments(null, null, $sortBy, $order);
        $artists = \App\Models\Artist::with(['activeQueue.service'])->orderBy('name')->get();
        $services = \App\Models\Service::orderBy('name')->get();
        $todayQueues = \App\Models\QueueEntry::with(['artist', 'service', 'user'])
            ->today()
            ->active()
            ->orderBy('id')
            ->get();

        return view('admin.appointments', compact('appointments', 'artists', 'services', 'todayQueues'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'artist_id' => 'required|exists:artists,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'notes' => 'nullable|string',
        ]);

        $service = \App\Models\Service::findOrFail($validated['service_id']);
        
        // Check availability
        if (!$this->appointmentService->isArtistAvailable($validated['artist_id'], $validated['appointment_date'], $validated['appointment_time'])) {
            return back()->with('error', 'The selected artist is not available at this time.');
        }

        Appointment::create([
            'client_name' => $validated['client_name'],
            'phone_number' => $validated['phone_number'],
            'artist_id' => $validated['artist_id'],
            'service_id' => $validated['service_id'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'total_price' => $service->price,
            'status' => 'confirmed', // Admin walk-ins are automatically confirmed
            'notes' => $validated['notes'],
        ]);

        return back()->with('success', 'Walk-in appointment recorded successfully!');
    }

    public function validateBooking(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $status = $request->input('status'); // approved, rejected

        if ($status == 'approved') {
            // Re-check availability before official approval, excluding self
            if (!$this->appointmentService->isArtistAvailable($appointment->artist_id, $appointment->appointment_date, $appointment->appointment_time, $appointment->id)) {
                return back()->with('error', 'Conflicts found! This slot is no longer available.');
            }
            $appointment->status = 'confirmed';
        } else {
            $appointment->status = 'cancelled';
        }

        $appointment->save();
        return back()->with('success', 'Appointment has been ' . ($status == 'approved' ? 'approved' : 'rejected') . '.');
    }

    public function calendar()
    {
        // 1. Fetch Appointments
        $appointments = Appointment::whereIn('status', ['confirmed', 'in-progress'])
            ->with(['artist', 'service'])
            ->get()
            ->map(function ($apt) {
                return [
                    'id'    => 'apt_' . $apt->id,
                    'title' => ($apt->artist->name ?? 'Artist') . ' - ' . $apt->service->name,
                    'start' => $apt->appointment_date . 'T' . $apt->appointment_time,
                    'color' => '#28a745'
                ];
            });

        // 2. Fetch Schedule Requests (Approved and Pending)
        $requests = \App\Models\ScheduleRequest::with('artist')
            ->whereIn('status', ['approved', 'pending'])
            ->get()
            ->map(function ($req) {
                $prefix = ($req->status === 'pending') ? 'PENDING: ' : '';
                $artistName = $req->artist->name ?? 'Artist';
                $typeLabel = ($req->type === 'close') ? 'OFF' : 'OPEN';
                $timePart = $req->time ? 'T' . $req->time : '';
                
                return [
                    'id'    => 'req_' . $req->id,
                    'title' => $prefix . $artistName . ' [' . $typeLabel . ']',
                    'start' => $req->date . $timePart,
                    'allDay' => empty($req->time),
                    'color' => ($req->type === 'close') ? '#dc3545' : '#28a745',
                    'textColor' => '#ffffff'
                ];
            });

        $events = $appointments->concat($requests);

        return response()->json($events);
    }

    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return back()->with('success', 'Appointment record has been deleted permanently.');
    }

    public function complete($id)
    {
        $appointment = Appointment::with('service')->findOrFail($id);
        
        if ($appointment->status === 'completed') {
            return back()->with('error', 'History shows this appointment is already marked as completed.');
        }

        $appointment->status = 'completed';
        $appointment->save();

        // Automatically record to sales history
        \App\Models\Payment::create([
            'appointment_id' => $appointment->id,
            'amount' => $appointment->service->price ?? 0,
            'method' => 'cash', // Default for now
            'status' => 'completed',
            'transaction_id' => 'APT-' . str_pad($appointment->id, 6, '0', STR_PAD_LEFT)
        ]);

        return back()->with('success', 'Appointment marked as complete and recorded in sales history.');
    }

    public function updateArtistStatus(Request $request, $id)
    {
        $artist = \App\Models\Artist::findOrFail($id);
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
            'message' => 'Status for ' . $artist->name . ' updated to ' . ucfirst(str_replace('_', ' ', $artist->live_status)),
            'status' => $artist->live_status
        ]);
    }

    public function updateQueueAction(Request $request, $id)
    {
        $ticket = \App\Models\QueueEntry::with('artist')->findOrFail($id);
        $action = $request->input('action');
        $artist = $ticket->artist;

        if ($action === 'call') {
            $ticket->called_at = now();
        } elseif ($action === 'start') {
            $ticket->status = 'in_service';
            $ticket->service_started_at = now();
            if ($artist) {
                $artist->live_status = 'busy';
                $artist->save();
            }
        } elseif ($action === 'complete') {
            $ticket->status = 'completed';
            $ticket->completed_at = now();
            if ($artist) {
                $remaining = \App\Models\QueueEntry::where('artist_id', $artist->id)
                    ->today()
                    ->where('status', 'in_service')
                    ->where('id', '!=', $ticket->id)
                    ->exists();
                if (!$remaining) {
                    $artist->live_status = 'available';
                    $artist->save();
                }
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
            if ($artist) {
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
            }
        } catch (\Throwable $e) {
            \Log::warning('Broadcast failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Queue ticket ' . $ticket->queue_number . ' updated to ' . ucfirst($action),
            'ticket' => $ticket
        ]);
    }

    public function addQueueWalkin(Request $request)
    {
        $validated = $request->validate([
            'artist_id' => 'required|exists:artists,id',
            'service_id' => 'required|exists:services,id',
            'client_name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500'
        ]);

        $artist = \App\Models\Artist::findOrFail($validated['artist_id']);
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
            'message' => 'Walk-in added to ' . $artist->name . '\'s queue (#' . $queueNumber . ')',
            'ticket' => $ticket->load(['artist', 'service'])
        ]);
    }

    /**
     * Inline status update — called by the AJAX dropdown in the Appointment Records table.
     */
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,in-progress,completed,cancelled',
        ]);

        $newStatus = $validated['status'];

        // If completing, also record payment like the full complete() flow
        if ($newStatus === 'completed' && $appointment->status !== 'completed') {
            $appointment->status = 'completed';
            $appointment->save();

            \App\Models\Payment::firstOrCreate(
                ['appointment_id' => $appointment->id],
                [
                    'amount'         => optional($appointment->service)->price ?? 0,
                    'method'         => 'cash',
                    'status'         => 'completed',
                    'transaction_id' => 'APT-' . str_pad($appointment->id, 6, '0', STR_PAD_LEFT),
                ]
            );
        } else {
            $appointment->status = $newStatus;
            $appointment->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated to ' . ucfirst(str_replace('-', ' ', $newStatus)),
            'status'  => $appointment->status,
        ]);
    }

    public function getQueueData(Request $request)
    {
        $artists = \App\Models\Artist::with(['activeQueue.service', 'activeQueue.user'])
            ->orderBy('name')
            ->get()
            ->map(function ($artist) {
                return [
                    'id' => $artist->id,
                    'name' => $artist->name,
                    'category' => $artist->category,
                    'live_status' => $artist->live_status,
                    'queue_length' => $artist->activeQueue->count(),
                    'estimated_wait_minutes' => $artist->estimated_wait_minutes,
                    'queue' => $artist->activeQueue->map(function ($ticket) {
                        return [
                            'id' => $ticket->id,
                            'queue_number' => $ticket->queue_number,
                            'client_name' => $ticket->client_name,
                            'phone_number' => $ticket->phone_number,
                            'service_name' => $ticket->service ? $ticket->service->name : 'Service',
                            'duration' => $ticket->service ? $ticket->service->duration_minutes : 30,
                            'status' => $ticket->status,
                            'position' => $ticket->position,
                            'called_at' => $ticket->called_at ? $ticket->called_at->format('g:i A') : null,
                            'service_started_at' => $ticket->service_started_at ? $ticket->service_started_at->format('g:i A') : null,
                        ];
                    })
                ];
            });

        return response()->json([
            'success' => true,
            'artists' => $artists
        ]);
    }
}

