<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'dashboard';
render_admin_layout_start($page, 'Executive Overview');
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<div class="dashboard-container">
    <!-- Executive Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <h1 class="page-title mb-1" style="font-family: var(--font-bebas); font-size: 2.4rem; letter-spacing: 1px; color: var(--primary);">
                Executive Overview
            </h1>
            <p class="text-muted small mb-0">Welcome back, {{ auth()->user()->name ?? 'Administrator' }}! Here is what's happening at Pedro's Artisan today.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.pos') }}" class="btn btn-gold d-inline-flex align-items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                </svg>
                Launch POS
            </a>
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-muted d-inline-flex align-items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-calendar" style="width: 18px; height: 18px; color: #ffffff;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                Booking Control
            </a>
            <a href="{{ route('admin.payments.print') }}" target="_blank" class="btn btn-outline-gold d-inline-flex align-items-center gap-2 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.07-.64-2.07-1.18-2.98m12.92 0c-.54.91-.94 1.91-1.18 2.98M9 20h6m-3-16v12m0-12l-3 3m3-3l3 3" />
                </svg>
                Print Report
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="row g-4 mb-5">
        <!-- Revenue Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="glass-card stat-card h-100 p-4" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon-box" style="width: 44px; height: 44px; background: rgba(212, 175, 55, 0.15); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 22px; height: 22px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small">
                        {{ $stats['revenue']['trend'] ?? 'Real-time' }}
                    </span>
                </div>
                <div class="stat-value h3 fw-bold mb-1" style="font-family: var(--font-bebas); letter-spacing: 1px; color: var(--primary); font-size: 2rem;">
                    {{ $stats['revenue']['value'] ?? '₱0.00' }}
                </div>
                <div class="stat-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                    {{ $stats['revenue']['label'] ?? 'Total Revenue' }}
                </div>
            </div>
        </div>

        <!-- Ongoing Appointments Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="glass-card stat-card h-100 p-4" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon-box" style="width: 44px; height: 44px; background: rgba(255, 255, 255, 0.1); color: #ffffff; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="icon-calendar" style="width: 22px; height: 22px; color: #ffffff;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill small">
                        {{ $stats['appointments']['trend'] ?? 'Active' }}
                    </span>
                </div>
                <div class="stat-value h3 fw-bold mb-1" style="font-family: var(--font-bebas); letter-spacing: 1px; color: #FFFFFF; font-size: 2rem;">
                    {{ $stats['appointments']['value'] ?? 0 }}
                </div>
                <div class="stat-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                    {{ $stats['appointments']['label'] ?? 'Ongoing Appointments' }}
                </div>
            </div>
        </div>

        <!-- Total Customers Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="glass-card stat-card h-100 p-4" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon-box" style="width: 44px; height: 44px; background: rgba(59, 130, 246, 0.15); color: #3b82f6; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 22px; height: 22px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-2 py-1 rounded-pill small">
                        {{ $stats['customers']['trend'] ?? 'Registered' }}
                    </span>
                </div>
                <div class="stat-value h3 fw-bold mb-1" style="font-family: var(--font-bebas); letter-spacing: 1px; color: #FFFFFF; font-size: 2rem;">
                    {{ $stats['customers']['value'] ?? 0 }}
                </div>
                <div class="stat-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                    {{ $stats['customers']['label'] ?? 'Total Customers' }}
                </div>
            </div>
        </div>

        <!-- Active Staff Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="glass-card stat-card h-100 p-4" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="stat-icon-box" style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 22px; height: 22px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small">
                        {{ $stats['active_staff']['trend'] ?? 'Active' }}
                    </span>
                </div>
                <div class="stat-value h3 fw-bold mb-1" style="font-family: var(--font-bebas); letter-spacing: 1px; color: #FFFFFF; font-size: 2rem;">
                    {{ $stats['active_staff']['value'] ?? 0 }}
                </div>
                <div class="stat-label small fw-bold text-muted text-uppercase" style="letter-spacing: 0.5px;">
                    {{ $stats['active_staff']['label'] ?? 'Active Staff' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics & Charts Row -->
    <div class="row g-4 mb-5">
        <!-- Daily Booking Trends Chart -->
        <div class="col-12 col-lg-7">
            <div class="glass-card p-4 h-100" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-white" style="font-family: var(--font-montserrat); letter-spacing: 0.5px;">Activity Overview (Last 7 Days)</h5>
                        <p class="text-muted small mb-0">Daily volume of client bookings and walk-ins.</p>
                    </div>
                    <span class="badge bg-gold-subtle text-gold px-3 py-1 rounded-pill small" style="background: rgba(212, 175, 55, 0.15); color: var(--primary);">Live Trend</span>
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="dailyActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Artist Workload Distribution Chart -->
        <div class="col-12 col-lg-5">
            <div class="glass-card p-4 h-100" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-white" style="font-family: var(--font-montserrat); letter-spacing: 0.5px;">Artist Workload</h5>
                        <p class="text-muted small mb-0">Distribution of booked appointments across artisans.</p>
                    </div>
                    <span class="badge bg-secondary bg-opacity-25 text-white px-2 py-1 rounded-pill small">Roster</span>
                </div>
                <div style="position: relative; height: 260px; width: 100%; display: flex; align-items: center; justify-content: center;">
                    <canvas id="artistWorkloadChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments & Operational Hub Row -->
    <div class="row g-4 mb-5">
        <!-- Recent Appointments Table -->
        <div class="col-12 col-xl-8">
            <div class="glass-card p-4 h-100" style="border-radius: 16px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="fw-bold mb-1 text-white" style="font-family: var(--font-montserrat); letter-spacing: 0.5px;">Recent Bookings</h5>
                        <p class="text-muted small mb-0">Latest customer appointments recorded in the system.</p>
                    </div>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-gold btn-sm rounded-pill px-3 fw-bold">
                        View All Bookings &rarr;
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-2 py-3 text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Client</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Service</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Artist</th>
                                <th class="py-3 text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Schedule</th>
                                <th class="py-3 pe-2 text-end text-muted small text-uppercase fw-bold" style="letter-spacing: 0.5px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentAppointments as $apt)
                            <tr>
                                <td class="ps-2 py-3">
                                    <div class="fw-bold text-white">{{ $apt->client_name }}</div>
                                    <div class="small text-muted">{{ $apt->phone_number }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="small fw-semibold text-gold">{{ $apt->service->name ?? 'Custom Service' }}</span>
                                    <div class="small text-muted">₱{{ number_format($apt->total_price, 2) }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="small text-white fw-medium">{{ $apt->artist->name ?? 'Unassigned' }}</span>
                                </td>
                                <td class="py-3">
                                    <div class="small text-white">{{ \Carbon\Carbon::parse($apt->appointment_date)->format('M d, Y') }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($apt->appointment_time)->format('h:i A') }}</div>
                                </td>
                                <td class="py-3 pe-2 text-end">
                                    @php
                                        $badgeClass = [
                                            'completed' => 'badge-completed',
                                            'confirmed' => 'badge-inprogress',
                                            'in-progress' => 'badge-inprogress',
                                            'pending' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25',
                                            'cancelled' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'
                                        ][$apt->status] ?? 'badge-pending';
                                    @endphp
                                    <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2" style="font-size: 0.7rem; letter-spacing: 0.5px; text-transform: uppercase;">
                                        {{ $apt->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted small">No recent bookings recorded.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Access Hub -->
        <div class="col-12 col-xl-4">
            <div class="glass-card p-4 h-100" style="border-radius: 16px;">
                <h5 class="fw-bold mb-1 text-white" style="font-family: var(--font-montserrat); letter-spacing: 0.5px;">Management Hub</h5>
                <p class="text-muted small mb-4">Direct shortcuts to essential administration modules.</p>

                <div class="d-flex flex-column gap-3">
                    <a href="{{ route('admin.pos') }}" class="glass-card p-3 d-flex align-items-center gap-3 text-decoration-none text-white hover-elevate" style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; transition: all 0.2s;">
                        <div style="width: 40px; height: 40px; background: rgba(212, 175, 55, 0.15); color: var(--primary); border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-white">Point of Sale (POS)</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Process walk-in payments & checkouts</div>
                        </div>
                        <span class="text-muted small">&rarr;</span>
                    </a>

                    <a href="{{ route('admin.appointments.index') }}" class="glass-card p-3 d-flex align-items-center gap-3 text-decoration-none text-white hover-elevate" style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; transition: all 0.2s;">
                        <div style="width: 40px; height: 40px; background: rgba(193, 18, 31, 0.15); color: #ef4444; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-white">Booking Management</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Validate and assign customer sessions</div>
                        </div>
                        <span class="text-muted small">&rarr;</span>
                    </a>

                    <a href="{{ route('admin.services.index') }}" class="glass-card p-3 d-flex align-items-center gap-3 text-decoration-none text-white hover-elevate" style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; transition: all 0.2s;">
                        <div style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.15); color: #3b82f6; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M7.848 8.25l1.536.887M7.848 8.25a3 3 0 11-5.196-3 3 3 0 015.196 3zm1.536.887a2.165 2.165 0 011.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 4.092a6.048 6.048 0 008.352 2.675 6.048 6.048 0 01-8.352-2.675zm0 0L9 12m3.45 2.409a2.4 2.4 0 00-.944.962l-1.476 2.898M9.384 9.137l.344-.537c.203-.317.427-.614.667-.89M12 9a3 3 0 11-6 0 3 3 0 016 0zm0 0l1.875 3.375"/></svg>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-white">Service Portfolio</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Modify menu offerings, rates & durations</div>
                        </div>
                        <span class="text-muted small">&rarr;</span>
                    </a>

                    <a href="{{ route('admin.staff.oversight') }}" class="glass-card p-3 d-flex align-items-center gap-3 text-decoration-none text-white hover-elevate" style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; transition: all 0.2s;">
                        <div style="width: 40px; height: 40px; background: rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-white">Personnel Database</div>
                            <div class="text-muted" style="font-size: 0.75rem;">Manage active staff credentials & accounts</div>
                        </div>
                        <span class="text-muted small">&rarr;</span>
                    </a>

                    <a href="{{ route('admin.inventory.index') }}" class="glass-card p-3 d-flex align-items-center gap-3 text-decoration-none text-white hover-elevate" style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; transition: all 0.2s;">
                        <div style="width: 40px; height: 40px; background: rgba(168, 85, 247, 0.15); color: #a855f7; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small text-white">Sales & Audit History</div>
                            <div class="text-muted" style="font-size: 0.75rem;">View payment ledger and receipts</div>
                        </div>
                        <span class="text-muted small">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Daily Activity Line/Bar Chart
    const activityLabels = {!! json_encode($labels) !!};
    const activityData = {!! json_encode($dailyActivity) !!};

    const ctxActivity = document.getElementById('dailyActivityChart');
    if (ctxActivity) {
        new Chart(ctxActivity, {
            type: 'line',
            data: {
                labels: activityLabels,
                datasets: [{
                    label: 'Appointments',
                    data: activityData,
                    borderColor: '#D4AF37',
                    backgroundColor: 'rgba(212, 175, 55, 0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: '#D4AF37',
                    pointBorderColor: '#0B0B0B',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1E1E1E',
                        titleColor: '#D4AF37',
                        bodyColor: '#FFFFFF',
                        borderColor: '#2A2A2A',
                        borderWidth: 1,
                        padding: 10,
                        boxPadding: 4,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + ' appointment' + (context.parsed.y === 1 ? '' : 's');
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#9CA3AF',
                            font: {
                                family: "'Montserrat', sans-serif",
                                size: 12
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: '#9CA3AF',
                            font: {
                                family: "'Montserrat', sans-serif",
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    }

    // 2. Artist Workload Doughnut Chart
    const workloadRaw = {!! json_encode($artistWorkload) !!};
    const workloadLabels = workloadRaw.length ? workloadRaw.map(item => item.name) : ['Pedro', 'Maya', 'Rico', 'Lisa', 'Jay'];
    const workloadCounts = workloadRaw.length ? workloadRaw.map(item => item.count) : [5, 3, 4, 2, 1];

    const ctxWorkload = document.getElementById('artistWorkloadChart');
    if (ctxWorkload) {
        new Chart(ctxWorkload, {
            type: 'doughnut',
            data: {
                labels: workloadLabels,
                datasets: [{
                    data: workloadCounts,
                    backgroundColor: [
                        '#D4AF37',
                        '#C1121F',
                        '#3B82F6',
                        '#10B981',
                        '#A855F7',
                        '#F59E0B'
                    ],
                    borderColor: '#121212',
                    borderWidth: 3,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#D1D1D1',
                            font: {
                                family: "'Montserrat', sans-serif",
                                size: 11
                            },
                            padding: 12,
                            boxWidth: 12,
                            boxHeight: 12
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1E1E1E',
                        titleColor: '#D4AF37',
                        bodyColor: '#FFFFFF',
                        borderColor: '#2A2A2A',
                        borderWidth: 1,
                        padding: 10
                    }
                },
                cutout: '65%'
            }
        });
    }
});
</script>

<?php render_admin_layout_end(); ?>
