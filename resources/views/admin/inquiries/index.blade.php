<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'inquiries';
render_admin_layout_start($page, 'Client Inquiries & Quotations Oversight');
?>

<div class="container-fluid py-4 px-md-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold text-white mb-1" style="letter-spacing: -0.5px;">Studio Inquiries & Quotation Oversight</h1>
            <p class="text-muted small mb-0">Monitor consultations, quotations, and conversion rates across all artisans and service categories.</p>
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
                    <span class="text-muted small text-uppercase fw-bold" style="font-size: 0.7rem;">Total Inquiries</span>
                    <span class="text-white">📊</span>
                </div>
                <div class="h3 fw-bold text-white mb-0">{{ $totalInquiries }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="glass-card p-3" style="border-radius: 14px; border: 1px solid rgba(234, 179, 8, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-warning small text-uppercase fw-bold" style="font-size: 0.7rem;">Awaiting Quote</span>
                    <span class="text-warning">⏳</span>
                </div>
                <div class="h3 fw-bold text-warning mb-0">{{ $pendingCount }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="glass-card p-3" style="border-radius: 14px; border: 1px solid rgba(59, 130, 246, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-info small text-uppercase fw-bold" style="font-size: 0.7rem;">Quotes Sent</span>
                    <span class="text-info">📋</span>
                </div>
                <div class="h3 fw-bold text-info mb-0">{{ $quotedCount }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="glass-card p-3" style="border-radius: 14px; border: 1px solid rgba(34, 197, 94, 0.3);">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-success small text-uppercase fw-bold" style="font-size: 0.7rem;">Accepted & Booked</span>
                    <span class="text-success">🎉</span>
                </div>
                <div class="h3 fw-bold text-success mb-0">{{ $acceptedCount }}</div>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="glass-card p-3 mb-4" style="border-radius: 14px; border: 1px solid var(--border);">
        <form action="<?= route('admin.inquiries.index') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="artist_id" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()">
                    <option value="">All Artisans / Specialists</option>
                    @foreach($artists as $artist)
                        <option value="{{ $artist->id }}" <?= request('artist_id') == $artist->id ? 'selected' : '' ?>>
                            {{ $artist->name }} ({{ $artist->category }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= request('status') === 'pending' ? 'selected' : '' ?>>Pending Review</option>
                    <option value="quoted" <?= request('status') === 'quoted' ? 'selected' : '' ?>>Quoted</option>
                    <option value="accepted" <?= request('status') === 'accepted' ? 'selected' : '' ?>>Accepted</option>
                    <option value="declined" <?= request('status') === 'declined' ? 'selected' : '' ?>>Declined</option>
                </select>
            </div>
            <div class="col-md-2">
                <a href="<?= route('admin.inquiries.index') ?>" class="btn btn-sm btn-outline-secondary w-100">Clear Filters</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="glass-card overflow-hidden" style="border-radius: 16px; border: 1px solid var(--border);">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0" style="background: transparent;">
                <thead>
                    <tr class="border-bottom border-secondary" style="font-size: 0.75rem; text-transform: uppercase; color: var(--muted-foreground); letter-spacing: 0.5px;">
                        <th class="ps-4">Inquiry / Client</th>
                        <th>Assigned Artist</th>
                        <th>Service Type</th>
                        <th>Client Budget</th>
                        <th>Quotation</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody class="small">
                    @forelse($inquiries as $inq)
                        <tr class="border-bottom border-secondary border-opacity-25">
                            <td class="ps-4">
                                <div class="text-white fw-bold">{{ $inq->user->name ?? 'Guest Client' }}</div>
                                <div class="text-muted" style="font-size: 0.75rem;">#INQ-{{ str_pad($inq->id, 4, '0', STR_PAD_LEFT) }} &bull; {{ $inq->user->email ?? '' }}</div>
                            </td>
                            <td>
                                <span class="text-gold fw-semibold">{{ $inq->artist->name ?? 'Unassigned' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-dark border border-secondary text-white">{{ $inq->service_type }}</span>
                            </td>
                            <td>
                                @if($inq->budget)
                                    <span class="text-gold">₱{{ number_format($inq->budget, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($inq->latestQuotation)
                                    <strong class="text-gold">₱{{ number_format($inq->latestQuotation->total_amount, 2) }}</strong>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $inq->latestQuotation->status }}</div>
                                @else
                                    <span class="text-muted">Not quoted</span>
                                @endif
                            </td>
                            <td>
                                {!! $inq->status_badge !!}
                            </td>
                            <td class="text-muted">
                                {{ $inq->created_at->format('M d, Y') }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="<?= route('admin.inquiries.show', $inq->id) ?>" class="btn btn-sm btn-outline-gold px-3 rounded-pill fw-semibold">
                                    Oversight View →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No inquiries match the selected criteria.
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

<?php render_admin_layout_end(); ?>
