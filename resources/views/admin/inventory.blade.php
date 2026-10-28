<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'inventory';
render_admin_layout_start($page, 'Sales History');
?>

<div class="dashboard-container">
    <!-- Header Section -->
    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0">Sales & Revenue History</h1>
            <p class="text-muted small mb-0">Overview of all transactions and financial records.</p>
        </div>
        <div class="col-auto">
            <button onclick="window.print()" class="btn btn-outline-gold btn-sm px-4 rounded-pill shadow-sm">
                Export Report
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <span class="badge bg-gold-subtle text-gold px-2 py-1 rounded-pill small">Lifetime</span>
                </div>
                <div class="h3 fw-bold mb-1" style="color: var(--foreground);">₱{{ number_format($totalRevenue, 2) }}</div>
                <div class="small fw-bold text-uppercase" style="color: var(--muted-foreground); letter-spacing: 0.5px;">Total Revenue</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon-box" style="background: rgba(34, 197, 94, 0.1); color: #22c55e;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75m0 1.5v.75m0 1.5v.75m0 1.5v.75m0 1.5V15h16.5V4.5H3.75z" /></svg>
                    </div>
                    <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill small">Daily</span>
                </div>
                <div class="h3 fw-bold mb-1" style="color: var(--foreground);">₱{{ number_format($todayRevenue, 2) }}</div>
                <div class="small fw-bold text-uppercase" style="color: var(--muted-foreground); letter-spacing: 0.5px;">Today's Sales</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="glass-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="stat-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                    </div>
                    <span class="badge bg-primary-subtle text-primary px-2 py-1 rounded-pill small">Total</span>
                </div>
                <div class="h3 fw-bold mb-1" style="color: var(--foreground);">{{ $payments->count() }}</div>
                <div class="small fw-bold text-uppercase" style="color: var(--muted-foreground); letter-spacing: 0.5px;">Total Transactions</div>
            </div>
        </div>
    </div>

    <!-- Transaction Table -->
    <div class="glass-card shadow-sm overflow-hidden mb-5">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4 py-4">Transaction ID</th>
                        <th class="py-4">Client</th>
                        <th class="py-4">Service</th>
                        <th class="py-4 text-center">Method</th>
                        <th class="py-4">Amount</th>
                        <th class="py-4">Date</th>
                        <th class="py-4 pe-4 text-end">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <?php
                        // Normalise items — handle both 'qty' and 'quantity' key variants
                        $items = [];
                        if (!empty($payment->items)) {
                            foreach ($payment->items as $item) {
                                $qty  = $item['qty'] ?? $item['quantity'] ?? 1;
                                $unit = $item['price'] ?? 0;
                                $items[] = [
                                    'name'     => $item['name'] ?? 'Item',
                                    'qty'      => $qty,
                                    'price'    => number_format($unit * $qty, 2),
                                    'category' => $item['category'] ?? 'POS Item',
                                ];
                            }
                        }

                        // Fallback to appointment service when no items array exists
                        if (empty($items)) {
                            $items[] = [
                                'name'     => optional(optional($payment->appointment)->service)->name ?? 'POS / Walk-in Sale',
                                'qty'      => 1,
                                'price'    => number_format($payment->amount, 2),
                                'category' => optional(optional($payment->appointment)->service)->category ?? 'General',
                            ];
                        }

                        // Build transaction detail data safely
                        $apt    = $payment->appointment;
                        $txnData = [
                            'id'     => $payment->transaction_id ?? $payment->id,
                            'client' => optional(optional($apt)->user)->name ?? ($apt->client_name ?? 'Walk-in Client'),
                            'email'  => optional(optional($apt)->user)->email ?? 'N/A',
                            'artist' => optional(optional($apt)->artist)->name ?? 'Not Assigned',
                            'notes'  => $apt->notes ?? 'No additional notes provided.',
                            'amount' => number_format($payment->amount, 2),
                            'method' => strtoupper($payment->method),
                            'date'   => $payment->created_at->format('M d, Y'),
                            'time'   => $payment->created_at->format('h:i A'),
                            'status' => ucfirst($payment->status),
                            'items'  => $items,
                        ];

                        $clientName = optional(optional($apt)->user)->name ?? ($apt->client_name ?? 'Walk-in Client');
                        $clientEmail = optional(optional($apt)->user)->email ?? '';
                    ?>
                    <tr onclick='showTransactionDetail(@json($txnData))' style="cursor: pointer;">
                        <td class="ps-4 py-3">
                            <span class="small fw-bold text-uppercase" style="color:var(--primary)">#{{ $payment->transaction_id ?? $payment->id }}</span>
                        </td>
                        <td class="py-3">
                            <div class="small fw-bold">{{ $clientName }}</div>
                            <div class="small text-muted">{{ $clientEmail }}</div>
                        </td>
                        <td class="py-3">
                            <span class="small">{{ count($items) > 1 ? count($items) . ' items' : $items[0]['name'] }}</span>
                        </td>
                        <td class="py-3 text-center">
                            <span class="badge bg-muted text-foreground border border-secondary px-2 py-1 small" style="font-size: 0.65rem;">{{ strtoupper($payment->method) }}</span>
                        </td>
                        <td class="py-3">
                            <span class="fw-bold" style="color:var(--foreground)">₱{{ number_format($payment->amount, 2) }}</span>
                        </td>
                        <td class="py-3">
                            <div class="small">{{ $payment->created_at->format('M d, Y') }}</div>
                            <div class="small text-muted">{{ $payment->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="pe-4 text-end">
                            @if($payment->status === 'completed')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1" style="font-size:0.65rem">Paid</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-1" style="font-size:0.65rem">{{ ucfirst($payment->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-5 text-center opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" style="width:48px;height:48px;margin-bottom:1rem"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75m0 1.5v.75m0 1.5v.75m0 1.5v.75m0 1.5V15h16.5V4.5H3.75z" /></svg>
                            <p>No transaction history found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Transaction Detail Modal -->
<div class="modal fade" id="txnDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background:#1a1a1a; color:white; border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color:var(--primary); font-family:var(--font-bebas); letter-spacing:1px">Transaction Receipt</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <div class="text-center mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Total Amount Paid</div>
                    <div class="h2 fw-bold text-gold" id="modalAmount">₱0.00</div>
                    <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill mt-2" id="modalStatus">Completed</div>
                </div>

                <div class="row g-4">
                    <div class="col-6">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Transaction ID</label>
                        <div class="fw-bold text-white" id="modalTxnId">#000000</div>
                    </div>
                    <div class="col-6 text-end">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-1">Payment Method</label>
                        <div class="badge bg-dark border border-secondary px-2 py-1" id="modalMethod">CASH</div>
                    </div>
                    
                    <div class="col-12">
                        <div class="p-3 rounded-4" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05);">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted fw-bold">Client</span>
                                <span class="small fw-bold" id="modalClient">Guest</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted fw-bold">Email</span>
                                <span class="small fw-bold" id="modalEmail">N/A</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="small text-muted fw-bold">Assigned Artist</span>
                                <span class="small fw-bold" id="modalArtist">Pedro</span>
                            </div>
                        </div>
                    </div>

                    <!-- Services Breakdown -->
                    <div class="col-12">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-2">Services Breakdown</label>
                        <div class="rounded-4 overflow-hidden border border-secondary border-opacity-25">
                            <table class="table table-dark table-sm mb-0" style="font-size: 0.85rem;">
                                <thead class="bg-white bg-opacity-10">
                                    <tr>
                                        <th class="ps-3 py-2">Service / Category</th>
                                        <th class="py-2 text-center">Qty</th>
                                        <th class="pe-3 py-2 text-end">Price</th>
                                    </tr>
                                </thead>
                                <tbody id="modalItemsBody">
                                    <!-- Dynamic items here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="col-12" id="modalNotesContainer">
                        <label class="small text-muted text-uppercase fw-bold d-block mb-2">Internal Notes</label>
                        <div class="p-3 rounded-4 small italic text-muted" style="background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1);" id="modalNotes">
                            No additional notes.
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:14px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span id="modalDateTime">Oct 24, 2023 at 02:30 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-muted w-100 py-3 fw-bold rounded-pill" data-bs-dismiss="modal">Close Breakdown</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showTransactionDetail(data) {
        document.getElementById('modalAmount').innerText = '₱' + data.amount;
        document.getElementById('modalStatus').innerText = data.status;
        document.getElementById('modalTxnId').innerText = '#' + data.id;
        document.getElementById('modalMethod').innerText = data.method;
        document.getElementById('modalClient').innerText = data.client;
        document.getElementById('modalEmail').innerText = data.email;
        document.getElementById('modalArtist').innerText = data.artist;
        document.getElementById('modalNotes').innerText = data.notes;
        document.getElementById('modalDateTime').innerText = data.date + ' at ' + data.time;

        // Populate items
        const body = document.getElementById('modalItemsBody');
        body.innerHTML = data.items.map(i => `
            <tr>
                <td class="ps-3 py-3 fw-bold">
                    <div>${i.name}</div>
                    <div class="small text-muted fw-normal">${i.category}</div>
                </td>
                <td class="py-3 text-center text-muted">${i.qty}</td>
                <td class="pe-3 py-3 text-end fw-bold text-gold">₱${i.price}</td>
            </tr>
        `).join('');

        const modal = new bootstrap.Modal(document.getElementById('txnDetailModal'));
        modal.show();
    }
</script>

<style>
    .stat-icon-box {
        width: 42px; height: 42px;
        background: rgba(212, 175, 55, 0.1);
        color: var(--primary);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(212, 175, 55, 0.05) !important;
    }
</style>

<?php render_admin_layout_end(); ?>
