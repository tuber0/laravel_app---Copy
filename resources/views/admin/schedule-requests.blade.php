<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'schedule';
render_admin_layout_start($page, 'Schedule Management');
?>

<div class="dashboard-container p-2 p-md-4">
    <div class="row align-items-center mb-5">
        <div class="col">
            <h1 class="page-title mb-0" style="font-family: var(--font-bebas); letter-spacing: 2px; color: var(--primary); font-size: 2.5rem;">Schedule Requests</h1>
            <p class="text-muted small mb-0">Approve or reject schedule changes requested by the team.</p>
        </div>
    </div>

    <!-- Feedback Alerts -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4 bg-dark-subtle border-success text-success" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="glass-card shadow-sm overflow-hidden" style="border-radius: 20px;">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3 text-muted-foreground small">ARTIST</th>
                                <th class="py-3 text-muted-foreground small">DATE/TIME</th>
                                <th class="py-3 text-muted-foreground small">TYPE</th>
                                <th class="py-3 text-muted-foreground small">REASON</th>
                                <th class="py-3 pe-4 text-end text-muted-foreground small">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold" style="color: var(--primary);">{{ $req->artist->name }}</div>
                                    <div class="small text-muted-foreground">{{ $req->artist->email }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-bold" style="color: var(--foreground);">{{ \Carbon\Carbon::parse($req->date)->format('M d, Y') }}</div>
                                    <div class="small text-muted-foreground">{{ $req->time ? \Carbon\Carbon::parse($req->time)->format('h:i A') : 'ALL DAY' }}</div>
                                </td>
                                <td class="py-3">
                                    <span class="badge {{ $req->type == 'close' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-3 py-1" style="font-size: 0.65rem;">
                                        {{ strtoupper($req->type) }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <div class="small text-muted-foreground" style="max-width: 250px; white-space: normal;">{{ $req->reason ?? 'No note provided.' }}</div>
                                </td>
                                <td class="py-3 pe-4 text-end">
                                    @if($req->status == 'pending')
                                    <div class="d-flex justify-content-end gap-2">
                                        <form action="{{ route('admin.schedule-requests.update', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-gold btn-sm rounded-pill fw-bold small shadow-sm" style="width: 95px; height: 32px; padding: 0; border: 1px solid transparent; display: flex; align-items: center; justify-content: center;">Approve</button>
                                        </form>
                                        <form action="{{ route('admin.schedule-requests.update', $req->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-bold small" style="width: 95px; height: 32px; padding: 0; border: 1px solid #f87171; display: flex; align-items: center; justify-content: center;">Reject</button>
                                        </form>
                                    </div>
                                    @else
                                    @php
                                        $statusClass = [
                                            'approved' => 'badge-inprogress',
                                            'rejected' => 'bg-danger-subtle text-danger'
                                        ][$req->status] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} rounded-pill px-3 py-2" style="font-size: 0.65rem;">
                                        {{ strtoupper($req->status) }}
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted-foreground small">No schedule requests found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-gold { background: var(--primary); color: #000; border: none; font-size: 0.7rem; }
    .btn-outline-danger { color: #f87171; border: 1px solid #f87171; font-size: 0.7rem; }
</style>

<?php render_admin_layout_end(); ?>
