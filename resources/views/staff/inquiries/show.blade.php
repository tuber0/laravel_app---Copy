<?php
require_once resource_path('views/components/staff-layout.php');
$page = 'inquiries';
render_staff_layout_start($page, 'Inquiry #INQ-' . str_pad($inquiry->id, 4, '0', STR_PAD_LEFT));
?>

<div class="container-fluid py-4 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= route('staff.inquiries.index') ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; padding: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h3 fw-bold text-white mb-0">Inquiry #INQ-{{ str_pad($inquiry->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    {!! $inquiry->status_badge !!}
                </div>
                <div class="text-muted small">Submitted by <strong class="text-white">{{ $inquiry->user->name ?? 'Client' }}</strong> ({{ $inquiry->user->email ?? '' }}) &bull; {{ $inquiry->created_at->format('M d, Y h:i A') }}</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-gold fw-bold px-4 py-2" data-bs-toggle="collapse" data-bs-target="#quotationFormCollapse">
                + Prepare / Update Quotation
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-25 text-white mb-4" role="alert" style="border-radius: 12px;">
            <div class="d-flex align-items-center gap-2">
                <span>✓</span>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Quotation Preparation Form (Collapsible / Prominent) -->
    <div class="collapse mb-4 {{ $inquiry->status === 'pending' ? 'show' : '' }}" id="quotationFormCollapse">
        <div class="glass-card p-4 p-md-5" style="border-radius: 18px; border: 2px solid var(--primary); background: rgba(0,0,0,0.5);">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="text-gold fw-bold mb-1">Formal Quotation Builder</h4>
                    <p class="text-muted small mb-0">Create an itemized quote with pricing, estimated duration, and session notes for the client.</p>
                </div>
                <span class="badge bg-dark border border-secondary text-gold">Step 2: Quotation</span>
            </div>

            <form action="<?= route('staff.inquiries.quotation.store', $inquiry->id) ?>" method="POST" id="quotationForm">
                <?= csrf_field() ?>

                <!-- Dynamic Line Items -->
                <div class="mb-4">
                    <label class="form-label text-gold text-uppercase fw-bold small" style="letter-spacing: 1px;">Itemized Services & Materials</label>
                    <div class="table-responsive">
                        <table class="table table-dark table-sm align-middle mb-2" id="lineItemsTable" style="background: transparent;">
                            <thead>
                                <tr class="text-muted small text-uppercase border-bottom border-secondary">
                                    <th>Description / Line Item</th>
                                    <th style="width: 100px;">Qty</th>
                                    <th style="width: 160px;">Unit Price (₱)</th>
                                    <th style="width: 160px;">Subtotal (₱)</th>
                                    <th style="width: 50px;"></th>
                                </tr>
                            </thead>
                            <tbody id="lineItemsBody">
                                <tr class="line-item-row border-bottom border-secondary border-opacity-25">
                                    <td>
                                        <input type="text" name="items[0][description]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="e.g. 4-inch Custom Realism Tattoo Design" required value="{{ $inquiry->service_type }}">
                                    </td>
                                    <td>
                                        <input type="number" min="1" name="items[0][quantity]" class="form-control form-control-sm bg-dark text-white border-secondary item-qty text-center" value="1" required onchange="calculateQuoteTotal()">
                                    </td>
                                    <td>
                                        <input type="number" step="50" min="0" name="items[0][unit_price]" class="form-control form-control-sm bg-dark text-white border-secondary item-price text-end" placeholder="2500" value="{{ $inquiry->budget ?: ($artist->starting_rate ?? 500) }}" required onchange="calculateQuoteTotal()">
                                    </td>
                                    <td class="text-end fw-bold text-white pe-3 item-subtotal">
                                        ₱0.00
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="removeLineItem(this)" style="border-radius: 6px;">✕</button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end pt-3 text-uppercase fw-bold text-muted small">Total Quotation Amount:</td>
                                    <td class="text-end pt-3"><span class="h4 fw-bold text-gold mb-0" id="grandTotalDisplay">₱0.00</span></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="addLineItem()">
                        + Add Line Item
                    </button>
                </div>

                <!-- Duration, Validity, Notes -->
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-gold small text-uppercase fw-bold">Estimated Duration / Sessions</label>
                        <input type="text" name="estimated_duration" class="form-control bg-dark text-white border-secondary" placeholder="e.g. 2.5 hours (1 session)" style="border-radius: 8px;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-gold small text-uppercase fw-bold">Quote Validity Date</label>
                        <input type="date" name="validity_date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="form-control bg-dark text-white border-secondary" style="border-radius: 8px;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-gold small text-uppercase fw-bold">Artist Notes & Instructions</label>
                        <input type="text" name="notes" class="form-control bg-dark text-white border-secondary" placeholder="e.g. Includes touch-up within 30 days & aftercare kit." style="border-radius: 8px;">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 pt-3 border-top border-secondary border-opacity-25">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-toggle="collapse" data-bs-target="#quotationFormCollapse">Close</button>
                    <button type="submit" class="btn btn-gold px-5 fw-bold" style="border-radius: 10px;">
                        Send Formal Quotation to Client 🚀
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Inquiry Info & Quotations History -->
        <div class="col-lg-7">
            <!-- Details Card -->
            <div class="glass-card p-4 mb-4" style="border-radius: 16px; border: 1px solid var(--border);">
                <h5 class="text-gold fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">Inquiry Specifications</h5>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Client Name:</span>
                        <strong class="text-white">{{ $inquiry->user->name ?? 'Guest Client' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Phone:</span>
                        <strong class="text-white">{{ $inquiry->user->phone ?? 'Not provided' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Service Requested:</span>
                        <span class="badge bg-dark border border-secondary text-gold">{{ $inquiry->service_type }}</span>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Preferred Date:</span>
                        <strong class="text-white">{{ $inquiry->preferred_date ? $inquiry->preferred_date->format('F d, Y') : 'Flexible' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Client's Budget Target:</span>
                        <strong class="text-gold">{{ $inquiry->budget ? '₱' . number_format($inquiry->budget, 2) : 'Open / Unspecified' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Current Inquiry Status:</span>
                        {!! $inquiry->status_badge !!}
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Concept Description:</span>
                    <div class="p-3 bg-dark rounded-3 border border-secondary text-white small" style="line-height: 1.6;">
                        {{ $inquiry->description }}
                    </div>
                </div>

                @if(!empty($inquiry->reference_image_urls))
                    <div>
                        <span class="text-muted small d-block mb-2">Reference Images ({{ count($inquiry->reference_image_urls) }}):</span>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($inquiry->reference_image_urls as $imgUrl)
                                <a href="{{ $imgUrl }}" target="_blank" class="d-block text-decoration-none" style="border-radius: 10px; overflow: hidden; border: 1.5px solid var(--primary); transition: transform 0.2s; background: #000;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <img src="{{ $imgUrl }}" style="width: 100px; height: 100px; object-fit: cover;" alt="Reference Image" onerror="this.onerror=null; this.src='https://via.placeholder.com/100x100/222222/D4AF37?text=Reference';">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quotations Sent Card -->
            <div class="glass-card p-4" id="quotations-section" style="border-radius: 16px; border: 1px solid var(--border);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-gold fw-bold text-uppercase mb-0 small" style="letter-spacing: 1px;">Quotations History</h5>
                    <span class="badge bg-dark border border-secondary text-white">{{ $inquiry->quotations->count() }} Sent</span>
                </div>

                @forelse($inquiry->quotations as $quotation)
                    <div class="p-3 rounded-3 mb-3 border <?= $quotation->status === 'accepted' ? 'border-success bg-success bg-opacity-10' : ($quotation->status === 'declined' ? 'border-danger bg-danger bg-opacity-10' : 'border-secondary bg-dark bg-opacity-50') ?>" id="quotation-{{ $quotation->id }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong class="text-gold">#QTE-{{ str_pad($quotation->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                <span class="text-muted small ms-2">{{ $quotation->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <div>{!! $quotation->status_badge !!}</div>
                        </div>

                        <div class="row g-2 small mb-2">
                            <div class="col-sm-4 text-muted">Amount: <strong class="text-gold">₱{{ number_format($quotation->total_amount, 2) }}</strong></div>
                            <div class="col-sm-4 text-muted">Est Duration: <strong class="text-white">{{ $quotation->estimated_duration ?: 'N/A' }}</strong></div>
                            <div class="col-sm-4 text-muted">Valid until: <strong class="text-white">{{ $quotation->validity_date ? $quotation->validity_date->format('M d, Y') : 'Open' }}</strong></div>
                        </div>

                        <!-- Items list -->
                        <ul class="list-unstyled mb-2 small text-muted">
                            @foreach($quotation->items as $item)
                                <li>&bull; <span class="text-white">{{ $item->description }}</span> ({{ $item->quantity }}x @ ₱{{ number_format($item->unit_price, 2) }}) = <span class="text-gold">₱{{ number_format($item->subtotal, 2) }}</span></li>
                            @endforeach
                        </ul>

                        @if($quotation->revision_notes)
                            <div class="p-2 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25 small text-warning mt-2">
                                <strong>Client Revision Note:</strong> {{ $quotation->revision_notes }}
                            </div>
                        @endif

                        @if($quotation->appointment_id)
                            <div class="mt-2 text-success small fw-bold">
                                ✓ Converted to Appointment #{{ $quotation->appointment_id }}
                            </div>
                        @elseif($quotation->queue_entry_id)
                            <div class="mt-2 text-info small fw-bold">
                                ✓ Converted to Queue Ticket #{{ $quotation->queueEntry->queue_number ?? '' }}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-muted text-center py-4 small">
                        No quotations created yet. Click "+ Prepare / Update Quotation" above to draft one.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right: Message Thread -->
        <div class="col-lg-5">
            <div class="glass-card p-4 d-flex flex-column" style="border-radius: 16px; border: 1px solid var(--border); min-height: 550px;">
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-secondary border-opacity-50 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-gold">💬</span>
                        <h5 class="text-white fw-bold mb-0 small text-uppercase" style="letter-spacing: 1px;">Live Consultation Thread</h5>
                    </div>
                    <span class="badge bg-dark border border-secondary text-muted" style="font-size: 0.7rem;">Direct with Client</span>
                </div>

                <!-- Messages Container -->
                <div id="messages-container" class="flex-grow-1 overflow-auto pe-2 mb-3" style="max-height: 400px; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
                    <!-- Initial request -->
                    <div class="d-flex flex-column align-items-start" id="message-initial">
                        <div class="p-3 chat-bubble-other" style="max-width: 85%;">
                            <div class="chat-sender mb-1">{{ $inquiry->user->name ?? 'Client' }} (Initial Request)</div>
                            <div class="chat-text">{{ $inquiry->description }}</div>
                        </div>
                        <span class="text-muted mt-1" style="font-size: 0.7rem;">{{ $inquiry->created_at->format('h:i A') }}</span>
                    </div>

                    @foreach($inquiry->messages as $msg)
                        @php $isMe = $msg->user_id === Auth::id(); @endphp
                        <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }} inquiry-message-item" id="message-{{ $msg->id }}" data-message-id="{{ $msg->id }}">
                            <div class="p-3 {{ $isMe ? 'chat-bubble-me' : 'chat-bubble-other' }}" style="max-width: 85%; transition: all 0.3s ease;">
                                <div class="chat-sender mb-1">
                                    {{ $isMe ? 'You (' . $artist->name . ')' : ($msg->user->name ?? 'Client') }}
                                </div>
                                <div class="chat-text">{{ $msg->message }}</div>
                                @if($msg->attachment)
                                    @php
                                        $attUrl = \Illuminate\Support\Str::startsWith($msg->attachment, ['http://', 'https://']) ? $msg->attachment : asset('storage/' . ltrim(preg_replace('#^/?storage/#', '', $msg->attachment), '/'));
                                    @endphp
                                    <div class="mt-2">
                                        <a href="{{ $attUrl }}" target="_blank">
                                            <img src="{{ $attUrl }}" style="max-width: 140px; border-radius: 8px; border: 1px solid var(--border);" alt="attachment" onerror="this.onerror=null; this.src='https://via.placeholder.com/140x100/222222/D4AF37?text=Attachment';">
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <span class="text-muted mt-1" style="font-size: 0.7rem;">{{ $msg->created_at->format('h:i A') }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Reply Form -->
                <form action="<?= route('staff.inquiries.message', $inquiry->id) ?>" method="POST" enctype="multipart/form-data" class="pt-2 border-top border-secondary border-opacity-50">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <textarea name="message" rows="2" class="form-control bg-dark text-white border-secondary" placeholder="Write reply to client..." required style="border-radius: 10px 0 0 10px; font-size: 0.85rem;"></textarea>
                        <button type="submit" class="btn btn-gold fw-bold px-3" style="border-radius: 0 10px 10px 0;">
                            Send
                        </button>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <label class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 py-1 px-2 cursor-pointer" style="font-size: 0.75rem; border-radius: 6px;">
                            <span>📎 Attach Sketch / Reference</span>
                            <input type="file" name="attachment" accept="image/*" class="d-none">
                        </label>
                        <span class="text-muted" style="font-size: 0.7rem;">Press Enter to send</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let itemIndex = 1;

function addLineItem() {
    const tbody = document.getElementById('lineItemsBody');
    const tr = document.createElement('tr');
    tr.className = 'line-item-row border-bottom border-secondary border-opacity-25';
    tr.innerHTML = `
        <td>
            <input type="text" name="items[${itemIndex}][description]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="e.g. Additional shading / design setup" required>
        </td>
        <td>
            <input type="number" min="1" name="items[${itemIndex}][quantity]" class="form-control form-control-sm bg-dark text-white border-secondary item-qty text-center" value="1" required onchange="calculateQuoteTotal()">
        </td>
        <td>
            <input type="number" step="50" min="0" name="items[${itemIndex}][unit_price]" class="form-control form-control-sm bg-dark text-white border-secondary item-price text-end" placeholder="500" value="0" required onchange="calculateQuoteTotal()">
        </td>
        <td class="text-end fw-bold text-white pe-3 item-subtotal">
            ₱0.00
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger p-1" onclick="removeLineItem(this)" style="border-radius: 6px;">✕</button>
        </td>
    `;
    tbody.appendChild(tr);
    itemIndex++;
    calculateQuoteTotal();
}

function removeLineItem(btn) {
    const rows = document.querySelectorAll('.line-item-row');
    if (rows.length > 1) {
        btn.closest('tr').remove();
        calculateQuoteTotal();
    } else {
        alert('At least one line item is required.');
    }
}

function calculateQuoteTotal() {
    let grandTotal = 0;
    const rows = document.querySelectorAll('.line-item-row');
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const subtotal = qty * price;
        row.querySelector('.item-subtotal').textContent = '₱' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        grandTotal += subtotal;
    });

    document.getElementById('grandTotalDisplay').textContent = '₱' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

document.addEventListener('DOMContentLoaded', calculateQuoteTotal);

// Auto-scroll to message or quotation if linked with hash (e.g. #message-5 or #quotation-2)
function scrollToHashTarget() {
    const hash = window.location.hash;
    if (!hash) return;

    try {
        const target = document.querySelector(hash);
        if (target) {
            const msgContainer = document.getElementById('messages-container');
            if (msgContainer && msgContainer.contains(target)) {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }

            const bubble = target.querySelector('.chat-bubble-me, .chat-bubble-other, .glass-card') || target;
            bubble.classList.add('highlight-target-glow');
            setTimeout(() => {
                bubble.classList.remove('highlight-target-glow');
            }, 3000);
        }
    } catch(e) {}
}

window.addEventListener('DOMContentLoaded', () => setTimeout(scrollToHashTarget, 200));
window.addEventListener('hashchange', scrollToHashTarget);
</script>

<style>
@keyframes highlightGlow {
    0% {
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.9), 0 0 25px rgba(212, 175, 55, 0.7);
        transform: scale(1.02);
    }
    50% {
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 1), 0 0 30px rgba(212, 175, 55, 0.8);
        transform: scale(1.03);
    }
    100% {
        box-shadow: none;
        transform: scale(1);
    }
}
.highlight-target-glow {
    animation: highlightGlow 2.5s ease !important;
    border-color: #D4AF37 !important;
}
</style>

<?php render_staff_layout_end(); ?>
