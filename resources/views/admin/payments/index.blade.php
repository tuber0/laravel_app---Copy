<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'payments';
render_admin_layout_start($page, 'Transaction History');
?>

<div class="dashboard-container">
    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0">Financial History</h1>
            <p class="text-muted small mb-0">Track all shop transactions, payment statuses, and revenue records.</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('admin.payments.print') }}" target="_blank" class="btn btn-outline-gold px-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;margin-right:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.89l-2.25 2.25m0 0l2.25 2.25m-2.25-2.25h16.5m-3.75-3c-.62 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125h1.5c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-1.5zm-15 0c-.62 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125h1.5c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-1.5z" /></svg>
                Export Report
            </a>
            <button class="btn btn-gold px-4 shadow-sm" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;margin-right:8px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.89l-2.25 2.25m0 0l2.25 2.25m-2.25-2.25h16.5M6.75 6.75h10.5M6.75 10.5h10.5M6.75 14.25h10.5" /></svg>
                Print Current List
            </button>
        </div>
    </div>

    <!-- Payment Stats -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card-simple border-border">
                <span class="text-muted small text-uppercase fw-bold">Total Revenue</span>
                <h2 class="fw-bold text-white mb-0 mt-1">₱0.00</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple border-border">
                <span class="text-muted small text-uppercase fw-bold">Comp. Payments</span>
                <h2 class="fw-bold text-success mb-0 mt-1">0</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card-simple border-border">
                <span class="text-muted small text-uppercase fw-bold">Pending Amount</span>
                <h2 class="fw-bold text-warning mb-0 mt-1">₱0.00</h2>
            </div>
        </div>
    </div>

    <div class="glass-card shadow-sm overflow-hidden" style="border-radius: 20px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Transaction ID</th>
                        <th>Date & Time</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Artist</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td class="ps-4 font-mono small">{{ $payment->transaction_id ?: 'T-'.str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $payment->created_at->format('M d, Y - h:i A') }}</td>
                        <td>{{ $payment->appointment->client_name }}</td>
                        <td>{{ $payment->appointment->service->name }}</td>
                        <td>{{ $payment->appointment->artist->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-dark-subtle border border-border text-capitalize px-2 py-1">
                                {{ $payment->method }}
                            </span>
                        </td>
                        <td class="fw-bold" style="color: var(--foreground)">₱{{ number_format($payment->amount, 2) }}</td>
                        <td>
                            <span class="status-pill status-{{ $payment->status }}">
                                {{ $payment->status }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <button class="btn btn-icon-sm" onclick="viewDetails({{ $payment->id }})">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            No payment records found matching your criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $payments->links() }}
    </div>
</div>

<style>
    .dashboard-container { padding: 3rem; background: var(--background); min-height: 100vh; }
    .page-title { font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 3rem; }
    
    .stat-card-simple { background: var(--sidebar); border: 1px solid var(--border); border-radius: 16px; padding: 1.5rem; transition: 0.3s; }
    .stat-card-simple:hover { border-color: var(--primary); transform: translateY(-5px); }
    
    .table-dark-custom { color: black; background: white; --bs-table-bg: transparent; }
    .table-dark-custom thead th { font-family: var(--font-montserrat); text-transform: uppercase; font-size: 0.7rem; letter-spacing: 1.5px; color: black; padding: 1.25rem 0.75rem; border-bottom: 2px solid black; font-weight: 800; }
    .table-dark-custom tbody td { border-bottom: 1px solid #eee; padding: 1rem 0.75rem; vertical-align: middle; font-size: 0.9rem; }
    .table-dark-custom tbody tr:hover { background: #f8f9fa; }
    
    .status-pill { padding: 0.35rem 0.75rem; border-radius: 30px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
    .status-completed { background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2); }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2); }
    .status-failed { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }
    
    .btn-gold { background: var(--primary); color: black; font-weight: 700; border: none; border-radius: 12px; }
    .btn-gold:hover { background: #e5c060; transform: translateY(-2px); box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3); }
    .btn-outline-gold { border: 2px solid var(--primary); color: var(--primary); background: transparent; font-weight: 700; border-radius: 12px; }
    .btn-outline-gold:hover { background: var(--primary); color: black; }
    
    .font-mono { font-family: 'Courier New', Courier, monospace; }

    /* Print Overrides */
    @media print {
        header, aside, .col-auto, .mt-4, .btn-icon-sm { display: none !important; }
        body { background: white !important; color: black !important; }
        .dashboard-container { padding: 0 !important; }
        .stat-card-simple { border: 1px solid #ddd !important; }
        .stat-card-simple h2, .stat-card-simple span { color: black !important; }
        .table-dark-custom { background: white !important; color: black !important; width: 100% !important; border: 1px solid #ddd !important; }
        .table-dark-custom thead th { background: #f8f9fa !important; color: black !important; border-bottom: 2px solid #ddd !important; }
        .table-dark-custom tbody td { border-bottom: 1px solid #eee !important; color: black !important; }
        .status-pill { border: 1px solid #ddd !important; background: transparent !important; color: black !important; }
    }
</style>

<script>
    function viewDetails(id) {
        window.location.href = '/admin/payments/' + id;
    }
</script>

<?php render_admin_layout_end(); ?>
