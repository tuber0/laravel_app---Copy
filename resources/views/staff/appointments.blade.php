<?php
require_once resource_path('views/components/staff-layout.php');
$page = 'appointments';
render_staff_layout_start($page, 'My Schedule & Queue');
?>

<div class="dashboard-container p-2 p-md-4">
    @if(!$artist)
    <div class="alert alert-warning border-warning bg-dark-subtle text-warning">
        <strong>Profile Not Linked!</strong> We couldn't find an Artist profile matching your account.
    </div>
    @else
    
    <!-- Top Action & Live Availability Header -->
    <div class="row align-items-center mb-4 g-3">
        <div class="col-lg-6">
            <h1 class="page-title mb-0" style="font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 2.3rem;">Schedule & Live Queue</h1>
            <p class="text-muted small mb-0">Control your real-time availability, manage the walk-in queue, and monitor booked sessions.</p>
        </div>
        <div class="col-lg-6 text-lg-end d-flex flex-wrap gap-2 justify-content-lg-end align-items-center">
            <!-- Live Status Dropdown / Pill -->
            <div class="dropdown">
                <button class="btn btn-dark border border-secondary rounded-pill px-3 py-2 dropdown-toggle d-flex align-items-center gap-2" type="button" id="liveStatusDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <span id="statusIndicatorDot" class="status-pulse-dot {{ $artist->live_status ?? 'available' }}"></span>
                    <span class="small fw-bold text-uppercase" id="statusLabelText" style="letter-spacing: 1px;">
                        STATUS: {{ strtoupper(str_replace('_', ' ', $artist->live_status ?? 'available')) }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow-lg" aria-labelledby="liveStatusDropdown" style="background:#181818; border-color:#333;">
                    <li><button class="dropdown-item py-2 d-flex align-items-center gap-2" onclick="setLiveStatus('available')"><span class="status-pulse-dot available"></span> Available (Ready)</button></li>
                    <li><button class="dropdown-item py-2 d-flex align-items-center gap-2" onclick="setLiveStatus('busy')"><span class="status-pulse-dot busy"></span> Busy (In Service)</button></li>
                    <li><button class="dropdown-item py-2 d-flex align-items-center gap-2" onclick="setLiveStatus('on_break')"><span class="status-pulse-dot on_break"></span> On Break</button></li>
                    <li><button class="dropdown-item py-2 d-flex align-items-center gap-2" onclick="setLiveStatus('off')"><span class="status-pulse-dot off"></span> Off Duty (Not Accepting)</button></li>
                </ul>
            </div>

            <!-- Working Hours Modal Trigger -->
            <button type="button" class="btn btn-outline-secondary text-white rounded-pill px-3 py-2 small fw-bold" data-bs-toggle="modal" data-bs-target="#workingHoursModal">
                Working Hours
            </button>

            <!-- Add Walkin to Queue Modal Trigger -->
            <button type="button" class="btn btn-gold rounded-pill px-3 py-2 small fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addWalkinModal">
                + Add Walk-In
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show bg-dark-subtle border-success text-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show bg-dark-subtle border-danger text-danger" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- LIVE QUEUE DASHBOARD (Phase 2) -->
    <div class="row g-4 mb-5">
        <!-- Currently In Chair / Serving -->
        <div class="col-lg-5">
            <h5 class="section-title mb-3" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--primary); font-size: 0.85rem; font-weight: 800;">
                Currently In Chair
            </h5>
            <div id="inChairCard" class="glass-card p-4 h-100" style="border-radius: 20px; border: 1px solid rgba(212, 175, 55, 0.3); background: rgba(25, 25, 25, 0.8);">
                @php
                    $inServiceTicket = $activeQueue->firstWhere('status', 'in_service');
                @endphp
                @if($inServiceTicket)
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge bg-gold text-dark fw-bold rounded-pill px-3 py-1 mb-2" style="font-size:0.75rem;">
                            {{ $inServiceTicket->queue_number }}
                        </span>
                        <h4 class="fw-bold mb-0 text-white">{{ $inServiceTicket->client_name }}</h4>
                        <div class="text-muted small">{{ $inServiceTicket->phone_number ?: 'Walk-in Guest' }}</div>
                    </div>
                    <span class="badge badge-inprogress rounded-pill px-3 py-1" style="font-size: 0.7rem;">IN SERVICE</span>
                </div>
                <div class="p-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border);">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Service:</span>
                        <strong class="text-white">{{ $inServiceTicket->service->name ?? 'General Service' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Estimated Duration:</span>
                        <span class="text-white">{{ $inServiceTicket->service->duration ?? '30 min' }}</span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Started At:</span>
                        <span class="text-white">{{ $inServiceTicket->service_started_at ? $inServiceTicket->service_started_at->format('g:i A') : 'Just now' }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success flex-fill py-2 fw-bold rounded-pill" onclick="triggerQueueAction({{ $inServiceTicket->id }}, 'complete')">
                        Complete Service
                    </button>
                    <a href="{{ route('admin.pos') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small text-muted-foreground">
                        POS
                    </a>
                </div>
                @else
                <div class="text-center py-5">
                    <div style="font-size: 2.5rem; opacity: 0.3;">💺</div>
                    <h6 class="fw-bold text-white mt-2">Chair is Empty</h6>
                    <p class="small text-muted-foreground mb-4">No client is currently being served. Call the next customer waiting in line below.</p>
                    @php
                        $nextWaiting = $activeQueue->firstWhere('status', 'waiting');
                    @endphp
                    @if($nextWaiting)
                    <button class="btn btn-gold rounded-pill px-4 py-2 fw-bold" onclick="triggerQueueAction({{ $nextWaiting->id }}, 'start')">
                        Start Next: {{ $nextWaiting->client_name }} ({{ $nextWaiting->queue_number }})
                    </button>
                    @else
                    <button class="btn btn-outline-secondary rounded-pill px-4 py-2 small text-white" data-bs-toggle="modal" data-bs-target="#addWalkinModal">
                        + Add Walk-In Client
                    </button>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Waiting Queue List -->
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="section-title mb-0" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.85rem; font-weight: 800;">
                    Waiting Line (<span id="waitingCountBadge">{{ $activeQueue->where('status', 'waiting')->count() }}</span> Waiting)
                </h5>
                <span class="small text-muted">
                    Est. Total Wait: <strong class="text-white" id="totalWaitBadge">~{{ $artist->estimated_wait_minutes }} min</strong>
                </span>
            </div>

            <div class="glass-card shadow-sm overflow-hidden" style="border-radius: 20px; border: 1px solid var(--border); min-height: 250px;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr style="background: rgba(255,255,255,0.02);">
                                <th class="ps-4 py-3 text-muted-foreground small">POS / TICKET</th>
                                <th class="py-3 text-muted-foreground small">CLIENT & SERVICE</th>
                                <th class="py-3 text-muted-foreground small">EST. WAIT</th>
                                <th class="pe-4 py-3 text-muted-foreground small text-end">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody id="queueTableBody">
                            @php
                                $waitingList = $activeQueue->where('status', 'waiting');
                            @endphp
                            @forelse($waitingList as $ticket)
                            <tr id="ticketRow_{{ $ticket->id }}">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-secondary text-white rounded-pill px-2 py-1" style="font-size:0.65rem;">
                                            #{{ $ticket->position }}
                                        </span>
                                        <span class="fw-bold text-white">{{ $ticket->queue_number }}</span>
                                    </div>
                                    @if($ticket->called_at)
                                    <div class="text-warning small" style="font-size:0.7rem;">Called {{ $ticket->called_at->format('g:i A') }}</div>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold text-white">{{ $ticket->client_name }}</div>
                                    <div class="small text-muted">{{ $ticket->service->name ?? 'Service' }} ({{ $ticket->service->duration ?? '30m' }})</div>
                                </td>
                                <td class="py-3">
                                    <span class="text-white small fw-bold">~{{ $ticket->computeEstimatedWait() }} min</span>
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning btn-sm" title="Call Client" onclick="triggerQueueAction({{ $ticket->id }}, 'call')">
                                            Call
                                        </button>
                                        <button class="btn btn-gold btn-sm fw-bold px-3" title="Start Service" onclick="triggerQueueAction({{ $ticket->id }}, 'start')">
                                            Start
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" title="Mark No Show" onclick="triggerQueueAction({{ $ticket->id }}, 'skip')">
                                            Skip
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr id="emptyQueueRow">
                                <td colspan="4" class="py-5 text-center text-muted-foreground small">
                                    No customers currently waiting in your queue.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CALENDAR & TODAY'S SESSIONS (Existing Section Preserved) -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <h5 class="section-title mb-4" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.8rem; font-weight: 800;">My Appointment Calendar</h5>
            <div class="calendar-card p-4">
                <div id='calendar'></div>
            </div>
        </div>
        <div class="col-lg-4">
            <h5 class="section-title mb-4" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.8rem; font-weight: 800;">Today's Scheduled Sessions</h5>
            <div class="glass-card p-3 h-100" style="border-radius: 20px;">
                @forelse($todayAppointments as $apt)
                <div class="p-3 mb-3 border border-secondary rounded-4 bg-dark-subtle">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-bold" style="color: var(--primary); font-size: 0.9rem;">{{ $apt->service->name }}</div>
                            <div class="small fw-bold">{{ $apt->client_name }}</div>
                            <div class="small text-muted-foreground">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</div>
                        </div>
                        <span class="badge {{ $apt->status === 'in-progress' ? 'badge-inprogress' : 'bg-dark border border-secondary text-muted-foreground' }} rounded-pill" style="font-size: 0.6rem;">
                            {{ strtoupper($apt->status) }}
                        </span>
                    </div>
                    <div class="mt-3 d-flex gap-2">
                        @if($apt->status === 'confirmed')
                        <button class="btn btn-sm btn-gold flex-fill py-2 fw-bold" style="font-size: 0.7rem" onclick="updateAppointmentStatus({{ $apt->id }}, 'in-progress')">START SESSION</button>
                        @elseif($apt->status === 'in-progress')
                        <button class="btn btn-sm btn-success flex-fill py-2 fw-bold" style="font-size: 0.7rem" onclick="updateAppointmentStatus({{ $apt->id }}, 'completed')">FINISH SESSION</button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <div style="font-size: 2rem; opacity: 0.3;">☕</div>
                    <p class="small text-muted-foreground mt-2">No more scheduled appointments for today.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- SCHEDULE REQUESTS (Leave / Time-Off) SECTION -->
    <div class="row mt-4 g-4">
        <div class="col-lg-5">
            <h5 class="section-title mb-4" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.8rem; font-weight: 800;">Request Leave / Schedule Block</h5>
            <div class="glass-card p-4" style="border-radius: 20px;">
                <form action="{{ route('staff.schedule-request.submit') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="small fw-bold text-muted-foreground text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Request Type</label>
                        <select name="type" class="form-select bg-dark text-white border-secondary rounded-pill px-4 py-2" required>
                            <option value="close">Close (Day Off / Block Slot)</option>
                            <option value="open">Open (Exception / Extra Hours)</option>
                        </select>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <label class="small fw-bold text-muted-foreground text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Date</label>
                            <input type="date" name="date" class="form-control bg-dark text-white border-secondary rounded-pill px-4 py-2" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted-foreground text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Time (Optional)</label>
                            <input type="time" name="time" class="form-control bg-dark text-white border-secondary rounded-pill px-4 py-2">
                            <div class="small text-muted mt-1" style="font-size: 0.7rem;">Leave blank for all day off.</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="small fw-bold text-muted-foreground text-uppercase mb-2 d-block" style="letter-spacing: 1px;">Reason</label>
                        <textarea name="reason" rows="3" class="form-control bg-dark text-white border-secondary px-4 py-2" style="border-radius: 15px;" placeholder="Optional: reason for leave."></textarea>
                    </div>

                    <button type="submit" class="btn btn-gold w-100 rounded-pill py-2 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.8rem;">Submit Leave Request</button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <h5 class="section-title mb-4" style="font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.8rem; font-weight: 800;">My Leave History</h5>
            <div class="glass-card shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 text-muted-foreground small">DATE/TIME</th>
                                <th class="py-3 text-muted-foreground small">TYPE</th>
                                <th class="py-3 text-muted-foreground small">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($scheduleRequests as $req)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold" style="color: var(--foreground);">{{ \Carbon\Carbon::parse($req->date)->format('M d, Y') }}</div>
                                    <div class="small text-muted-foreground">{{ $req->time ? \Carbon\Carbon::parse($req->time)->format('h:i A') : 'ALL DAY' }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="badge {{ $req->type == 'close' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3 py-1" style="font-size: 0.65rem;">
                                        {{ strtoupper($req->type) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    @php
                                        $statusClass = [
                                            'pending' => 'bg-warning-subtle text-warning',
                                            'approved' => 'badge-inprogress',
                                            'rejected' => 'bg-danger-subtle text-danger'
                                        ][$req->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-1" style="font-size: 0.65rem;">
                                        {{ strtoupper($req->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="py-5 text-center text-muted-foreground small">No requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL 1: Weekly Working Hours -->
    <div class="modal fade" id="workingHoursModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark text-white border-secondary" style="background:#141414; border-radius:20px;">
                <form action="{{ route('staff.working-hours.update') }}" method="POST">
                    @csrf
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold" style="color: var(--primary);">Weekly Working Hours</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-4">Set your standard working hours for each day of the week. Customers cannot book appointments during your off-days or outside these hours.</p>
                        @php
                            $days = [
                                'monday' => 'Monday (Shop Closed by default)',
                                'tuesday' => 'Tuesday',
                                'wednesday' => 'Wednesday',
                                'thursday' => 'Thursday',
                                'friday' => 'Friday',
                                'saturday' => 'Saturday',
                                'sunday' => 'Sunday'
                            ];
                            $currentSchedule = $artist->working_hours ?? [];
                        @endphp
                        <div class="table-responsive">
                            <table class="table table-dark table-borderless align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small">
                                        <th>DAY</th>
                                        <th>WORKING?</th>
                                        <th>START TIME</th>
                                        <th>END TIME</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($days as $key => $label)
                                    @php
                                        $dayConfig = $currentSchedule[$key] ?? ['is_working' => ($key !== 'monday'), 'start' => '09:00', 'end' => '18:00'];
                                    @endphp
                                    <tr class="border-bottom border-secondary">
                                        <td class="fw-bold">{{ $label }}</td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="working_hours[{{ $key }}][is_working]" value="1" {{ !empty($dayConfig['is_working']) ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="time" name="working_hours[{{ $key }}][start]" class="form-control form-control-sm bg-dark text-white border-secondary rounded-pill" value="{{ $dayConfig['start'] ?? '09:00' }}">
                                        </td>
                                        <td>
                                            <input type="time" name="working_hours[{{ $key }}][end]" class="form-control form-control-sm bg-dark text-white border-secondary rounded-pill" value="{{ $dayConfig['end'] ?? '18:00' }}">
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-gold rounded-pill px-4 fw-bold">Save Working Hours</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL 2: Add Walk-in to Queue -->
    <div class="modal fade" id="addWalkinModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-secondary" style="background:#141414; border-radius:20px;">
                <form id="addWalkinForm" onsubmit="submitWalkinQueue(event)">
                    @csrf
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title fw-bold" style="color: var(--primary);">Add Walk-In to My Queue</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Client Name *</label>
                            <input type="text" name="client_name" class="form-control bg-dark text-white border-secondary rounded-pill px-3 py-2" required placeholder="e.g. John Doe">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Phone Number (Optional)</label>
                            <input type="text" name="phone_number" class="form-control bg-dark text-white border-secondary rounded-pill px-3 py-2" placeholder="e.g. 09171234567">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Service *</label>
                            <select name="service_id" class="form-select bg-dark text-white border-secondary rounded-pill px-3 py-2" required>
                                @foreach($services as $srv)
                                <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->duration ?? '30m' }} - ₱{{ number_format($srv->price, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">Notes (Optional)</label>
                            <textarea name="notes" class="form-control bg-dark text-white border-secondary rounded-3 px-3 py-2" rows="2" placeholder="Any preferences or specific instructions..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="addWalkinSubmitBtn" class="btn btn-gold rounded-pill px-4 fw-bold">Add to Queue</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        .calendar-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; min-height: 600px; padding: 1.5rem; }
        .btn-gold { background: var(--primary); color: #000; border: none; transition: 0.3s; }
        .btn-gold:hover { background: #e5c060; transform: translateY(-2px); }
        .bg-gold { background: var(--primary) !important; }
        .text-gold { color: var(--primary) !important; }

        /* Status pulsing dot */
        .status-pulse-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        .status-pulse-dot.available {
            background-color: #28a745;
            box-shadow: 0 0 8px rgba(40, 167, 69, 0.8);
        }
        .status-pulse-dot.busy {
            background-color: #dc3545;
            box-shadow: 0 0 8px rgba(220, 53, 69, 0.8);
        }
        .status-pulse-dot.on_break {
            background-color: #ffc107;
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.8);
        }
        .status-pulse-dot.off {
            background-color: #6c757d;
        }
        
        /* FullCalendar Overrides */
        .fc-header-toolbar { margin-bottom: 2rem !important; }
        .fc-toolbar-title { font-family: var(--font-montserrat); font-weight: 800; color: var(--primary) !important; font-size: 1.5rem !important; text-transform: uppercase; }
        .fc-button-primary { background-color: var(--card) !important; border: 1px solid var(--border) !important; color: var(--foreground) !important; text-transform: uppercase; letter-spacing: 1px; font-weight: 800; font-size: 0.8rem !important; padding: 0.6rem 1.2rem !important; }
        .fc-button-primary:hover { background-color: var(--muted) !important; color: var(--primary) !important; border-color: var(--primary) !important; }
        .fc-button-active { background-color: var(--primary) !important; border-color: var(--primary) !important; color: #000000 !important; }
        .fc td, .fc th { border: 1px solid var(--border) !important; }
        .fc-col-header-cell { padding: 1.25rem 0 !important; background: var(--primary) !important; }
        .fc-col-header-cell-cushion { color: #000000 !important; font-size: 0.95rem; text-transform: uppercase; font-weight: 800; text-decoration: none !important; }
        .fc-daygrid-day-number { color: var(--foreground) !important; font-size: 1rem; font-weight: 700; padding: 12px !important; text-decoration: none !important; }
        .fc-day-today { background: rgba(212, 175, 55, 0.05) !important; border-top: 4px solid var(--primary) !important; }
        .fc-event { border: 1px solid var(--primary) !important; background-color: rgba(212, 175, 55, 0.15) !important; padding: 5px 8px !important; border-radius: 6px !important; font-size: 0.8rem !important; font-weight: 700; color: var(--foreground) !important; }
    </style>

    <script>
        // Calendar initialization
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            if (calendarEl) {
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: window.innerWidth < 768 ? 'timeGridDay' : 'dayGridMonth',
                    themeSystem: 'standard',
                    height: 'auto',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    events: '{{ route("staff.appointments.calendar") }}'
                });
                calendar.render();
            }

            // Real-time listener via Echo / Reverb (fallback to polling every 8s)
            initRealtime();
        });

        // 1. Live Status Toggle
        async function setLiveStatus(status) {
            try {
                const res = await fetch('{{ route("staff.live-status.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status })
                });
                const data = await res.json();
                if (data.success) {
                    updateStatusBadgeUI(data.status);
                } else {
                    alert(data.message || 'Error updating status');
                }
            } catch (e) {
                console.error(e);
            }
        }

        function updateStatusBadgeUI(status) {
            const dot = document.getElementById('statusIndicatorDot');
            const label = document.getElementById('statusLabelText');
            if (dot && label) {
                dot.className = 'status-pulse-dot ' + status;
                label.innerText = 'STATUS: ' + status.replace('_', ' ').toUpperCase();
            }
        }

        // 2. Queue Actions (call, start, complete, skip, cancel)
        async function triggerQueueAction(id, action) {
            const confirmMsg = action === 'complete' ? 'Mark this service completed?' :
                              action === 'skip' ? 'Skip customer and mark as No-Show?' : null;
            if (confirmMsg && !confirm(confirmMsg)) return;

            try {
                const res = await fetch(`/staff/queue/${id}/action`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ action })
                });
                const data = await res.json();
                if (data.success) {
                    fetchQueueData();
                } else {
                    alert(data.message || 'Error updating queue');
                }
            } catch (e) {
                console.error(e);
            }
        }

        // 3. Add Walkin
        async function submitWalkinQueue(e) {
            e.preventDefault();
            const form = document.getElementById('addWalkinForm');
            const formData = new FormData(form);
            const btn = document.getElementById('addWalkinSubmitBtn');
            btn.disabled = true;

            try {
                const res = await fetch('{{ route("staff.queue.walkin") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    form.reset();
                    const modalEl = document.getElementById('addWalkinModal');
                    bootstrap.Modal.getInstance(modalEl).hide();
                    fetchQueueData();
                } else {
                    alert(data.message || 'Error adding walk-in');
                }
            } catch (err) {
                console.error(err);
                alert('Submission failed');
            } finally {
                btn.disabled = false;
            }
        }

        // 4. Fetch Queue Data / Real-time updater
        async function fetchQueueData() {
            try {
                const res = await fetch('{{ route("staff.queue.data") }}');
                const data = await res.json();
                if (data.success) {
                    renderQueueUI(data);
                }
            } catch (e) {
                console.error(e);
            }
        }

        function renderQueueUI(data) {
            // Update artist status & badges
            if (data.artist) {
                updateStatusBadgeUI(data.artist.live_status);
                const totalWait = document.getElementById('totalWaitBadge');
                if (totalWait) totalWait.innerText = `~${data.artist.estimated_wait_minutes} min`;
            }

            const inChairCard = document.getElementById('inChairCard');
            const tbody = document.getElementById('queueTableBody');
            const waitingCountBadge = document.getElementById('waitingCountBadge');

            const inService = data.queue.find(t => t.status === 'in_service');
            const waitingList = data.queue.filter(t => t.status === 'waiting');

            if (waitingCountBadge) waitingCountBadge.innerText = waitingList.length;

            // Render In-Chair Card
            if (inChairCard) {
                if (inService) {
                    inChairCard.innerHTML = `
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge bg-gold text-dark fw-bold rounded-pill px-3 py-1 mb-2" style="font-size:0.75rem;">
                                    ${inService.queue_number}
                                </span>
                                <h4 class="fw-bold mb-0 text-white">${inService.client_name}</h4>
                                <div class="text-muted small">${inService.phone_number || 'Walk-in Guest'}</div>
                            </div>
                            <span class="badge badge-inprogress rounded-pill px-3 py-1" style="font-size: 0.7rem;">IN SERVICE</span>
                        </div>
                        <div class="p-3 rounded-3 mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border);">
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Service:</span>
                                <strong class="text-white">${inService.service_name}</strong>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-1">
                                <span>Estimated Duration:</span>
                                <span class="text-white">${inService.service_duration} min</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted small">
                                <span>Started At:</span>
                                <span class="text-white">${inService.service_started_at || 'Just now'}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-success flex-fill py-2 fw-bold rounded-pill" onclick="triggerQueueAction(${inService.id}, 'complete')">
                                Complete Service
                            </button>
                            <a href="{{ route('admin.pos') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 small text-muted-foreground">
                                POS
                            </a>
                        </div>
                    `;
                } else {
                    const next = waitingList[0];
                    inChairCard.innerHTML = `
                        <div class="text-center py-5">
                            <div style="font-size: 2.5rem; opacity: 0.3;">💺</div>
                            <h6 class="fw-bold text-white mt-2">Chair is Empty</h6>
                            <p class="small text-muted-foreground mb-4">No client is currently being served. Call the next customer waiting in line below.</p>
                            ${next ? `
                                <button class="btn btn-gold rounded-pill px-4 py-2 fw-bold" onclick="triggerQueueAction(${next.id}, 'start')">
                                    Start Next: ${next.client_name} (${next.queue_number})
                                </button>
                            ` : `
                                <button class="btn btn-outline-secondary rounded-pill px-4 py-2 small text-white" data-bs-toggle="modal" data-bs-target="#addWalkinModal">
                                    + Add Walk-In Client
                                </button>
                            `}
                        </div>
                    `;
                }
            }

            // Render Waiting Table
            if (tbody) {
                if (waitingList.length === 0) {
                    tbody.innerHTML = `
                        <tr id="emptyQueueRow">
                            <td colspan="4" class="py-5 text-center text-muted-foreground small">
                                No customers currently waiting in your queue.
                            </td>
                        </tr>
                    `;
                } else {
                    tbody.innerHTML = waitingList.map(ticket => `
                        <tr id="ticketRow_${ticket.id}">
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary text-white rounded-pill px-2 py-1" style="font-size:0.65rem;">
                                        #${ticket.position}
                                    </span>
                                    <span class="fw-bold text-white">${ticket.queue_number}</span>
                                </div>
                                ${ticket.called_at ? `<div class="text-warning small" style="font-size:0.7rem;">Called ${ticket.called_at}</div>` : ''}
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-white">${ticket.client_name}</div>
                                <div class="small text-muted">${ticket.service_name} (${ticket.service_duration}m)</div>
                            </td>
                            <td class="py-3">
                                <span class="text-white small fw-bold">~${ticket.estimated_wait_minutes} min</span>
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-warning btn-sm" title="Call Client" onclick="triggerQueueAction(${ticket.id}, 'call')">
                                        Call
                                    </button>
                                    <button class="btn btn-gold btn-sm fw-bold px-3" title="Start Service" onclick="triggerQueueAction(${ticket.id}, 'start')">
                                        Start
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" title="Mark No Show" onclick="triggerQueueAction(${ticket.id}, 'skip')">
                                        Skip
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `).join('');
                }
            }
        }

        // Reverb / Echo Listener with Polling Fallback
        function initRealtime() {
            if (window.Echo) {
                window.Echo.channel('artist-queue.{{ $artist->id }}')
                    .listen('.QueueUpdated', () => fetchQueueData())
                    .listen('QueueUpdated', () => fetchQueueData());
                
                window.Echo.channel('artist-status')
                    .listen('.ArtistStatusUpdated', (e) => {
                        if (e.artistId == {{ $artist->id }}) updateStatusBadgeUI(e.liveStatus);
                    });
            }
            // Auto-refresh fallback every 8 seconds
            setInterval(fetchQueueData, 8000);
        }

        async function updateAppointmentStatus(id, status) {
            if(!confirm(`Mark this appointment as ${status.replace('-', ' ')}?`)) return;
            try {
                const response = await fetch(`/staff/appointments/${id}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
    @endif
</div>

<?php render_staff_layout_end(); ?>
