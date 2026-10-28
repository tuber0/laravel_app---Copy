<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'oversight';
render_admin_layout_start($page, 'Staff Oversight');
?>

<div class="dashboard-container">
    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0">Personnel Oversight</h1>
            <p class="text-muted small mb-0">Overview of staff activity, performance, and scheduling.</p>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" style="width:20px">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <span class="badge bg-gold-subtle text-gold px-2 py-1 rounded-pill small">Active Personnel</span>
                </div>
                <div class="h3 fw-bold mb-1" style="color: var(--foreground);">{{ $staffCount }}</div>
                <div class="small fw-bold text-uppercase"
                    style="color: var(--muted-foreground); letter-spacing: 0.5px;">Registered Staff</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" style="width:20px">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21a3.745 3.745 0 01-3.068-1.593 3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3a3.746 3.746 0 013.068 1.593 3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill small">Admins</span>
                </div>
                <div class="h3 fw-bold mb-1" style="color: var(--foreground);">{{ $adminCount }}</div>
                <div class="small fw-bold text-uppercase"
                    style="color: var(--muted-foreground); letter-spacing: 0.5px;">Administrators</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon-box" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" style="width:20px">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small">Average
                        Shifts</span>
                </div>
                <div class="h3 fw-bold mb-1" style="color: var(--foreground);">40h</div>
                <div class="small fw-bold text-uppercase"
                    style="color: var(--muted-foreground); letter-spacing: 0.5px;">Avg Weekly Shifts</div>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-4 mt-5">
        <div class="col">
            <h3 class="fw-bold mb-0" style="color:var(--foreground)">Personnel Records</h3>
            <p class="text-muted small mb-0">Manage staff accounts, update access credentials, and oversee roles.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.staff.create') }}" class="btn btn-gold btn-sm px-4 rounded-pill shadow-sm">
                Add Personnel
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="background:rgba(16,185,129,0.1);color:#10b981">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm mb-4" style="background:rgba(212,175,55,0.1);color:#d4af37">
            {{ session('warning') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="background:rgba(239,68,68,0.1);color:#ef4444">
            {{ session('error') }}
        </div>
    @endif

    <div class="glass-card shadow-sm mb-5" style="border-radius: 20px;">
        <div class="px-2">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4 py-4">Personnel Profile</th>
                        <th class="py-4 text-center">Role / Access</th>
                        <th class="py-4">Contact Info</th>
                        <th class="py-4 text-center">Status</th>
                        <th class="py-4 pe-4 text-end">Management Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffMembers as $staff)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-box-round"
                                        style="background: {{ $staff->isAdmin() ? '#3b82f6' : 'var(--primary)' }}; color: {{ $staff->isAdmin() ? 'white' : 'black' }}">
                                        {{ strtoupper(substr($staff->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-0">
                                            <h6 class="fw-bold mb-0" style="color:var(--foreground)">{{ $staff->name }}</h6>
                                            @if($staff->artist && $staff->artist->specialty)
                                                <span class="badge bg-gold-subtle text-gold py-0 px-2"
                                                    style="font-size:0.6rem; border:1px solid rgba(212,175,55,0.2)">
                                                    {{ is_array($staff->artist->specialty) ? $staff->artist->specialty[0] : (explode(',', $staff->artist->specialty)[0]) }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="small" style="color:var(--muted-foreground)">Registered
                                            {{ $staff->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-center">
                                @if($staff->isAdmin())
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1"
                                        style="font-size:0.6rem">ADMIN</span>
                                @else
                                    <span class="badge bg-gold-subtle text-gold rounded-pill px-3 py-1"
                                        style="font-size:0.6rem">STAFF</span>
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="small fw-bold">{{ $staff->email }}</div>
                                <div class="small text-muted">{{ $staff->phone ?: 'No phone provided' }}</div>
                            </td>
                            <td class="py-3 text-center">
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2"
                                    style="font-size:0.65rem">Active</span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="position-relative oversight-dropdown">
                                    <button class="btn btn-muted btn-sm rounded-pill px-3 shadow-sm" type="button"
                                        onclick="toggleOversightMenu(this, event)">
                                        Manage ▼
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 bg-dark-custom position-absolute"
                                        style="z-index: 9999; right: 0;">
                                        @if($staff->isStaff())
                                            <li><a class="dropdown-item py-2 small fw-bold text-success"
                                                    href="{{ route('admin.artists.index', ['highlight' => $staff->artist->id ?? null]) }}">Manage
                                                    Artist Profile</a></li>
                                        @endif
                                        <li><button type="button" class="dropdown-item py-2 small fw-bold"
                                                onclick="openPasswordModal('{{ $staff->id }}', '{{ addslashes($staff->name) }}')">Change
                                                Password</button></li>
                                        <li>
                                            <hr class="dropdown-divider opacity-10">
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST"
                                                class="m-0"
                                                onsubmit="return confirm('WARNING: Are you sure you want to delete this account? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dropdown-item text-danger py-2 small fw-bold">Delete
                                                    Account</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center opacity-50">
                                <p>No personnel records found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function toggleOversightMenu(btn, event) {
        event.stopPropagation();

        // Close all other open menus first
        document.querySelectorAll('.oversight-dropdown .dropdown-menu.show').forEach(m => {
            if (m !== btn.nextElementSibling) m.classList.remove('show');
        });

        // Toggle this one
        btn.nextElementSibling.classList.toggle('show');
    }

    // Close when clicking outside
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.oversight-dropdown')) {
            document.querySelectorAll('.oversight-dropdown .dropdown-menu.show').forEach(m => m.classList.remove('show'));
        }
    });

    function openPasswordModal(id, name) {
        const form = document.getElementById('passwordForm');
        form.action = `/admin/staff/${id}/password`;
        document.getElementById('staffNameDisplay').innerText = name;

        const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
        modal.show();
    }
</script>

<style>
    .avatar-box-round {
        width: 40px;
        height: 40px;
        background: var(--primary);
        color: black;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.9rem;
    }

    .bg-dark-custom {
        background: #1a1a1a !important;
        border: 1px solid var(--border) !important;
    }

    .dropdown-item {
        color: var(--muted-foreground) !important;
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background: rgba(212, 175, 55, 0.1) !important;
        color: var(--primary) !important;
    }

    .dropdown-item.text-danger:hover {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
    }
</style>

<?php render_admin_layout_end(); ?>