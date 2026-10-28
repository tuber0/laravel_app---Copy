<?php
require_once resource_path('views/components/staff-layout.php');
$page = 'dashboard';
render_staff_layout_start($page, 'Staff Dashboard');
?>

<div class="dashboard-container p-2 p-md-4">
    @if(!$artist)
    <div class="alert alert-warning border-warning bg-dark-subtle text-warning">
        <strong>Profile Not Linked!</strong> We couldn't find an Artist profile matching your email address ({{ auth()->user()->email }}). 
        Please contact an Administrator to map your account. You will not be able to manage your schedule or portfolio until this is resolved.
    </div>
    @else
    
    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0" style="font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 2.5rem;">Hello, {{ $artist->name }}</h1>
            <p class="text-muted small mb-0">Here is a quick overview of your activity for today.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="glass-card p-4 h-100" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="background: rgba(255, 255, 255, 0.1); color: #ffffff; border-radius: 12px; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-calendar" style="width:20px; color:#ffffff;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                    </div>
                </div>
                <div class="fw-bold h3 mb-1" style="color: var(--foreground);">{{ count($todayAppointments) }}</div>
                <div class="small fw-bold text-uppercase" style="letter-spacing: 0.5px; color: var(--muted-foreground);">Today's Appointments</div>
            </div>
        </div>

        <!-- Phase 2: Live Queue Status Card -->
        <div class="col-12 col-md-4">
            <div class="glass-card p-4 h-100" style="border-radius: 20px; border-color: rgba(212,175,55,0.25);">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div style="background: rgba(212,175,55,0.1); color: var(--primary); border-radius: 12px; width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
                    </div>
                    @if($artist)
                    <span class="badge rounded-pill px-3 py-1 small fw-bold
                        {{ $artist->live_status === 'available' ? 'bg-success' : ($artist->live_status === 'busy' ? 'bg-danger' : ($artist->live_status === 'on_break' ? 'bg-warning text-dark' : 'bg-secondary')) }}">
                        {{ strtoupper(str_replace('_', ' ', $artist->live_status ?? 'off')) }}
                    </span>
                    @endif
                </div>
                <div class="fw-bold h3 mb-1" style="color: var(--foreground);">
                    {{ $artist ? $artist->current_queue_length : 0 }}
                </div>
                <div class="small fw-bold text-uppercase" style="letter-spacing: 0.5px; color: var(--muted-foreground);">
                    In Queue Today
                    @if($artist && $artist->current_queue_length > 0)
                    <span class="text-gold ms-1">~{{ $artist->estimated_wait_minutes }}m wait</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="glass-card p-4 h-100" style="border-radius: 20px; background: rgba(212, 175, 55, 0.05); border-color: rgba(212, 175, 55, 0.2);">
                <div class="d-flex flex-column justify-content-center h-100">
                    <h5 class="fw-bold mb-2 text-gold">Quick Actions</h5>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('staff.appointments') }}" class="btn btn-gold btn-sm fw-bold rounded-pill">Manage Queue & Schedule</a>
                        <a href="{{ route('staff.portfolio') }}" class="btn btn-outline-gold btn-sm fw-bold rounded-pill">Edit Portfolio →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phase 2: Today's Live Queue Preview -->
    @if($artist && $artist->current_queue_length > 0)
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.8rem; font-weight: 800;">Live Queue — Now</h5>
            <a href="{{ route('staff.appointments') }}" class="btn btn-sm btn-outline-secondary small rounded-pill px-3">Full Queue View →</a>
        </div>
        <div class="glass-card overflow-hidden" style="border-radius: 20px;">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3 text-muted-foreground small">#</th>
                            <th class="py-3 text-muted-foreground small">CLIENT</th>
                            <th class="py-3 text-muted-foreground small">SERVICE</th>
                            <th class="py-3 pe-4 text-end text-muted-foreground small">STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($artist->activeQueue()->with('service')->get() as $ticket)
                        <tr>
                            <td class="ps-4 py-3">
                                <span class="badge bg-gold text-dark fw-bold rounded-pill px-2">{{ $ticket->queue_number }}</span>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold small" style="color:var(--foreground)">{{ $ticket->client_name }}</div>
                                @if($ticket->phone_number)<div class="small text-muted-foreground">{{ $ticket->phone_number }}</div>@endif
                            </td>
                            <td class="py-3">
                                <div class="small fw-bold text-primary">{{ $ticket->service->name ?? 'Service' }}</div>
                            </td>
                            <td class="py-3 pe-4 text-end">
                                @if($ticket->status === 'in_service')
                                <span class="badge badge-inprogress rounded-pill px-3 py-1 small">🟢 In Chair</span>
                                @else
                                <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3 py-1 small">⏳ Waiting</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Appointment History Table -->
    <h5 class="section-title mb-4" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.8rem; font-weight: 800;">Appointment Records</h5>
    <div class="glass-card shadow-sm overflow-hidden" style="border-radius: 20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4 py-3 text-muted-foreground small">CLIENT</th>
                        <th class="py-3 text-muted-foreground small">SERVICE</th>
                        <th class="py-3 text-muted-foreground small">SCHEDULE</th>
                        <th class="py-3 pe-4 text-end text-muted-foreground small">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $apt)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold" style="color: var(--foreground);">{{ $apt->client_name }}</div>
                            <div class="small text-muted-foreground">{{ $apt->phone_number }}</div>
                        </td>
                        <td class="py-3">
                            <div class="small fw-bold text-primary">{{ $apt->service->name }}</div>
                        </td>
                        <td class="py-3">
                            <div class="small" style="color: var(--foreground);">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</div>
                            <div class="small text-muted-foreground">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</div>
                        </td>
                        <td class="py-3 pe-4 text-end">
                            @php
                                $statusClass = [
                                    'confirmed' => 'badge-inprogress',
                                    'completed' => 'badge-completed',
                                    'in-progress' => 'badge-inprogress',
                                    'pending' => 'bg-warning-subtle text-warning'
                                ][$apt->status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-2" style="font-size:0.65rem; letter-spacing: 0.5px">
                                {{ ucfirst($apt->status) }}
                            </span>
                            <div class="mt-2 d-flex justify-content-end gap-1">
                                @if($apt->status === 'confirmed')
                                <button class="btn btn-sm btn-gold py-1 px-2" style="font-size:0.6rem" onclick="updateStatus(<?= $apt->id ?>, 'in-progress')">Start</button>
                                @elseif($apt->status === 'in-progress')
                                <button class="btn btn-sm btn-success py-1 px-2" style="font-size:0.6rem" onclick="updateStatus(<?= $apt->id ?>, 'completed')">Finish</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-5 text-center text-muted-foreground small">No appointment records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @endif
</div>

<script>
async function updateStatus(id, status) {
    if(!confirm(`Mark this appointment as ${status.replace('-', ' ')}?`)) return;
    
    try {
        const response = await fetch(`/staff/appointments/${id}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            },
            body: JSON.stringify({ status })
        });
        
        const data = await response.json();
        if(data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating status');
        }
    } catch (e) {
        alert('An error occurred');
    }
}
</script>
<?php render_staff_layout_end(); ?>
