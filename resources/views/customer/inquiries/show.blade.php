<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'inquiries';
render_customer_layout_start($page, 'Inquiry #' . str_pad($inquiry->id, 4, '0', STR_PAD_LEFT));
?>

<div class="container-fluid py-4 px-md-4">
    <!-- Breadcrumb & Status Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= route('customer.inquiries.index') ?>" class="btn btn-sm btn-outline-secondary" style="border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; padding: 0;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="h3 fw-bold text-white mb-0">Inquiry #INQ-{{ str_pad($inquiry->id, 4, '0', STR_PAD_LEFT) }}</h1>
                    {!! $inquiry->status_badge !!}
                </div>
                <div class="text-muted small">Submitted on {{ $inquiry->created_at->format('M d, Y h:i A') }}</div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="text-muted small">Assigned Artist:</span>
            <div class="d-flex align-items-center gap-2 bg-dark px-3 py-1 rounded-pill border border-secondary">
                <img src="<?= $inquiry->artist && $inquiry->artist->image_url ? (\Illuminate\Support\Str::startsWith($inquiry->artist->image_url, 'http') ? $inquiry->artist->image_url : asset('storage/' . $inquiry->artist->image_url)) : 'https://via.placeholder.com/40' ?>" 
                     style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover;">
                <strong class="text-gold small">{{ $inquiry->artist->name ?? 'Artist' }}</strong>
            </div>
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

    <div class="row g-4">
        <!-- Left Column: Inquiry Summary & Quotations -->
        <div class="col-lg-7">
            <!-- Inquiry Details Card -->
            <div class="glass-card p-4 mb-4" style="border-radius: 16px; border: 1px solid var(--border);">
                <h5 class="text-gold fw-bold text-uppercase mb-3 small" style="letter-spacing: 1px;">Inquiry Overview</h5>
                <div class="row g-3 mb-3">
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Requested Service:</span>
                        <strong class="text-white">{{ $inquiry->service_type }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Preferred Date:</span>
                        <strong class="text-white">{{ $inquiry->preferred_date ? $inquiry->preferred_date->format('F d, Y') : 'Flexible / No Preference' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Client Stated Budget:</span>
                        <strong class="text-gold">{{ $inquiry->budget ? '₱' . number_format($inquiry->budget, 2) : 'Not specified' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <span class="text-muted small d-block">Status:</span>
                        <span class="badge bg-dark border border-secondary text-white">{{ ucfirst($inquiry->status) }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Concept & Placement Description:</span>
                    <div class="p-3 bg-dark rounded-3 border border-secondary text-white small" style="line-height: 1.6;">
                        {{ $inquiry->description }}
                    </div>
                </div>

                @if(!empty($inquiry->reference_image_urls))
                    <div>
                        <span class="text-muted small d-block mb-2">Reference Images Uploaded ({{ count($inquiry->reference_image_urls) }}):</span>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($inquiry->reference_image_urls as $imgUrl)
                                <a href="{{ $imgUrl }}" target="_blank" class="d-block text-decoration-none" style="border-radius: 10px; overflow: hidden; border: 1.5px solid var(--primary); transition: transform 0.2s; background: #000;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    <img src="{{ $imgUrl }}" style="width: 90px; height: 90px; object-fit: cover;" alt="Reference Image" onerror="this.onerror=null; this.src='https://via.placeholder.com/90x90/222222/D4AF37?text=Reference';">
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Quotations Section -->
            <div class="mb-4" id="quotations-section">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="text-gold fw-bold text-uppercase mb-0 small" style="letter-spacing: 1px;">Artist Quotations</h5>
                    <span class="badge bg-dark border border-secondary text-white">{{ $inquiry->quotations->count() }} Quote(s)</span>
                </div>

                @if($inquiry->quotations->isEmpty())
                    <div class="glass-card p-4 text-center" style="border-radius: 16px; border: 1px dashed rgba(255,255,255,0.15);">
                        <div class="text-gold mb-2">⏳</div>
                        <h6 class="text-white fw-bold mb-1">Awaiting Quotation from Artist</h6>
                        <p class="text-muted small mb-0">The artist is reviewing your project details. Once the quotation with line items and pricing is ready, it will appear here for your review and approval.</p>
                    </div>
                @else
                    @foreach($inquiry->quotations as $quotation)
                        <div class="glass-card p-4 mb-3 quotation-card" id="quotation-{{ $quotation->id }}" style="border-radius: 16px; border: 2px solid <?= $quotation->status === 'accepted' ? 'var(--primary)' : ($quotation->status === 'declined' ? 'rgba(239,68,68,0.5)' : 'rgba(212,175,55,0.4)') ?>; background: rgba(0,0,0,0.3);">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-dark border border-secondary text-gold">QUOTE #QTE-{{ str_pad($quotation->id, 4, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-muted small ms-2">{{ $quotation->created_at->format('M d, Y') }}</span>
                                </div>
                                <div>
                                    {!! $quotation->status_badge !!}
                                </div>
                            </div>

                            <!-- Line Items Table -->
                            <div class="table-responsive mb-3">
                                <table class="table table-dark table-sm table-borderless align-middle mb-0" style="background: transparent;">
                                    <thead>
                                        <tr class="border-bottom border-secondary" style="font-size: 0.75rem; text-transform: uppercase; color: var(--muted-foreground);">
                                            <th>Description</th>
                                            <th class="text-center" style="width: 70px;">Qty</th>
                                            <th class="text-end" style="width: 110px;">Unit Price</th>
                                            <th class="text-end" style="width: 120px;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @foreach($quotation->items as $item)
                                            <tr class="border-bottom border-secondary border-opacity-25">
                                                <td class="text-white fw-semibold">{{ $item->description }}</td>
                                                <td class="text-center text-muted">{{ $item->quantity }}</td>
                                                <td class="text-end text-muted">₱{{ number_format($item->unit_price, 2) }}</td>
                                                <td class="text-end text-white fw-bold">₱{{ number_format($item->subtotal, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end pt-3 text-uppercase fw-bold text-muted small">Total Quoted Price:</td>
                                            <td class="text-end pt-3"><span class="h4 fw-bold text-gold mb-0">₱{{ number_format($quotation->total_amount, 2) }}</span></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <!-- Metadata row -->
                            <div class="row g-2 mb-3 p-2 rounded-3 bg-dark bg-opacity-50 small">
                                @if($quotation->estimated_duration)
                                    <div class="col-sm-6 text-muted">
                                        ⏱️ Est. Duration: <strong class="text-white">{{ $quotation->estimated_duration }}</strong>
                                    </div>
                                @endif
                                @if($quotation->validity_date)
                                    <div class="col-sm-6 text-muted">
                                        <span style="width:14px;height:14px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('calendar') ?></span> Quote Valid Until: <strong class="text-white">{{ $quotation->validity_date->format('M d, Y') }}</strong>
                                    </div>
                                @endif
                                @if($quotation->notes)
                                    <div class="col-12 text-muted mt-1">
                                        📝 Notes: <span class="text-white">{{ $quotation->notes }}</span>
                                    </div>
                                @endif
                                @if($quotation->revision_notes)
                                    <div class="col-12 text-warning mt-1">
                                        ⚠️ Revision Note: <span>{{ $quotation->revision_notes }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Converted Status Indicator -->
                            @if($quotation->appointment_id)
                                <div class="alert alert-success bg-success bg-opacity-10 border border-success border-opacity-25 py-2 px-3 small text-white mb-3 d-flex align-items-center gap-2">
                                    <span>🎉</span>
                                    <div>Converted to Confirmed Appointment #{{ $quotation->appointment_id }} on {{ $quotation->appointment->appointment_date ?? '' }}</div>
                                </div>
                            @elseif($quotation->queue_entry_id)
                                <div class="alert alert-info bg-info bg-opacity-10 border border-info border-opacity-25 py-2 px-3 small text-white mb-3 d-flex align-items-center gap-2">
                                    <span>🎟️</span>
                                    <div>Converted to Live Queue Ticket #{{ $quotation->queueEntry->queue_number ?? '' }}</div>
                                </div>
                            @endif

                            <!-- Action Buttons if Pending -->
                            @if($quotation->status === 'pending')
                                <div class="d-flex flex-wrap gap-2 pt-2 border-top border-secondary border-opacity-25">
                                    <!-- Accept Button (Triggers Modal) -->
                                    <button type="button" class="btn btn-gold px-4 py-2 fw-bold small rounded-pill" data-bs-toggle="modal" data-bs-target="#acceptModal{{ $quotation->id }}">
                                        ✓ Accept Quotation & Book
                                    </button>

                                    <!-- Request Revision Button -->
                                    <button type="button" class="btn btn-outline-warning px-3 py-2 small rounded-pill" data-bs-toggle="collapse" data-bs-target="#revisionBox{{ $quotation->id }}">
                                        ✎ Request Revision
                                    </button>

                                    <!-- Decline Form -->
                                    <form action="<?= route('customer.quotations.respond', $quotation->id) ?>" method="POST" onsubmit="return confirm('Are you sure you want to decline this quotation?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="decline">
                                        <button type="submit" class="btn btn-outline-danger px-3 py-2 small rounded-pill">
                                            ✕ Decline
                                        </button>
                                    </form>
                                </div>

                                <!-- Collapsible Revision Box -->
                                <div class="collapse mt-3" id="revisionBox{{ $quotation->id }}">
                                    <form action="<?= route('customer.quotations.respond', $quotation->id) ?>" method="POST" class="p-3 bg-dark rounded-3 border border-warning border-opacity-50">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="action" value="revision_requested">
                                        <label class="form-label text-warning small fw-bold">Describe desired modifications to the quote:</label>
                                        <textarea name="revision_notes" rows="3" class="form-control bg-dark text-white border-secondary mb-2" required placeholder="e.g. Can we simplify the background shading to adjust the price?"></textarea>
                                        <button type="submit" class="btn btn-warning btn-sm fw-bold">Submit Revision Request</button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Right Column: Message Thread -->
        <div class="col-lg-5">
            <div class="glass-card p-4 d-flex flex-column" style="border-radius: 16px; border: 1px solid var(--border); min-height: 550px;">
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom border-secondary border-opacity-50 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-gold">💬</span>
                        <h5 class="text-white fw-bold mb-0 small text-uppercase" style="letter-spacing: 1px;">Consultation Thread</h5>
                    </div>
                    <span class="badge bg-dark border border-secondary text-muted" style="font-size: 0.7rem;">Real-time</span>
                </div>

                <!-- Messages Container -->
                <div id="messages-container" class="flex-grow-1 overflow-auto pe-2 mb-3" style="max-height: 400px; display: flex; flex-direction: column; gap: 12px; scroll-behavior: smooth;">
                    <!-- Initial Message from inquiry description -->
                    <div class="d-flex flex-column align-items-end" id="message-initial">
                        <div class="p-3 chat-bubble-me" style="max-width: 85%;">
                            <div class="chat-sender mb-1">You (Initial Request)</div>
                            <div class="chat-text">{{ $inquiry->description }}</div>
                        </div>
                        <span class="text-muted mt-1" style="font-size: 0.7rem;">{{ $inquiry->created_at->format('h:i A') }}</span>
                    </div>

                    @foreach($inquiry->messages as $msg)
                        @php $isMe = $msg->user_id === Auth::id(); @endphp
                        <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }} inquiry-message-item" id="message-{{ $msg->id }}" data-message-id="{{ $msg->id }}">
                            <div class="p-3 {{ $isMe ? 'chat-bubble-me' : 'chat-bubble-other' }}" style="max-width: 85%; transition: all 0.3s ease;">
                                <div class="chat-sender mb-1">
                                    {{ $isMe ? 'You' : ($msg->user->name ?? 'Artist/Admin') }}
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
                <form action="<?= route('customer.inquiries.message', $inquiry->id) ?>" method="POST" enctype="multipart/form-data" class="pt-2 border-top border-secondary border-opacity-50">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <textarea name="message" rows="2" class="form-control bg-dark text-white border-secondary" placeholder="Type a message or question for the artist..." required style="border-radius: 10px 0 0 10px; font-size: 0.85rem;"></textarea>
                        <button type="submit" class="btn btn-gold fw-bold px-3" style="border-radius: 0 10px 10px 0;">
                            Send
                        </button>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <label class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1 py-1 px-2 cursor-pointer" style="font-size: 0.75rem; border-radius: 6px;">
                            <span>📎 Attach Photo</span>
                            <input type="file" name="attachment" accept="image/*" class="d-none">
                        </label>
                        <span class="text-muted" style="font-size: 0.7rem;">Press Enter to send</span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modals Section: Scrollable, Top-Level to prevent clipping -->
@foreach($inquiry->quotations as $quotation)
    @if($quotation->status === 'pending')
        <div class="modal fade" id="acceptModal{{ $quotation->id }}" tabindex="-1" aria-labelledby="acceptModalLabel{{ $quotation->id }}" aria-hidden="true" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: 520px;">
                <div class="modal-content bg-dark border border-secondary text-white shadow-lg" style="border-radius: 18px; overflow: hidden; background: #121212 !important;">
                    <!-- Sticky Header -->
                    <div class="modal-header border-secondary py-3 px-4" style="background: #181818;">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-gold" style="font-size: 1.2rem;">📋</span>
                            <h5 class="modal-title text-gold fw-bold mb-0" id="acceptModalLabel{{ $quotation->id }}">Accept Quote #{{ $quotation->id }}</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="<?= route('customer.quotations.respond', $quotation->id) ?>" method="POST" class="d-flex flex-column" style="margin: 0;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="accept">

                        <!-- Scrollable Body with comfortable padding and max-height -->
                        <div class="modal-body px-4 py-3" style="max-height: calc(80vh - 140px); overflow-y: auto;">
                            <!-- Quote Summary Pill -->
                            <div class="p-3 rounded-3 mb-3 border border-secondary" style="background: rgba(212, 175, 55, 0.08);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted small">Agreed Price Total:</span>
                                    <span class="h4 fw-bold text-gold mb-0">₱{{ number_format($quotation->total_amount, 2) }}</span>
                                </div>
                                <div class="text-muted small mt-1">Artist: <strong class="text-white">{{ $inquiry->artist->name ?? 'Artist' }}</strong> &bull; Service: <strong class="text-white">{{ $inquiry->service_type }}</strong></div>
                            </div>
                            
                            <label class="form-label text-gold small text-uppercase fw-bold mb-2" style="letter-spacing: 0.5px;">Choose how to proceed:</label>
                            
                            <!-- Option 1: Appointment -->
                            <div class="form-check p-3 rounded-3 border border-secondary mb-2" style="background: #1A1A1A; cursor: pointer;">
                                <input class="form-check-input" type="radio" name="conversion_type" id="conv_appt_{{ $quotation->id }}" value="appointment" checked onchange="toggleBookingFields({{ $quotation->id }}, 'appointment')">
                                <label class="form-check-label text-white fw-bold small ms-2 w-100 cursor-pointer" for="conv_appt_{{ $quotation->id }}">
                                    <span style="width:15px;height:15px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('calendar') ?></span> Schedule an Appointment
                                    <div class="text-muted fw-normal mt-1" style="font-size: 0.75rem;">Set a confirmed date and time slot for your session.</div>
                                </label>
                            </div>

                            <!-- Date / Time Inputs -->
                            <div id="bookingFields{{ $quotation->id }}" class="p-3 rounded-3 border border-secondary border-opacity-50 mb-3" style="background: rgba(0,0,0,0.3); margin-left: 1.5rem;">
                                <div class="row g-2">
                                    <div class="col-sm-6">
                                        <label class="form-label text-gold small fw-bold mb-1">Appointment Date:</label>
                                        <input type="date" name="appointment_date" min="<?= date('Y-m-d') ?>" value="{{ $inquiry->preferred_date ? $inquiry->preferred_date->format('Y-m-d') : date('Y-m-d') }}" class="form-control form-control-sm bg-dark text-white border-secondary">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label text-gold small fw-bold mb-1">Preferred Time:</label>
                                        <input type="time" name="appointment_time" value="13:00" class="form-control form-control-sm bg-dark text-white border-secondary">
                                    </div>
                                </div>
                            </div>

                            <!-- Option 2: Live Queue -->
                            <div class="form-check p-3 rounded-3 border border-secondary mb-2" style="background: #1A1A1A; cursor: pointer;">
                                <input class="form-check-input" type="radio" name="conversion_type" id="conv_queue_{{ $quotation->id }}" value="queue" onchange="toggleBookingFields({{ $quotation->id }}, 'queue')">
                                <label class="form-check-label text-white fw-bold small ms-2 w-100 cursor-pointer" for="conv_queue_{{ $quotation->id }}">
                                    🎟️ Join Today's Live Queue (Same-Day)
                                    <div class="text-muted fw-normal mt-1" style="font-size: 0.75rem;">Get a live queue ticket for walk-in service today.</div>
                                </label>
                            </div>

                            <!-- Option 3: Accept Quote Only -->
                            <div class="form-check p-3 rounded-3 border border-secondary" style="background: #1A1A1A; cursor: pointer;">
                                <input class="form-check-input" type="radio" name="conversion_type" id="conv_none_{{ $quotation->id }}" value="none" onchange="toggleBookingFields({{ $quotation->id }}, 'none')">
                                <label class="form-check-label text-white fw-bold small ms-2 w-100 cursor-pointer" for="conv_none_{{ $quotation->id }}">
                                    ✓ Accept Quote Only (Schedule Later)
                                    <div class="text-muted fw-normal mt-1" style="font-size: 0.75rem;">Lock in this agreed price and arrange the date via chat.</div>
                                </label>
                            </div>
                        </div>

                        <!-- Sticky Footer: Always visible, never hidden off screen -->
                        <div class="modal-footer border-secondary py-3 px-4 d-flex justify-content-between align-items-center" style="position: sticky; bottom: 0; background: #181818; z-index: 20;">
                            <button type="button" class="btn btn-outline-secondary px-3 py-2 small" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-gold px-4 py-2 fw-bold text-black" style="border-radius: 10px; font-size: 0.95rem;">
                                Confirm & Accept Quote ✓
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<script>
function toggleBookingFields(quoteId, type) {
    const fields = document.getElementById('bookingFields' + quoteId);
    if (fields) {
        fields.style.display = (type === 'appointment') ? 'block' : 'none';
    }
}

// Auto-scroll to message or quotation if linked with hash (e.g. #message-5 or #quotation-2)
function scrollToHashTarget() {
    const hash = window.location.hash;
    if (!hash) return;

    try {
        const target = document.querySelector(hash);
        if (target) {
            // If target is inside messages-container, ensure container scrolls to it
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

<?php render_customer_layout_end(); ?>
