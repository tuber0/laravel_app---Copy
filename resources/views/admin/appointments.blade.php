<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'appointments';
render_admin_layout_start($page, 'Appointment Management');
?>

<!-- FullCalendar CDN -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js'></script>

<div class="dashboard-container">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="page-title mb-0">Booking & Queue Control</h1>
            <p class="text-muted small mb-0">Monitor staff live availability, manage walk-in queues, and oversee scheduled appointments in real time.</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <button type="button" class="btn btn-outline-warning px-3 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#queueWalkInModal">
                + Add Walk-In Queue
            </button>
            <button type="button" class="btn btn-gold px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#walkInModal">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:20px; margin-right:6px">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Book Appointment
            </button>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 bg-dark-subtle border-success text-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4 bg-dark-subtle border-danger text-danger" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- 1. STAFF AVAILABILITY & LIVE STATUS BAR (Phase 2) -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">Staff Live Status & Queue Overview</h5>
            <span class="small text-muted">Real-Time Sync Active</span>
        </div>
        <div class="row g-3">
            @foreach($artists as $art)
            <div class="col-md-6 col-xl-4" id="artistCard_{{ $art->id }}">
                <div class="glass-card p-3 d-flex align-items-center justify-content-between border border-secondary" style="border-radius: 16px; background: rgba(20, 20, 20, 0.75);">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $art->profile_photo ? (Str::startsWith($art->profile_photo, 'http') ? $art->profile_photo : asset('storage/' . $art->profile_photo)) : ($art->image_url ? (Str::startsWith($art->image_url, 'http') ? $art->image_url : asset('storage/' . $art->image_url)) : 'https://ui-avatars.com/api/?name=' . urlencode($art->name) . '&background=D4AF37&color=0B0B0B') }}" 
                             alt="{{ $art->name }}" 
                             class="rounded-circle border border-secondary" 
                             style="width: 48px; height: 48px; object-fit: cover;">
                        <div>
                            <div class="fw-bold text-white mb-0" style="font-size: 0.95rem;">{{ $art->name }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $art->category }}</div>
                            <div class="small mt-1 text-muted" style="font-size: 0.75rem;">
                                Queue: <strong class="text-white queue-count-badge" data-artist-id="{{ $art->id }}">{{ $art->activeQueue->count() }}</strong> (est. <span class="queue-wait-badge" data-artist-id="{{ $art->id }}">~{{ $art->estimated_wait_minutes }}m</span>)
                            </div>
                        </div>
                    </div>

                    <!-- Admin Status Selector -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-dark border border-secondary rounded-pill px-3 py-1 dropdown-toggle d-flex align-items-center gap-2 status-btn-{{ $art->id }}" type="button" data-bs-toggle="dropdown" data-bs-strategy="fixed" data-bs-boundary="viewport">
                            <span class="status-pulse-dot {{ $art->live_status ?? 'available' }}"></span>
                            <span class="small fw-bold text-uppercase" style="font-size: 0.7rem;">{{ strtoupper(str_replace('_', ' ', $art->live_status ?? 'available')) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" style="background:#181818; border-color:#333; z-index:9999;">
                            <li><button class="dropdown-item py-2 d-flex align-items-center gap-2 small" onclick="adminSetArtistStatus({{ $art->id }}, 'available')"><span class="status-pulse-dot available"></span> Available</button></li>
                            <li><button class="dropdown-item py-2 d-flex align-items-center gap-2 small" onclick="adminSetArtistStatus({{ $art->id }}, 'busy')"><span class="status-pulse-dot busy"></span> Busy</button></li>
                            <li><button class="dropdown-item py-2 d-flex align-items-center gap-2 small" onclick="adminSetArtistStatus({{ $art->id }}, 'on_break')"><span class="status-pulse-dot on_break"></span> On Break</button></li>
                            <li><button class="dropdown-item py-2 d-flex align-items-center gap-2 small" onclick="adminSetArtistStatus({{ $art->id }}, 'off')"><span class="status-pulse-dot off"></span> Off Duty</button></li>
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- 2. LIVE QUEUE MONITOR BOARD (Phase 2) -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="section-title mb-0">Active Walk-in Queue Board</h5>
            <div class="d-flex align-items-center gap-2">
                <select id="adminQueueArtistFilter" class="form-select form-select-sm bg-dark text-white border-secondary rounded-pill px-3" onchange="renderAdminQueueTable()">
                    <option value="all">All Artists</option>
                    @foreach($artists as $art)
                    <option value="{{ $art->id }}">{{ $art->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="glass-card shadow-sm overflow-hidden" style="border-radius: 20px; border: 1px solid var(--border);">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr style="background: rgba(255,255,255,0.02);">
                            <th class="ps-4 py-3 text-muted-foreground small">TICKET / POS</th>
                            <th class="py-3 text-muted-foreground small">CLIENT & CONTACT</th>
                            <th class="py-3 text-muted-foreground small">ARTIST</th>
                            <th class="py-3 text-muted-foreground small">SERVICE</th>
                            <th class="py-3 text-muted-foreground small">STATUS</th>
                            <th class="py-3 pe-4 text-end text-muted-foreground small">QUEUE ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody id="adminQueueTableBody">
                        @forelse($todayQueues as $q)
                        <tr id="adminQueueRow_{{ $q->id }}" data-artist-id="{{ $q->artist_id }}">
                            <td class="ps-4 py-3">
                                <span class="badge bg-gold text-dark fw-bold rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                    {{ $q->queue_number }}
                                </span>
                                @if($q->called_at)
                                <div class="text-warning small" style="font-size:0.7rem;">Called {{ $q->called_at->format('g:i A') }}</div>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-white">{{ $q->client_name }}</div>
                                <div class="small text-muted">{{ $q->phone_number ?: 'Walk-in' }}</div>
                            </td>
                            <td class="py-3">
                                <span class="badge bg-dark border border-secondary text-white">{{ $q->artist->name ?? 'Artist' }}</span>
                            </td>
                            <td class="py-3">
                                <div class="small fw-bold text-white">{{ $q->service->name ?? 'Service' }}</div>
                                <div class="small text-muted">{{ $q->service->duration ?? '30 min' }}</div>
                            </td>
                            <td class="py-3">
                                @if($q->status === 'in_service')
                                <span class="badge badge-inprogress rounded-pill px-3 py-1">IN SERVICE</span>
                                @else
                                <span class="badge bg-secondary rounded-pill px-3 py-1">WAITING (#{{ $q->position }})</span>
                                @endif
                            </td>
                            <td class="pe-4 py-3 text-end">
                                <div class="btn-group btn-group-sm">
                                    @if($q->status === 'waiting')
                                    <button class="btn btn-outline-warning btn-sm" onclick="adminTriggerQueueAction({{ $q->id }}, 'call')">Call</button>
                                    <button class="btn btn-gold btn-sm fw-bold px-3" onclick="adminTriggerQueueAction({{ $q->id }}, 'start')">Start</button>
                                    <button class="btn btn-outline-danger btn-sm" onclick="adminTriggerQueueAction({{ $q->id }}, 'skip')">No-Show</button>
                                    @elseif($q->status === 'in_service')
                                    <button class="btn btn-success btn-sm fw-bold px-3" onclick="adminTriggerQueueAction({{ $q->id }}, 'complete')">Complete</button>
                                    <a href="{{ route('admin.pos') }}" class="btn btn-outline-secondary btn-sm text-white">POS</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="adminEmptyQueueRow">
                            <td colspan="6" class="py-5 text-center text-muted-foreground small">
                                No active queue entries for today across artists.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. BOOKING REQUESTS & SCHEDULE MONITOR (Preserved) -->
    <div class="row g-5">
        <!-- New Requests Column -->
        <div class="col-lg-5">
            <h5 class="section-title mb-4">Pending Requests</h5>
            <div class="request-scroll-area">
                @forelse($appointments->where('status', 'pending') as $req)
                <div class="request-card mb-3 p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="fw-bold mb-0">{{ $req->client_name }}</h6>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small text-muted">{{ $req->phone_number }}</span>
                                @if($req->user)
                                <span class="text-muted small">|</span>
                                <button onclick="openEmailModal('{{ $req->user->email }}', '{{ addslashes($req->client_name) }}')" class="btn btn-link link-gold p-0 text-decoration-none small fw-bold">Email Client</button>
                                @endif
                            </div>
                        </div>
                        <span class="badge bg-gold-subtle text-gold small px-2 py-1">New Request</span>
                    </div>
                    
                    <div class="request-details mb-4">
                        <div class="d-flex align-items-center gap-2 mb-2 text-white small">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z" /></svg>
                            <span>{{ $req->service->name }} with <b>{{ $req->artist->name ?? 'Any Artist' }}</b></span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted small">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" /></svg>
                            <span>{{ \Carbon\Carbon::parse($req->appointment_date)->format('M d, Y') }} at {{ \Carbon\Carbon::parse($req->appointment_time)->format('h:i A') }}</span>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.appointments.validate', $req->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-gold w-100 btn-sm py-2">Approve</button>
                        </form>
                        <form action="{{ route('admin.appointments.validate', $req->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-outline-danger w-100 btn-sm py-2">Reject</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 opacity-50">
                    <p>No pending booking requests.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Calendar Column -->
        <div class="col-lg-7">
            <h5 class="section-title mb-4">Schedule Monitor</h5>
            <div class="calendar-card p-4">
                <div id='calendar'></div>
            </div>
        </div>
    </div>

    <!-- All Records Table -->
    <div class="row mt-5" id="records">
        <div class="col-12">
            <h5 class="section-title mb-4">Appointment Records</h5>
            <div class="glass-card shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 text-muted-foreground small">CLIENT</th>
                                <th class="py-3 text-muted-foreground small">ARTIST & SERVICE</th>
                                <th class="py-3">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'appointment_date', 'order' => (request('sort') == 'appointment_date' && request('order') == 'asc') ? 'desc' : 'asc']) }}#records" 
                                       class="btn btn-link p-0 text-decoration-none d-flex align-items-center gap-2 fw-bold" 
                                       style="color: var(--primary); font-size: 0.75rem; letter-spacing: 1px;">
                                        SCHEDULE
                                        @if(request('sort', 'appointment_date') == 'appointment_date')
                                            @if(request('order') == 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" style="width:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" style="width:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                            @endif
                                        @endif
                                    </a>
                                </th>
                                <th class="py-3 text-muted-foreground small">STATUS</th>
                                <th class="py-3 pe-4 text-end text-muted-foreground small">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments->where('status', '!=', 'pending') as $apt)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $apt->client_name }}</div>
                                    <div class="small text-muted">{{ $apt->phone_number }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-primary">{{ $apt->service->name ?? 'Service' }}</div>
                                    <div class="small fw-bold" style="color:var(--muted-foreground)">Artist: {{ $apt->artist->name ?? 'Any' }}</div>
                                </td>
                                <td>
                                    <div class="small">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'confirmed'            => 'badge-confirmed-green',
                                            'cancelled'            => 'badge-confirmed',
                                            'cancelled_by_customer'=> 'badge-confirmed',
                                            'completed'            => 'badge-completed',
                                            'in-progress'          => 'badge-inprogress',
                                            'pending'              => 'badge-pending',
                                        ][$apt->status] ?? 'badge-pending';
                                        $statusLabel = [
                                            'cancelled_by_customer'=> 'Cancelled by Customer',
                                            'confirmed'            => 'Confirmed',
                                            'cancelled'            => 'Rejected',
                                            'completed'            => 'Completed',
                                            'in-progress'          => 'In Progress',
                                            'pending'              => 'Pending',
                                        ][$apt->status] ?? ucfirst($apt->status);
                                    @endphp
                                    <!-- Inline status-change dropdown -->
                                    <div class="dropdown">
                                        <button class="badge {{ $statusClass }} rounded-pill border-0 px-3 py-2 dropdown-toggle apt-status-btn-{{ $apt->id }}"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                data-bs-strategy="fixed"
                                                data-bs-boundary="viewport"
                                                style="font-size:0.65rem; letter-spacing:0.5px; cursor:pointer;">
                                            {{ $statusLabel }}
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark shadow" style="background:#181818; border-color:#333; z-index:9999; min-width:170px;">
                                            <li><h6 class="dropdown-header small text-muted text-uppercase">Change Status</h6></li>
                                            <li><button class="dropdown-item small py-2" onclick="adminChangeAptStatus({{ $apt->id }}, 'confirmed')">✅ Confirmed</button></li>
                                            <li><button class="dropdown-item small py-2" onclick="adminChangeAptStatus({{ $apt->id }}, 'in-progress')">🔄 In Progress</button></li>
                                            <li><button class="dropdown-item small py-2" onclick="adminChangeAptStatus({{ $apt->id }}, 'completed')">🏁 Completed</button></li>
                                            <li><button class="dropdown-item small py-2" onclick="adminChangeAptStatus({{ $apt->id }}, 'cancelled')">✖ Rejected / Cancel</button></li>
                                            <li><button class="dropdown-item small py-2" onclick="adminChangeAptStatus({{ $apt->id }}, 'pending')">⏳ Pending</button></li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="pe-4 text-end align-middle">
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                    @if($apt->user)
                                    <button onclick="openEmailModal('{{ $apt->user->email }}', '{{ addslashes($apt->client_name) }}')" class="btn btn-link link-gold p-0 text-decoration-none small fw-bold">Email</button>
                                    @endif
                                    <form action="{{ route('admin.appointments.destroy', $apt->id) }}" method="POST" onsubmit="return confirm('Delete this record permanently? This cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none small fw-bold">Delete</button>
                                    </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Add Walk-in to Live Queue (Phase 2) -->
<div class="modal fade" id="queueWalkInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary" style="background:#141414; border-radius:20px;">
            <form id="adminQueueWalkinForm" onsubmit="adminSubmitQueueWalkin(event)">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold" style="color: var(--primary);">Add Walk-In to Live Queue</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Artist *</label>
                        <select name="artist_id" class="form-select bg-dark text-white border-secondary rounded-pill px-3 py-2" required>
                            @foreach($artists as $art)
                            <option value="{{ $art->id }}">{{ $art->name }} ({{ $art->category }}) - Status: {{ ucfirst(str_replace('_', ' ', $art->live_status ?? 'available')) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Service *</label>
                        <select name="service_id" class="form-select bg-dark text-white border-secondary rounded-pill px-3 py-2" required>
                            @foreach($services as $srv)
                            <option value="{{ $srv->id }}">{{ $srv->name }} ({{ $srv->duration ?? '30m' }} - ₱{{ number_format($srv->price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Client Name *</label>
                        <input type="text" name="client_name" class="form-control bg-dark text-white border-secondary rounded-pill px-3 py-2" required placeholder="e.g. John Doe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control bg-dark text-white border-secondary rounded-pill px-3 py-2" placeholder="e.g. 09171234567">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold">Notes</label>
                        <textarea name="notes" class="form-control bg-dark text-white border-secondary rounded-3 px-3 py-2" rows="2" placeholder="Optional notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="adminWalkinSubmitBtn" class="btn btn-gold rounded-pill px-4 fw-bold">Issue Queue Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Add Walk-in (Scheduled Appointment) -->
<div class="modal fade" id="walkInModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-secondary text-white" style="background: rgba(18, 18, 18, 0.98); border-radius: 20px;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title font-bebas text-primary" style="font-size: 1.5rem; letter-spacing: 1px;">Schedule Walk-in Appointment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.appointments.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Client Name</label>
                        <input type="text" name="client_name" class="form-control bg-transparent border-secondary text-white" required placeholder="Full Name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Contact Number</label>
                        <input type="text" name="phone_number" class="form-control bg-transparent border-secondary text-white" placeholder="09xxxxxxxxx">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Service</label>
                            <select name="service_id" class="form-select bg-transparent border-secondary text-white" required>
                                <option value="" disabled selected>Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" class="bg-dark text-white">{{ $service->name }} (₱{{ number_format($service->price, 2) }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Artist</label>
                            <select name="artist_id" id="walkInArtist" class="form-select bg-transparent border-secondary text-white" required onchange="fetchWalkInSlots()">
                                <option value="" disabled selected>Select Artist</option>
                                @foreach($artists as $artist)
                                    <option value="{{ $artist->id }}" class="bg-dark text-white">{{ $artist->name }} ({{ $artist->category }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Date</label>
                            <input type="date" name="appointment_date" id="walkInDate" class="form-control bg-transparent border-secondary text-white" required min="{{ date('Y-m-d') }}" onchange="fetchWalkInSlots()">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted text-uppercase fw-bold">Time Slot</label>
                            <select name="appointment_time" id="walkInTime" class="form-select bg-transparent border-secondary text-white" required disabled>
                                <option value="" disabled selected>Select Date & Artist First</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold">Notes</label>
                        <textarea name="notes" class="form-control bg-transparent border-secondary text-white" rows="2" placeholder="Optional details..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gold px-5 py-2 fw-bold rounded-pill shadow">Save Appointment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email Modal -->
<div class="modal fade" id="emailClientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-secondary text-white" style="background: rgba(18, 18, 18, 0.98); border-radius: 20px;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title font-bebas text-primary" style="font-size: 1.5rem; letter-spacing: 1px;">Send Notification Email</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="emailClientForm" onsubmit="submitEmailClient(event)">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="to" id="emailToInput">
                    <input type="hidden" name="client_name" id="emailClientNameInput">
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Recipient</label>
                        <input type="text" id="emailDisplayRecipient" class="form-control bg-transparent border-secondary text-white" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted text-uppercase fw-bold">Subject</label>
                        <input type="text" name="subject" class="form-control bg-transparent border-secondary text-white" required value="Update Regarding Your Pedro's Artisan Appointment">
                    </div>
                    <div class="mb-0">
                        <label class="form-label small text-muted text-uppercase fw-bold">Message</label>
                        <textarea name="message" class="form-control bg-transparent border-secondary text-white" rows="4" required placeholder="Write your message to the client here..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-link text-muted text-decoration-none fw-bold" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="sendEmailSubmitBtn" class="btn btn-gold px-4 py-2 fw-bold rounded-pill shadow">Send Email</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .dashboard-container { padding: 3rem; background: var(--background); min-height: 100vh; }
    .page-title { font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 3rem; }
    .section-title { font-family: var(--font-montserrat); text-transform: uppercase; letter-spacing: 2px; color: var(--muted-foreground); font-size: 0.8rem; font-weight: 800; }
    .calendar-card { background: var(--card); border: 1px solid var(--border); border-radius: 20px; min-height: 600px; padding: 1.5rem; }
    .request-card { background: var(--card); border: 1px solid var(--border); border-radius: 15px; }
    .btn-gold { background: var(--primary); color: #000; border: none; transition: 0.3s; }
    .btn-gold:hover { background: #e5c060; transform: translateY(-2px); }
    .bg-gold { background: var(--primary) !important; }
    .bg-gold-subtle { background: rgba(212, 175, 55, 0.1); }
    .text-gold { color: var(--primary) !important; }

    /* Pulsing status dot */
    .status-pulse-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; }
    .status-pulse-dot.available { background-color: #28a745; box-shadow: 0 0 8px rgba(40, 167, 69, 0.8); }
    .status-pulse-dot.busy { background-color: #dc3545; box-shadow: 0 0 8px rgba(220, 53, 69, 0.8); }
    .status-pulse-dot.on_break { background-color: #ffc107; box-shadow: 0 0 8px rgba(255, 193, 7, 0.8); }
    .status-pulse-dot.off { background-color: #6c757d; }

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
    let adminQueuesData = [];

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
                events: '{{ route("admin.appointments.calendar") }}'
            });
            calendar.render();
        }

        // Initialize real-time listeners and polling fallback
        initAdminRealtime();
    });

    // 1. Admin Change Artist Status
    async function adminSetArtistStatus(artistId, status) {
        try {
            const res = await fetch(`/admin/artists/${artistId}/live-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status })
            });
            const data = await res.json();
            if (data.success) {
                adminUpdateArtistBadge(artistId, status);
            } else {
                alert(data.message || 'Error updating status');
            }
        } catch (e) {
            console.error(e);
        }
    }

    function adminUpdateArtistBadge(artistId, status) {
        const btn = document.querySelector(`.status-btn-${artistId}`);
        if (btn) {
            btn.innerHTML = `
                <span class="status-pulse-dot ${status}"></span>
                <span class="small fw-bold text-uppercase" style="font-size: 0.7rem;">${status.replace('_', ' ').toUpperCase()}</span>
            `;
        }
    }

    // 2. Admin Change Appointment Status (inline)
    const APT_STATUS_META = {
        'confirmed':   { label: 'Confirmed',           cls: 'badge-confirmed-green' },
        'in-progress': { label: 'In Progress',          cls: 'badge-inprogress' },
        'completed':   { label: 'Completed',            cls: 'badge-completed' },
        'cancelled':   { label: 'Rejected / Cancelled', cls: 'badge-confirmed' },
        'pending':     { label: 'Pending',              cls: 'badge-pending' },
    };

    async function adminChangeAptStatus(aptId, newStatus) {
        const meta = APT_STATUS_META[newStatus];
        if (!meta) return;
        if (!confirm(`Change appointment status to "${meta.label}"?`)) return;

        try {
            const res = await fetch(`/admin/appointments/${aptId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            });
            const data = await res.json();
            if (data.success) {
                // Update the badge in the DOM without reload
                const btn = document.querySelector(`.apt-status-btn-${aptId}`);
                if (btn) {
                    // Remove all badge-* classes and add the new one
                    btn.className = btn.className
                        .replace(/badge-\S+/g, '')
                        .trim();
                    btn.classList.add(meta.cls);
                    btn.textContent = meta.label;
                    // Re-add dropdown-toggle
                    btn.classList.add('dropdown-toggle');
                }
                // Show a brief success toast
                showAdminToast(`Status updated to ${meta.label}`, 'success');
            } else {
                alert(data.message || 'Error updating status');
            }
        } catch (e) {
            console.error(e);
            alert('Network error. Please try again.');
        }
    }

    // Mini toast helper
    function showAdminToast(msg, type = 'success') {
        const existing = document.getElementById('adminToastEl');
        if (existing) existing.remove();
        const color = type === 'success' ? '#28a745' : '#dc3545';
        const toast = document.createElement('div');
        toast.id = 'adminToastEl';
        toast.style.cssText = `position:fixed;bottom:1.5rem;right:1.5rem;z-index:99999;
            background:#141414;border:1px solid ${color};color:#fff;
            padding:0.75rem 1.25rem;border-radius:12px;font-size:0.85rem;
            box-shadow:0 8px 32px rgba(0,0,0,0.5);transition:opacity 0.4s;`;
        toast.innerHTML = `<span style="color:${color};margin-right:6px;">●</span>${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; setTimeout(() => toast.remove(), 400); }, 2500);
    }

    // 3. Admin Queue Action
    async function adminTriggerQueueAction(id, action) {
        const confirmMsg = action === 'complete' ? 'Mark service completed?' :
                          action === 'skip' ? 'Mark customer as No-Show?' : null;
        if (confirmMsg && !confirm(confirmMsg)) return;

        try {
            const res = await fetch(`/admin/queue/${id}/action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ action })
            });
            const data = await res.json();
            if (data.success) {
                fetchAdminQueueData();
            } else {
                alert(data.message || 'Error');
            }
        } catch (e) {
            console.error(e);
        }
    }

    // 3. Admin Add Walkin Queue
    async function adminSubmitQueueWalkin(e) {
        e.preventDefault();
        const form = document.getElementById('adminQueueWalkinForm');
        const formData = new FormData(form);
        const btn = document.getElementById('adminWalkinSubmitBtn');
        btn.disabled = true;

        try {
            const res = await fetch('{{ route("admin.queue.walkin") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                form.reset();
                bootstrap.Modal.getInstance(document.getElementById('queueWalkInModal')).hide();
                fetchAdminQueueData();
            } else {
                alert(data.message || 'Error');
            }
        } catch (err) {
            console.error(err);
        } finally {
            btn.disabled = false;
        }
    }

    // 4. Admin Queue Data Fetch
    async function fetchAdminQueueData() {
        try {
            const res = await fetch('{{ route("admin.queue.data") }}');
            const data = await res.json();
            if (data.success) {
                adminQueuesData = data.artists;
                renderAdminArtistCards(data.artists);
                renderAdminQueueTable();
            }
        } catch (e) {
            console.error(e);
        }
    }

    function renderAdminArtistCards(artists) {
        artists.forEach(art => {
            adminUpdateArtistBadge(art.id, art.live_status);
            const countBadge = document.querySelector(`.queue-count-badge[data-artist-id="${art.id}"]`);
            const waitBadge = document.querySelector(`.queue-wait-badge[data-artist-id="${art.id}"]`);
            if (countBadge) countBadge.innerText = art.queue_length;
            if (waitBadge) waitBadge.innerText = `~${art.estimated_wait_minutes}m`;
        });
    }

    function renderAdminQueueTable() {
        const filter = document.getElementById('adminQueueArtistFilter').value;
        const tbody = document.getElementById('adminQueueTableBody');
        if (!tbody) return;

        let allTickets = [];
        adminQueuesData.forEach(art => {
            if (filter === 'all' || filter == art.id) {
                art.queue.forEach(t => {
                    t.artist_name = art.name;
                    t.artist_id = art.id;
                    allTickets.push(t);
                });
            }
        });

        if (allTickets.length === 0) {
            tbody.innerHTML = `
                <tr id="adminEmptyQueueRow">
                    <td colspan="6" class="py-5 text-center text-muted-foreground small">
                        No active queue entries for the selected filter.
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = allTickets.map(q => `
            <tr id="adminQueueRow_${q.id}">
                <td class="ps-4 py-3">
                    <span class="badge bg-gold text-dark fw-bold rounded-pill px-2 py-1" style="font-size:0.75rem;">
                        ${q.queue_number}
                    </span>
                    ${q.called_at ? `<div class="text-warning small" style="font-size:0.7rem;">Called ${q.called_at}</div>` : ''}
                </td>
                <td class="py-3">
                    <div class="fw-bold text-white">${q.client_name}</div>
                    <div class="small text-muted">${q.phone_number || 'Walk-in'}</div>
                </td>
                <td class="py-3">
                    <span class="badge bg-dark border border-secondary text-white">${q.artist_name}</span>
                </td>
                <td class="py-3">
                    <div class="small fw-bold text-white">${q.service_name}</div>
                    <div class="small text-muted">${q.duration} min</div>
                </td>
                <td class="py-3">
                    ${q.status === 'in_service' 
                        ? '<span class="badge badge-inprogress rounded-pill px-3 py-1">IN SERVICE</span>' 
                        : `<span class="badge bg-secondary rounded-pill px-3 py-1">WAITING (#${q.position})</span>`}
                </td>
                <td class="pe-4 py-3 text-end">
                    <div class="btn-group btn-group-sm">
                        ${q.status === 'waiting' ? `
                            <button class="btn btn-outline-warning btn-sm" onclick="adminTriggerQueueAction(${q.id}, 'call')">Call</button>
                            <button class="btn btn-gold btn-sm fw-bold px-3" onclick="adminTriggerQueueAction(${q.id}, 'start')">Start</button>
                            <button class="btn btn-outline-danger btn-sm" onclick="adminTriggerQueueAction(${q.id}, 'skip')">No-Show</button>
                        ` : `
                            <button class="btn btn-success btn-sm fw-bold px-3" onclick="adminTriggerQueueAction(${q.id}, 'complete')">Complete</button>
                            <a href="{{ route('admin.pos') }}" class="btn btn-outline-secondary btn-sm text-white">POS</a>
                        `}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function initAdminRealtime() {
        if (window.Echo) {
            window.Echo.private('admin-queue')
                .listen('.QueueUpdated', () => fetchAdminQueueData())
                .listen('.ArtistStatusUpdated', (e) => {
                    adminUpdateArtistBadge(e.artistId, e.liveStatus);
                    fetchAdminQueueData();
                });
        }
        setInterval(fetchAdminQueueData, 8000);
    }

    // Ensure dropdowns rendered inside fixed-strategy escape overflow clipping
    document.addEventListener('show.bs.dropdown', function (e) {
        const menu = e.target.nextElementSibling;
        if (menu && menu.classList.contains('dropdown-menu')) {
            const rect = e.target.getBoundingClientRect();
            menu.style.position  = 'fixed';
            menu.style.top       = (rect.bottom + 4) + 'px';
            menu.style.left      = Math.min(rect.left, window.innerWidth - 200) + 'px';
            menu.style.zIndex    = '9999';
        }
    });

    async function fetchWalkInSlots() {
        const artistId = document.getElementById('walkInArtist').value;
        const date = document.getElementById('walkInDate').value;
        const timeSelect = document.getElementById('walkInTime');

        if (!artistId || !date) {
            timeSelect.disabled = true;
            timeSelect.innerHTML = '<option value="" disabled selected>Select Date & Artist First</option>';
            return;
        }

        timeSelect.disabled = true;
        timeSelect.innerHTML = '<option value="" disabled selected>Checking availability...</option>';

        try {
            const response = await fetch(`{{ route('available-slots') }}?artist_id=${artistId}&date=${date}`);
            const data = await response.json();
            
            let slots = Array.isArray(data) ? data : (data.slots || []);
            timeSelect.innerHTML = '<option value="" disabled selected>Select Time</option>';
            
            if (slots.length === 0) {
                timeSelect.innerHTML = '<option value="" disabled selected>No slots available</option>';
            } else {
                slots.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.time;
                    opt.textContent = s.label;
                    opt.className = 'bg-dark text-white';
                    timeSelect.appendChild(opt);
                });
                timeSelect.disabled = false;
            }
        } catch (error) {
            timeSelect.innerHTML = '<option value="" disabled selected>Error loading slots</option>';
        }
    }

    function openEmailModal(email, name) {
        document.getElementById('emailToInput').value = email;
        document.getElementById('emailClientNameInput').value = name;
        document.getElementById('emailDisplayRecipient').value = `${name} (${email})`;
        new bootstrap.Modal(document.getElementById('emailClientModal')).show();
    }

    async function submitEmailClient(e) {
        e.preventDefault();
        const form = document.getElementById('emailClientForm');
        const formData = new FormData(form);
        const btn = document.getElementById('sendEmailSubmitBtn');
        btn.disabled = true;

        try {
            const res = await fetch('{{ route("admin.send-email") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('emailClientModal')).hide();
            } else {
                alert(data.message || 'Failed to send email');
            }
        } catch (err) {
            alert('An error occurred');
        } finally {
            btn.disabled = false;
        }
    }
</script>

<?php render_admin_layout_end(); ?>
