<?php
require_once resource_path('views/components/staff-layout.php');
$page = 'inquiries';
render_staff_layout_start($page, 'Inquiries & Quotations');
?>

<div class="container-fluid py-4 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold text-white mb-1" style="letter-spacing: -0.5px;">Client Inquiries & Quotes</h1>
            <p class="text-muted small mb-0">Review custom consultation requests from clients, chat in real-time, and issue itemized quotations.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-gold text-black fw-bold px-3 py-2 rounded-pill">
                Artist: <?= htmlspecialchars($artist->name) ?>
            </span>
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

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="glass-card p-3" style="border-radius: 14px; border: 1px solid var(--border);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">All Inquiries</span>
                    <span class="text-white">📥</span>
                </div>
                <div class="h3 fw-bold text-white mb-0">{{ $inquiries->total() }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="glass-card p-3" style="border-radius: 14px; border: 1px solid rgba(234, 179, 8, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-warning small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pending Review</span>
                    <span class="text-warning">⏳</span>
                </div>
                <div class="h3 fw-bold text-warning mb-0">{{ $pendingCount }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="glass-card p-3" style="border-radius: 14px; border: 1px solid rgba(59, 130, 246, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-info small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Quotations Sent</span>
                    <span class="text-info">📋</span>
                </div>
                <div class="h3 fw-bold text-info mb-0">{{ $quotedCount }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="glass-card p-3" style="border-radius: 14px; border: 1px solid rgba(34, 197, 94, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-success small text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Accepted & Booked</span>
                    <span class="text-success">🎉</span>
                </div>
                <div class="h3 fw-bold text-success mb-0">{{ $acceptedCount }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Pills -->
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="<?= route('staff.inquiries.index') ?>" class="btn btn-sm <?= !request('status') ? 'btn-gold' : 'btn-dark border border-secondary text-white' ?> rounded-pill px-3">
            All Requests
        </a>
        <a href="<?= route('staff.inquiries.index', ['status' => 'pending']) ?>" class="btn btn-sm <?= request('status') === 'pending' ? 'btn-warning text-dark fw-bold' : 'btn-dark border border-secondary text-white' ?> rounded-pill px-3">
            Pending ({{ $pendingCount }})
        </a>
        <a href="<?= route('staff.inquiries.index', ['status' => 'quoted']) ?>" class="btn btn-sm <?= request('status') === 'quoted' ? 'btn-info text-dark fw-bold' : 'btn-dark border border-secondary text-white' ?> rounded-pill px-3">
            Quoted ({{ $quotedCount }})
        </a>
        <a href="<?= route('staff.inquiries.index', ['status' => 'accepted']) ?>" class="btn btn-sm <?= request('status') === 'accepted' ? 'btn-success text-white fw-bold' : 'btn-dark border border-secondary text-white' ?> rounded-pill px-3">
            Accepted ({{ $acceptedCount }})
        </a>
    </div>

    <!-- Inquiries Table -->
    <div class="glass-card overflow-hidden" style="border-radius: 16px; border: 1px solid var(--border);">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="border-bottom border-secondary" style="font-size: 0.75rem; text-transform: uppercase; color: var(--muted-foreground); letter-spacing: 0.5px;">
                        <th class="ps-4">Inquiry / Client</th>
                        <th>Service Type</th>
                        <th>Preferred Date</th>
                        <th>Client Budget</th>
                        <th>Quote Status</th>
                        <th>Received</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($inquiries as $inq)
                        <tr class="border-bottom border-secondary border-opacity-25">
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width: 38px; height: 38px; border-radius: 50%; background: rgba(212,175,55,0.15); color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: bold; border: 1px solid rgba(212,175,55,0.3);">
                                        {{ substr($inq->user->name ?? 'C', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-white fw-bold">{{ $inq->user->name ?? 'Guest Client' }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">#INQ-{{ str_pad($inq->id, 4, '0', STR_PAD_LEFT) }} &bull; {{ $inq->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-dark border border-secondary text-white">{{ $inq->service_type }}</span>
                                @if(!empty($inq->reference_images))
                                    <span class="badge bg-secondary bg-opacity-25 text-gold ms-1">📷 {{ count($inq->reference_images) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($inq->preferred_date)
                                    <span class="text-white fw-semibold">{{ $inq->preferred_date->format('M d, Y') }}</span>
                                @else
                                    <span class="text-muted">Flexible</span>
                                @endif
                            </td>
                            <td>
                                @if($inq->budget)
                                    <strong class="text-gold">₱{{ number_format($inq->budget, 2) }}</strong>
                                @else
                                    <span class="text-muted">Open</span>
                                @endif
                            </td>
                            <td>
                                {!! $inq->status_badge !!}
                                @if($inq->latestQuotation)
                                    <div class="text-gold fw-bold mt-1" style="font-size: 0.8rem;">
                                        ₱{{ number_format($inq->latestQuotation->total_amount, 2) }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-muted">
                                {{ $inq->created_at->diffForHumans() }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= route('staff.inquiries.show', $inq->id) ?>" class="btn btn-sm btn-outline-gold px-3 rounded-pill fw-semibold">
                                    Open & Quote →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="mb-2">📭</div>
                                <div>No inquiries matching this status.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $inquiries->links() }}
    </div>
</div>

<?php render_staff_layout_end(); ?>
