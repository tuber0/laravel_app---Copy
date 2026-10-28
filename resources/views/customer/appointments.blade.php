<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'appointments';
render_customer_layout_start($page, 'My Appointments & Live Queue');
?>

<div class="p-3">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h2 class="page-title mb-1">My Appointments & Queue</h2>
      <p class="page-subtitle mb-0">Track your real-time queue status and scheduled booking history</p>
    </div>
    <a href="<?= route('customer.book') ?>" class="btn btn-gold btn-sm px-3 shadow-sm">+ Book / Join Queue</a>
  </div>

  <!-- LIVE QUEUE TICKET PASS (Phase 2) -->
  <div id="liveQueueSection">
    @if($activeTicket)
    <div class="card p-4 mb-5 border-0 shadow-lg position-relative overflow-hidden" id="queueTicketCard" style="background: linear-gradient(135deg, rgba(212,175,55,0.12) 0%, rgba(20,20,20,0.95) 100%); border: 1px solid rgba(212,175,55,0.4) !important; border-radius: 20px;">
      <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
          <span class="badge bg-gold text-dark fw-bold rounded-pill px-3 py-1 mb-2" style="font-size:0.8rem; letter-spacing:1px;">
            LIVE QUEUE PASS
          </span>
          <h2 class="fw-bold mb-0 text-white" style="font-family:var(--font-bebas); font-size:2.8rem; letter-spacing:2px; color:var(--primary);">
            {{ $activeTicket->queue_number }}
          </h2>
        </div>
        <div class="text-end">
          @if($activeTicket->status === 'in_service')
          <span class="badge badge-inprogress rounded-pill px-3 py-2 text-uppercase fw-bold" id="ticketStatusBadge" style="font-size:0.75rem;">
            🟢 IN CHAIR (SERVING NOW)
          </span>
          @else
          <span class="badge bg-warning-subtle text-warning border border-warning rounded-pill px-3 py-2 text-uppercase fw-bold" id="ticketStatusBadge" style="font-size:0.75rem;">
            ⏳ WAITING IN LINE
          </span>
          @endif
        </div>
      </div>

      <div class="row g-3 my-2 p-3 rounded-4" style="background:rgba(0,0,0,0.4); border:1px solid rgba(255,255,255,0.05);">
        <div class="col-sm-4">
          <div class="text-muted small">Specialist Artist:</div>
          <div class="fw-bold text-white fs-6">{{ $activeTicket->artist->name ?? 'Specialist' }}</div>
          <div class="small text-muted" style="font-size:0.75rem;">{{ $activeTicket->artist->category ?? '' }}</div>
        </div>
        <div class="col-sm-4">
          <div class="text-muted small">Service:</div>
          <div class="fw-bold text-white fs-6">{{ $activeTicket->service->name ?? 'Service' }}</div>
          <div class="small text-muted" style="font-size:0.75rem;">Duration: {{ $activeTicket->service->duration ?? '30 min' }}</div>
        </div>
        <div class="col-sm-4">
          <div class="text-muted small">Queue Position & Wait:</div>
          <div class="fw-bold text-white fs-6" id="ticketPositionText">
            @if($activeTicket->status === 'in_service')
              Serving in Chair Now!
            @elseif($activeTicket->position === 1)
              ⭐ Next in Line!
            @else
              #{{ $activeTicket->position }} in Line
            @endif
          </div>
          <div class="small" style="color:var(--primary);" id="ticketWaitText">
            Est. Wait: ~{{ $activeTicket->computeEstimatedWait() }} mins
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-between align-items-center mt-3 pt-2">
        <div class="small text-muted d-flex align-items-center gap-2">
          <span class="status-pulse-dot available"></span>
          <span>Live updates connected via Reverb / Sync</span>
        </div>
        <button class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold" onclick="cancelQueueTicket({{ $activeTicket->id }})">
          Cancel My Spot
        </button>
      </div>
    </div>
    @endif
  </div>

  <!-- Upcoming Appointments -->
  <h5 style="font-family:var(--font-bebas);letter-spacing:0.05em;color:var(--primary);margin-bottom:1.25rem">Upcoming Scheduled Sessions</h5>

  <?php if ($upcoming->isEmpty()): ?>
  <div class="card p-4 text-center mb-5" style="border-style:dashed; background:transparent">
    <div style="width:36px;height:36px;margin:0 auto 0.5rem;color:#ffffff"><?= icon('calendar') ?></div>
    <p class="mb-0 text-muted">No upcoming appointments scheduled.</p>
  </div>
  <?php else: ?>
  <?php  foreach ($upcoming as $a):
    $statusClass = match ($a->status) {
      'confirmed' => 'badge-confirmed-green',
      'in-progress' => 'badge-inprogress',
      default => 'badge-pending'
    };
    ?>
  <div class="card p-4 mb-3 border-0 shadow-sm" style="background:var(--card); border-radius:16px;">
    <div class="d-flex justify-content-between align-items-start mb-2">
      <div>
        <div style="font-weight:700;font-size:1.1rem"><?= $a->service->name ?? 'Service Removed' ?></div>
        <div style="color:var(--muted-foreground);font-size:0.85rem">with <span
            class="text-white fw-semibold"><?= $a->artist->name ?? 'Artist Removed' ?></span></div>
      </div>
      <span class="badge rounded-pill <?= $statusClass ?>"
        style="font-size:0.7rem;padding:0.4rem 0.8rem;text-transform:uppercase;letter-spacing:0.5px"><?= $a->status ?></span>
    </div>
    <div class="d-flex align-items-center gap-3 mt-2" style="font-size:0.9rem">
      <div style="color:var(--primary)" class="d-inline-flex align-items-center gap-1">
        <span style="width:16px;height:16px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('calendar') ?></span>
        <?= date('M d, Y', strtotime($a->appointment_date)) ?>
      </div>
      <div style="color:var(--muted-foreground)">|</div>
      <div style="color:var(--foreground)" class="d-inline-flex align-items-center gap-1">
        <span style="width:16px;height:16px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('clock') ?></span>
        <?= date('g:i A', strtotime($a->appointment_time)) ?>
      </div>
    </div>
  </div>
  <?php  endforeach; ?>
  <?php endif; ?>

  <!-- Past Appointments -->
  <h5
    style="font-family:var(--font-bebas);letter-spacing:0.05em;color:var(--muted-foreground);margin-top:2rem;margin-bottom:1.25rem">
    Past Experience</h5>

  <?php if ($history->isEmpty()): ?>
  <div class="text-center p-3">
    <p class="small text-muted">No past appointments found.</p>
  </div>
  <?php else: ?>
  <div class="history-list">
    <?php  foreach ($history as $a): ?>
    <div class="d-flex align-items-center gap-3 p-3 mb-2 rounded-3"
      style="background:rgba(255,255,255,0.02);border:1px solid var(--border)">
      <div class="flex-grow-1">
        <div style="font-weight:600;font-size:0.9rem"><?= $a->service->name ?? 'Service Removed' ?></div>
        <div style="font-size:0.75rem;color:var(--muted-foreground)">
          <?= date('M d, Y', strtotime($a->appointment_date)) ?> • <?= $a->artist->name ?? 'Artist Removed' ?>
        </div>
      </div>
      <div class="text-end">
        <div style="font-weight:700;color:var(--primary);font-size:0.9rem">₱<?= number_format($a->total_price, 2) ?>
        </div>
        <div
          style="font-size:0.7rem;text-transform:uppercase;color:<?= $a->status === 'completed' ? 'var(--success)' : 'var(--destructive)' ?>">
          <?= $a->status ?>
        </div>
        <?php if ($a->status === 'completed' && !$a->review): ?>
        <button class="btn btn-gold btn-sm py-1 px-3 mt-1" style="font-size:0.65rem" onclick="openReviewModal(<?= $a->id ?>, <?= $a->artist_id ?>, '<?= addslashes($a->artist->name) ?>')">Review</button>
        <?php endif; ?>
      </div>
    </div>
    <?php  endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content glass-card border-0" style="background:rgba(26,26,26,0.98)">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title" style="font-family:var(--font-bebas);letter-spacing:1px;color:var(--primary)">Review Artist</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="reviewForm">
        <div class="modal-body py-4">
          <p class="text-muted-custom small mb-4">Share your experience with <strong id="reviewArtistName" class="text-white"></strong></p>
          <input type="hidden" id="reviewAppointmentId" name="appointment_id">
          <input type="hidden" id="reviewArtistId" name="artist_id">
          
            <div class="mb-4 text-center">
              <label class="form-label text-muted-custom small fw-bold text-uppercase d-block mb-3">Your Rating</label>
              <div class="position-relative d-inline-block" style="font-size: 3rem; line-height: 1;">
                <div style="color: rgba(255,255,255,0.1); letter-spacing: 2px;">★★★★★</div>
                <div id="starsFilled" style="position: absolute; top: 0; left: 0; color: var(--primary); overflow: hidden; width: 100%; white-space: nowrap; transition: width 0.2s ease; letter-spacing: 2px;">★★★★★</div>
                <input type="range" name="rating" id="ratingInput" min="0.5" max="5" step="0.5" value="5" 
                       style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;"
                       oninput="document.getElementById('starsFilled').style.width = (this.value / 5 * 100) + '%'; document.getElementById('ratingDisplay').textContent = parseFloat(this.value).toFixed(1)">
              </div>
              <div id="ratingDisplay" class="fw-bold text-primary mt-2" style="font-size: 1.5rem;">5.0</div>
            </div>

          <div class="mb-3">
            <label class="form-label text-muted-custom small fw-bold text-uppercase">Your Comment</label>
            <textarea name="comment" class="form-control" rows="3" placeholder="Tell us what you liked (or what we can improve)..."></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-muted px-4" data-bs-dismiss="modal">Close</button>
          <button type="submit" id="btnSubmitReview" class="btn btn-gold px-4">Submit Review</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.star-icon.selected { color: var(--primary) !important; }
.badge-confirmed-green { background: rgba(40, 167, 69, 0.15); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2); }
.status-pulse-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; }
.status-pulse-dot.available { background-color: #28a745; box-shadow: 0 0 6px rgba(40, 167, 69, 0.8); }
</style>

<script>
// Cancel Queue Spot
async function cancelQueueTicket(id) {
    if (!confirm('Are you sure you want to cancel your live queue ticket?')) return;

    try {
        const res = await fetch(`/customer/queue/${id}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            }
        });
        const data = await res.json();
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message || 'Error cancelling queue spot');
        }
    } catch (e) {
        console.error(e);
    }
}

// Real-time Queue Poller & Reverb / Echo Listener
async function checkMyQueueStatus() {
    try {
        const res = await fetch('{{ route("customer.queue.ticket") }}');
        const data = await res.json();
        if (data.success && data.ticket) {
            const t = data.ticket;
            const posEl = document.getElementById('ticketPositionText');
            const waitEl = document.getElementById('ticketWaitText');
            const badgeEl = document.getElementById('ticketStatusBadge');

            if (posEl) {
                if (t.status === 'in_service') {
                    posEl.innerText = "Serving in Chair Now!";
                } else if (t.position === 1) {
                    posEl.innerText = "⭐ Next in Line!";
                } else {
                    posEl.innerText = `#${t.position} in Line`;
                }
            }

            if (waitEl) {
                waitEl.innerText = `Est. Wait: ~${t.estimated_wait_minutes} mins`;
            }

            if (badgeEl) {
                if (t.status === 'in_service') {
                    badgeEl.className = 'badge badge-inprogress rounded-pill px-3 py-2 text-uppercase fw-bold';
                    badgeEl.innerText = "🟢 IN CHAIR (SERVING NOW)";
                } else {
                    badgeEl.className = 'badge bg-warning-subtle text-warning border border-warning rounded-pill px-3 py-2 text-uppercase fw-bold';
                    badgeEl.innerText = "⏳ WAITING IN LINE";
                }
            }
        } else if (document.getElementById('queueTicketCard')) {
            // Ticket was completed or removed
            location.reload();
        }
    } catch (e) {
        console.error(e);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // If Echo is present, listen on user queue channel
    if (window.Echo && {{ auth()->check() ? auth()->id() : 0 }}) {
        window.Echo.channel('user-queue.{{ auth()->id() }}')
            .listen('.QueueUpdated', checkMyQueueStatus);
    }
    // Poll every 6 seconds for live accuracy
    setInterval(checkMyQueueStatus, 6000);
});

function openReviewModal(appointmentId, artistId, artistName) {
    document.getElementById('reviewAppointmentId').value = appointmentId;
    document.getElementById('reviewArtistId').value = artistId;
    document.getElementById('reviewArtistName').textContent = artistName;
    document.getElementById('ratingInput').value = 5;
    document.getElementById('starsFilled').style.width = '100%';
    document.getElementById('ratingDisplay').textContent = '5.0';
    new bootstrap.Modal(document.getElementById('reviewModal')).show();
}

document.getElementById('reviewForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitReview');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('<?= route('customer.reviews.store') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?= csrf_token() ?>'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        if (result.success) {
            location.reload();
        } else {
            alert(result.message || 'Something went wrong');
            btn.disabled = false;
            btn.textContent = 'Submit Review';
        }
    } catch (error) {
        alert('Error submitting review');
        btn.disabled = false;
        btn.textContent = 'Submit Review';
    }
});
</script>
<?php render_customer_layout_end(); ?>