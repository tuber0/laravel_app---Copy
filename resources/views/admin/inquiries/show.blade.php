<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'inquiries';
render_admin_layout_start($page, 'Inquiry #INQ-' . str_pad($inquiry->id, 4, '0', STR_PAD_LEFT));
?>

<div class="container-fluid py-4 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= route('admin.inquiries.index') ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; padding: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h3 fw-bold text-white mb-0">Inquiry #INQ-{{ str_pad($inquiry->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    {!! $inquiry->status_badge !!}
                </div>
                <div class="text-muted small">Client: <strong class="text-white">{{ $inquiry->user->name ?? 'Client' }}</strong> ({{ $inquiry->user->email ?? '' }}) &bull; Artist: <strong class="text-gold">{{ $inquiry->artist->name ?? 'Artist' }}</strong></div>
            </div>
        </div>

        <button type="button" class="btn btn-gold fw-bold px-4 py-2" data-bs-toggle="collapse" data-bs-target="#adminQuoteCollapse">
            + Issue Admin Quotation
        </button>
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

    <!-- Admin Quotation Form Collapse -->
    <div class="collapse mb-4" id="adminQuoteCollapse">
        <div class="glass-card p-4 p-md-5" style="border-radius: 18px; border: 2px solid var(--primary); background: rgba(0,0,0,0.5);">
            <h4 class="text-gold fw-bold mb-1">Issue Quotation on Behalf of Artist ({{ $inquiry->artist->name ?? 'Artist' }})</h4>
            <p class="text-muted small mb-4">Admins can assist artists by directly publishing formal quotations to clients.</p>

            <form action="<?= route('admin.inquiries.quotation.store', $inquiry->id) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label text-gold small fw-bold">Service / Line Item Description</label>
                            <input type="text" name="items[0][description]" class="form-control form-control-sm bg-dark text-white border-secondary" required value="{{ $inquiry->service_type }}">
                        </div>
                        <div class="col-2">
                            <label class="form-label text-gold small fw-bold">Qty</label>
                            <input type="number" min="1" name="items[0][quantity]" class="form-control form-control-sm bg-dark text-white border-secondary" value="1" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label text-gold small fw-bold">Price (₱)</label>
                            <input type="number" step="50" min="0" name="items[0][unit_price]" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $inquiry->budget ?: 1500 }}" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label text-gold small fw-bold">Estimated Duration</label>
                        <input type="text" name="estimated_duration" class="form-control bg-dark text-white border-secondary" placeholder="e.g. 2 hours">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-gold small fw-bold">Validity Date</label>
                        <input type="date" name="validity_date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="form-control bg-dark text-white border-secondary">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-gold small fw-bold">Notes</label>
                        <input type="text" name="notes" class="form-control bg-dark text-white border-secondary" placeholder="Admin notes...">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#adminQuoteCollapse">Cancel</button>
                    <button type="submit" class="btn btn-gold fw-bold px-4">Submit Quotation to Client</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Details & Quotations -->
        <div class="col-lg-7">
            <div class="glass-card p-4 mb-4" style="border-radius: 16px; border: 1px solid var(--border);">
                <h5 class="text-gold fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">Inquiry Specs</h5>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6"><span class="text-muted small d-block">Client:</span> <strong class="text-white">{{ $inquiry->user->name ?? 'Client' }}</strong></div>
                    <div class="col-sm-6"><span class="text-muted small d-block">Assigned Artist:</span> <strong class="text-gold">{{ $inquiry->artist->name ?? 'Artist' }}</strong></div>
                    <div class="col-sm-6"><span class="text-muted small d-block">Service:</span> <span class="badge bg-dark border border-secondary text-white">{{ $inquiry->service_type }}</span></div>
                    <div class="col-sm-6"><span class="text-muted small d-block">Budget:</span> <strong class="text-gold">{{ $inquiry->budget ? '₱' . number_format($inquiry->budget, 2) : 'Open' }}</strong></div>
                </div>
                <div class="p-3 bg-dark rounded-3 border border-secondary text-white small mb-3">
                    {{ $inquiry->description }}
                </div>
                @if(!empty($inquiry->reference_image_urls))
                    <div>
                        <span class="text-muted small d-block mb-2">Uploaded References ({{ count($inquiry->reference_image_urls) }}):</span>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($inquiry->reference_image_urls as $imgUrl)
                                <a href="{{ $imgUrl }}" target="_blank" class="d-block text-decoration-none" style="border-radius: 8px; overflow: hidden; border: 1px solid var(--primary); transition: transform 0.2s; background: #000;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <img src="{{ $imgUrl }}" style="width: 80px; height: 80px; object-fit: cover;" alt="Reference Image" onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80/222222/D4AF37?text=Reference';">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="glass-card p-4" id="quotations-section" style="border-radius: 16px; border: 1px solid var(--border);">
                <h5 class="text-gold fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">Quotations Created ({{ $inquiry->quotations->count() }})</h5>
                @forelse($inquiry->quotations as $quotation)
                    <div class="p-3 rounded-3 mb-3 border border-secondary bg-dark bg-opacity-50" id="quotation-{{ $quotation->id }}">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-dark border border-secondary text-gold">QUOTE #{{ $quotation->id }}</span>
                            {!! $quotation->status_badge !!}
                        </div>
                        <div class="h5 fw-bold text-gold mb-1">₱{{ number_format($quotation->total_amount, 2) }}</div>
                        <div class="text-muted small">Valid Until: {{ $quotation->validity_date ? $quotation->validity_date->format('M d, Y') : 'N/A' }}</div>
                        @if($quotation->notes)<div class="text-muted small mt-1">Notes: {{ $quotation->notes }}</div>@endif
                        @if($quotation->appointment_id)<div class="text-success small fw-bold mt-2">✓ Booked as Appointment #{{ $quotation->appointment_id }}</div>@endif
                        @if($quotation->queue_entry_id)<div class="text-info small fw-bold mt-2">✓ Live Queue Ticket #{{ $quotation->queueEntry->queue_number ?? '' }}</div>@endif
                    </div>
                @empty
                    <div class="text-muted small text-center py-3">No quotes yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Chat Thread -->
        <div class="col-lg-5">
            <div class="glass-card p-4 d-flex flex-column" style="border-radius: 16px; border: 1px solid var(--border); min-height: 550px;">
                <h5 class="text-white fw-bold mb-3 small text-uppercase" style="letter-spacing: 1px;">Conversation Thread</h5>
                <div id="messages-container" class="flex-grow-1 overflow-auto pe-2 mb-3" style="max-height: 400px; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
                    <!-- Initial Request -->
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
                                <div class="chat-sender mb-1">{{ $msg->user->name ?? 'User' }}</div>
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
                <form action="<?= route('admin.inquiries.message', $inquiry->id) ?>" method="POST" enctype="multipart/form-data" class="pt-2 border-top border-secondary border-opacity-50">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <textarea name="message" rows="2" class="form-control bg-dark text-white border-secondary" placeholder="Admin message..." required></textarea>
                        <button type="submit" class="btn btn-gold fw-bold px-3">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function scrollToHashTarget() {
    const hash = window.location.hash;
    if (!hash) return;

    try {
        const target = document.querySelector(hash);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
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

<?php render_admin_layout_end(); ?>
