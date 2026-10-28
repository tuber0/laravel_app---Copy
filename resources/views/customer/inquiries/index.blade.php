<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'inquiries';
render_customer_layout_start($page, 'My Inquiries & Quotations');
?>

<div class="container-fluid py-4 px-md-4">
    <!-- Header with Action -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold text-white mb-1" style="letter-spacing: -0.5px;">Custom Inquiries & Quotes</h1>
            <p class="text-muted small mb-0">Consult with artists, review custom price quotes, and convert them directly into bookings.</p>
        </div>
        <a href="<?= route('customer.inquiries.create') ?>" class="btn btn-gold d-inline-flex align-items-center gap-2 fw-semibold px-4 py-2" style="border-radius: 10px;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 18px; height: 18px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>New Custom Inquiry</span>
        </a>
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

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 bg-info bg-opacity-25 text-white mb-4" role="alert" style="border-radius: 12px;">
            <div>{{ session('info') }}</div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Inquiries List -->
    @if($inquiries->isEmpty())
        <div class="glass-card text-center py-5 px-4 my-4" style="border-radius: 16px; border: 1px dashed rgba(255,255,255,0.15);">
            <div class="mb-3" style="width: 64px; height: 64px; margin: 0 auto; background: rgba(212, 175, 55, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--primary);">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 32px; height: 32px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 011.037-.443 48.282 48.282 0 005.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                </svg>
            </div>
            <h4 class="text-white fw-bold mb-2">No Inquiries Submitted Yet</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 480px;">Have a custom tattoo idea, piercing specification, or specialized styling in mind? Submit an inquiry with reference photos to get an itemized quote directly from our artists.</p>
            <a href="<?= route('customer.inquiries.create') ?>" class="btn btn-gold px-4 py-2 fw-semibold" style="border-radius: 10px;">
                Start Your First Inquiry
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($inquiries as $inquiry)
                <div class="col-12">
                    <div class="glass-card p-4 transition-all" style="border-radius: 16px; border: 1px solid var(--border); background: rgba(255,255,255,0.02);">
                        <div class="row align-items-center g-3">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $inquiry->artist && $inquiry->artist->image_url ? (\Illuminate\Support\Str::startsWith($inquiry->artist->image_url, 'http') ? $inquiry->artist->image_url : asset('storage/' . $inquiry->artist->image_url)) : 'https://via.placeholder.com/80' ?>" 
                                         alt="<?= $inquiry->artist->name ?? 'Artist' ?>" 
                                         style="width: 54px; height: 54px; border-radius: 12px; object-fit: cover; border: 1.5px solid var(--primary);">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <span class="badge bg-dark border border-secondary text-white" style="font-size: 0.7rem; letter-spacing: 0.5px;">#INQ-{{ str_pad($inquiry->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            {!! $inquiry->status_badge !!}
                                        </div>
                                        <h5 class="text-white mb-0 fw-bold">{{ $inquiry->service_type }}</h5>
                                        <div class="text-muted small">Artist: <span class="text-white">{{ $inquiry->artist->name ?? 'Unassigned' }}</span></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="text-muted small mb-1">Description & Request:</div>
                                <div class="text-white small text-truncate" style="max-width: 380px;">{{ $inquiry->description }}</div>
                                <div class="d-flex align-items-center gap-3 mt-2 text-muted" style="font-size: 0.75rem;">
                                    @if($inquiry->preferred_date)
                                        <span><span style="width:14px;height:14px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('calendar') ?></span> Pref: <strong class="text-white">{{ $inquiry->preferred_date->format('M d, Y') }}</strong></span>
                                    @endif
                                    @if($inquiry->budget)
                                        <span>💰 Budget: <strong class="text-gold">₱{{ number_format($inquiry->budget, 2) }}</strong></span>
                                    @endif
                                    @if(!empty($inquiry->reference_images))
                                        <span>📷 {{ count($inquiry->reference_images) }} Ref Images</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-4 text-md-end">
                                @if($inquiry->latestQuotation)
                                    <div class="mb-2">
                                        <span class="text-muted small d-block">Quoted Amount:</span>
                                        <span class="h4 fw-bold text-gold mb-0">₱{{ number_format($inquiry->latestQuotation->total_amount, 2) }}</span>
                                        <div class="mt-1">{!! $inquiry->latestQuotation->status_badge !!}</div>
                                    </div>
                                @else
                                    <div class="text-muted small mb-2">Awaiting artist price assessment</div>
                                @endif
                                <a href="<?= route('customer.inquiries.show', $inquiry->id) ?>" class="btn btn-sm btn-outline-gold px-3 py-2 rounded-pill fw-semibold">
                                    View Thread & Quote →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $inquiries->links() }}
        </div>
    @endif
</div>

<?php render_customer_layout_end(); ?>
