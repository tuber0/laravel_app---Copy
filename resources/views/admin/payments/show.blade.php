<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'payments';
render_admin_layout_start($page, 'Transaction Details');

$apt = $payment->appointment;
$clientName = optional(optional($apt)->user)->name ?? ($apt->client_name ?? 'Walk-in Client');
$serviceName = optional(optional($apt)->service)->name;
if (!$serviceName && !empty($payment->items)) {
    $itemNames = [];
    foreach ($payment->items as $item) {
        $qty = $item['qty'] ?? $item['quantity'] ?? 1;
        $name = $item['name'] ?? 'Item';
        $itemNames[] = $qty > 1 ? "$name (x$qty)" : $name;
    }
    $serviceName = implode(', ', $itemNames);
}
$serviceName = $serviceName ?: 'POS / Walk-in Sale';
$artistName = optional(optional($apt)->artist)->name ?? 'N/A';
?>

<div class="dashboard-container">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h1 class="page-title mb-0">Transaction #{{ $payment->transaction_id ?: 'T-'.str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</h1>
            <p class="text-muted small mb-0">Detailed view of transaction record.</p>
        </div>
        <div class="col-auto d-flex gap-2">
            <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary px-3">
                &larr; Back to History
            </a>
            <button class="btn btn-gold px-4" onclick="window.print()">
                Print Receipt
            </button>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="glass-card p-4 p-md-5" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold d-block mb-1">Receipt for</span>
                        <h3 class="fw-bold text-white mb-0">{{ $clientName }}</h3>
                        <span class="text-muted small">{{ $payment->created_at ? $payment->created_at->format('F d, Y - h:i A') : 'N/A' }}</span>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success-subtle text-success px-3 py-2 text-uppercase fw-bold">
                            {{ $payment->status }}
                        </span>
                        <div class="mt-2 text-muted small font-mono">
                            {{ $payment->transaction_id ?: 'T-'.str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold d-block mb-1">Service / Description</span>
                        <h5 class="text-white">{{ $serviceName }}</h5>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold d-block mb-1">Assigned Artist</span>
                        <h5 class="text-white">{{ $artistName }}</h5>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold d-block mb-1">Payment Method</span>
                        <h5 class="text-white text-capitalize">{{ $payment->method ?? 'Cash' }}</h5>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small text-uppercase fw-bold d-block mb-1">Total Amount</span>
                        <h3 class="fw-bold" style="color: var(--primary);">₱{{ number_format($payment->amount, 2) }}</h3>
                    </div>
                </div>

                @if(!empty($payment->items))
                <div class="border-top pt-4 mt-4">
                    <h6 class="text-uppercase small fw-bold text-muted mb-3">Item Breakdown</h6>
                    <div class="table-responsive">
                        <table class="table table-dark-custom mb-0">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->items as $item)
                                <tr>
                                    <td>{{ $item['name'] ?? 'Service' }}</td>
                                    <td class="text-center">{{ $item['qty'] ?? $item['quantity'] ?? 1 }}</td>
                                    <td class="text-end">₱{{ number_format(($item['price'] ?? 0) * ($item['qty'] ?? $item['quantity'] ?? 1), 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-container { padding: 3rem; background: var(--background); min-height: 100vh; }
    .page-title { font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 2.5rem; }
    .font-mono { font-family: 'Courier New', Courier, monospace; }
    .btn-gold { background: var(--primary); color: black; font-weight: 700; border: none; border-radius: 12px; }
    .btn-gold:hover { background: #e5c060; }
</style>

<?php render_admin_layout_end(); ?>
