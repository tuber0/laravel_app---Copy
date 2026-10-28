<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalStaff = User::where('role', 'staff')->count();
        
        $totalRevenueValue = \App\Models\Payment::where('status', 'completed')->sum('amount');
        $ongoingApptsCount = \App\Models\Appointment::whereIn('status', ['confirmed', 'in-progress'])->count();

        $stats = [
            'revenue' => [
                'value' => '₱' . number_format($totalRevenueValue, 2),
                'trend' => 'Real-time',
                'label' => 'Total Revenue'
            ],
            'appointments' => [
                'value' => $ongoingApptsCount,
                'trend' => 'Active',
                'label' => 'Ongoing Appointments'
            ],
            'customers' => [
                'value' => $totalCustomers,
                'trend' => 'Registered',
                'label' => 'Total Customers'
            ],
            'active_staff' => [
                'value' => $totalStaff,
                'trend' => 'Stable',
                'label' => 'Active Staff'
            ]
        ];

        $recentAppointments = \App\Models\Appointment::with(['service', 'artist'])
                                ->latest()
                                ->take(5)
                                ->get();

        // 1. Business Activity (Last 7 Days)
        $dailyActivity = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = now()->subDays($i)->format('D');
            $dailyActivity[] = \App\Models\Appointment::whereDate('created_at', $date)->count();
        }

        // 2. Artist Workload (Resource Allocation)
        $artistWorkload = \App\Models\Appointment::join('artists', 'appointments.artist_id', '=', 'artists.id')
            ->select('artists.name', \DB::raw('count(*) as count'))
            ->groupBy('artists.name')
            ->orderByDesc('count')
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'recentAppointments', 
            'totalUsers',
            'dailyActivity',
            'labels',
            'artistWorkload'
        ));
    }

    public function pos()
    {
        $upcomingAppointments = \App\Models\Appointment::with(['user', 'service'])
            ->where('status', 'confirmed')
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('appointment_date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->get();

        $services_list = \App\Models\Service::orderBy('category')->orderBy('name')->get();
        // Extract unique categories or default to 'All'
        $categories = $services_list->pluck('category')->unique()->sort()->values();
        $categories->prepend('All');

        $artists = \App\Models\Artist::orderBy('name')->get();

        return view('admin.pos', compact('upcomingAppointments', 'services_list', 'categories', 'artists'));
    }

    public function createStaff()
    {
        $categories = \App\Models\Service::distinct()->pluck('category')->filter()->values();
        return view('admin.staff-create', compact('categories'));
    }

    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,staff',
            'category' => 'nullable|string|max:255',
            'specialty' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);
        
        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            \Log::error('Registration Email Failed: ' . $e->getMessage());
            session()->flash('warning', 'The account was created, but the verification email could not be sent. Please ensure your mail server is configured correctly.');
        }

        // Auto-create skeleton Artist profile for easy management
        $artistId = null;
        if ($user->isStaff()) {
            // Check if artist already exists by email
            $artist = \App\Models\Artist::where('email', $user->email)->first();
            
            if ($artist) {
                // Link existing artist
                $artist->update([
                    'user_id' => $user->id,
                    'category' => $request->category ?: $artist->category,
                ]);
            } else {
                // Create new artist
                $artist = \App\Models\Artist::create([
                    'user_id' => $user->id,
                    'name'    => $user->name,
                    'email'   => $user->email,
                    'category' => $request->category,
                    'specialty' => $request->specialty ? [$request->specialty] : null,
                ]);
            }
            $artistId = $artist->id;
        }

        if ($user->isStaff()) {
            return redirect()->route('admin.artists.index', [
                'created_artist_id' => $artistId
            ])->with('success', 'Personnel account and Artist profile created! You can now finish the professional details.');
        }

        return redirect()->route('admin.staff.oversight')->with('success', 'New personnel account created and connected successfully!');
    }

    public function customers()
    {
        $customers = User::where('role', 'customer')->orderBy('created_at', 'desc')->get();
        return view('admin.customers', compact('customers'));
    }

    public function oversight()
    {
        $staffCount = User::where('role', 'staff')->count();
        $adminCount = User::where('role', 'admin')->count();
        $staffMembers = User::with('artist')
                           ->whereIn('role', ['staff', 'admin'])
                           ->orderBy('created_at', 'desc')
                           ->get();
        return view('admin.oversight', compact('staffCount', 'adminCount', 'staffMembers'));
    }
                            
    /**
     * @deprecated The system now automatically links staff to artists via user_id.
     */
    public function linkStaffToArtist(Request $request)
    {
        return back()->with('error', 'The linking feature has been automated. Profiles are now connected by default.');
    }

    public function inventory()
    {
        $payments = \App\Models\Payment::with(['appointment.user', 'appointment.service', 'appointment.artist'])
                                     ->latest()
                                     ->get();
        
        $totalRevenue = $payments->where('status', 'completed')->sum('amount');
        $todayRevenue = $payments->where('status', 'completed')
                                ->where('created_at', '>=', now()->startOfDay())
                                ->sum('amount');
                                
        return view('admin.inventory', compact('payments', 'totalRevenue', 'todayRevenue'));
    }

    public function sendEmail(Request $request)
    {
        $validated = $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'client_name' => 'nullable|string'
        ]);

        try {
            Mail::raw($validated['message'], function ($mail) use ($validated) {
                $mail->to($validated['to'])
                     ->subject($validated['subject']);
            });

            return response()->json([
                'success' => true,
                'message' => 'Email sent successfully to ' . ($validated['client_name'] ?? $validated['to']) . '!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyStaff($id)
    {
        $personnel = User::findOrFail($id);
        
        // Prevent deleting self
        if ($personnel->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own administrative account.');
        }

        $personnel->delete();
        return back()->with('success', 'Personnel account has been removed successfully.');
    }

    public function updateStaffPassword(Request $request, $id)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ]);

        $personnel = User::findOrFail($id);
        $personnel->password = bcrypt($validated['password']);
        $personnel->save();

        return back()->with('success', 'Password for ' . $personnel->name . ' has been updated.');
    }

    public function posCheckout(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric',
            'method' => 'required|string',
            'transaction_id' => 'required|string',
            'appointment_id' => 'nullable',
            'items' => 'nullable|array',
            'artist_id' => 'nullable',
            'client_name' => 'nullable'
        ]);

        try {
            $payment = \App\Models\Payment::create([
                'amount' => $validated['amount'],
                'method' => $validated['method'],
                'status' => 'completed',
                'transaction_id' => $validated['transaction_id'],
                'appointment_id' => $validated['appointment_id'] ?: null,
                'items' => $validated['items'] ?? null
            ]);

            // If it's a walk-in (no appointment_id) but an artist is provided
            if (!$validated['appointment_id'] && $request->artist_id) {
                // Auto-create a completed appointment record
                $appointment = \App\Models\Appointment::create([
                    'client_name' => $request->client_name ?: 'Walk-in Client',
                    'artist_id' => $request->artist_id,
                    'service_id' => $validated['items'][0]['id'] ?? 1,
                    'appointment_date' => now()->toDateString(),
                    'appointment_time' => now()->format('H:i:s'),
                    'status' => 'completed',
                    'total_price' => $validated['amount']
                ]);
                
                // Link the payment to the newly created appointment
                $payment->appointment_id = $appointment->id;
                $payment->save();
            }

            // If it's a linked appointment, mark as completed
            if ($validated['appointment_id']) {
                $appointment = \App\Models\Appointment::find($validated['appointment_id']);
                if ($appointment) {
                    $appointment->status = 'completed';
                    $appointment->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transaction recorded successfully.',
                'type' => $validated['appointment_id'] ? 'BOOKED' : 'WALK-IN'
            ]);
        } catch (\Exception $e) {
            \Log::error('POS Checkout Failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function scheduleRequests()
    {
        $requests = \App\Models\ScheduleRequest::with('artist')
                      ->orderByRaw("FIELD(status, 'pending') DESC")
                      ->orderBy('date', 'asc')
                      ->get();
        return view('admin.schedule-requests', compact('requests'));
    }

    public function updateScheduleRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $scheduleRequest = \App\Models\ScheduleRequest::findOrFail($id);
        $scheduleRequest->status = $validated['status'];
        $scheduleRequest->save();

        return back()->with('success', 'Schedule request for ' . $scheduleRequest->artist->name . ' has been ' . $validated['status'] . '.');
    }

    public function services()
    {
        $services = \App\Models\Service::orderBy('category')->orderBy('name')->get();
        // Get unique categories for filtering
        $categories = $services->pluck('category')->unique()->sort()->values();
        return view('admin.services', compact('services', 'categories'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        \App\Models\Service::create($validated);

        return back()->with('success', 'New service added successfully!');
    }

    public function updateService(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $service = \App\Models\Service::findOrFail($id);
        $service->update($validated);

        return back()->with('success', 'Service updated successfully!');
    }

    public function settings()
    {
        $user = auth()->user();
        return view('admin.settings', compact('user'));
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

        return back()->with('success', 'Admin profile updated successfully!');
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

        return back()->with('success', 'Admin password updated securely!');
    }
}
