<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Report – Pedro's Artisan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { background: white; color: black; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; }
        .report-header { border-bottom: 3px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .brand-name { font-weight: 900; font-size: 2.5rem; letter-spacing: 2px; }
        .report-title { text-transform: uppercase; letter-spacing: 3px; color: #666; font-size: 0.9rem; }
        .table thead th { background-color: #f8f9fa !important; color: black !important; border-bottom: 2px solid #dee2e6 !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
        .stat-box { border: 1px solid #dee2e6; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-label { text-transform: uppercase; font-size: 0.7rem; color: #666; font-weight: 700; margin-bottom: 5px; }
        .stat-value { font-size: 1.5rem; font-weight: 800; color: #000; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <div class="no-print mb-4">
            <button class="btn btn-dark btn-sm px-4" onclick="window.close()">Close Preview</button>
            <button class="btn btn-primary btn-sm px-4" onclick="window.print()">Print Now</button>
        </div>

        <div class="report-header d-flex justify-content-between align-items-end">
            <div>
                <div class="report-title">Financial Transaction Report</div>
                <div class="brand-name">PEDRO'S ARTISAN</div>
            </div>
            <div class="text-end">
                <p class="mb-0 small text-muted">Generated on:</p>
                <p class="fw-bold mb-0">{{ now()->format('F d, Y - h:i A') }}</p>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-value">{{ $payments->count() }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-value text-success">₱{{ number_format($payments->where('status', 'completed')->sum('amount'), 2) }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="stat-box">
                    <div class="stat-label">Pending Collection</div>
                    <div class="stat-value text-warning">₱{{ number_format($payments->where('status', 'pending')->sum('amount'), 2) }}</div>
                </div>
            </div>
        </div>

        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Artist</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                @php
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
                @endphp
                <tr>
                    <td>{{ $payment->created_at ? $payment->created_at->format('Y-m-d') : 'N/A' }}</td>
                    <td class="small font-monospace">{{ $payment->transaction_id ?: 'T-'.str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $clientName }}</td>
                    <td>{{ $serviceName }}</td>
                    <td>{{ $artistName }}</td>
                    <td class="text-capitalize">{{ $payment->method ?? 'Cash' }}</td>
                    <td class="text-capitalize">{{ $payment->status ?? 'Completed' }}</td>
                    <td class="text-end fw-bold">₱{{ number_format($payment->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No payment records found.</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="7" class="text-end fw-bold text-uppercase small">Statement Total</td>
                    <td class="text-end fw-bold">₱{{ number_format($payments->sum('amount'), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-5 pt-5 border-top border-light text-center small text-muted">
            <p>This is a computer-generated report. Pedro's Artisan One-Stop Shop.</p>
        </div>
    </div>
</body>
</html>
