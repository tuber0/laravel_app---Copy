<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'customers';
render_admin_layout_start($page, 'Customer Directory');
?>

<div class="dashboard-container">
    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0">Customer Records</h1>
            <p class="text-muted small mb-0">Comprehensive list of all registered clients in the system.</p>
        </div>
    </div>

    <div class="glass-card shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4 py-4">Client Profile</th>
                        <th class="py-4">Contact Info</th>
                        <th class="py-4">Registration Date</th>
                        <th class="py-4 text-center">Status</th>
                        <th class="py-4 pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-box-round">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0" style="color:var(--foreground)">{{ $customer->name }}</h6>
                                    <span class="small" style="color:var(--muted-foreground)">ID: #{{ $customer->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <div class="small fw-bold">{{ $customer->email }}</div>
                            <div class="small text-muted">{{ $customer->phone ?: 'No phone provided' }}</div>
                        </td>
                        <td class="py-3">
                            <div class="small">{{ $customer->created_at->format('M d, Y') }}</div>
                            <div class="small text-muted">{{ $customer->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2" style="font-size:0.65rem">Active</span>
                        </td>
                        <td class="pe-4 text-end">
                            <button onclick="openEmailModal('{{ $customer->email }}', '{{ addslashes($customer->name) }}')" class="btn btn-gold btn-sm px-3 rounded-pill shadow-sm">Email Client</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-5 text-center opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:48px;height:48px;margin-bottom:1rem"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                            <p>No customers found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .avatar-box-round {
        width: 40px; height: 40px;
        background: var(--primary);
        color: black;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.9rem;
    }
</style>

<?php render_admin_layout_end(); ?>
