<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'book';
$presetService = request()->has('service_id') ? \App\Models\Service::find(request('service_id')) : null;
render_customer_layout_start($page, 'Book Appointment & Live Queue');
?>

<div class="p-3">
  <h2 class="page-title mb-1">Book or Join Live Queue</h2>
  <?php if(isset($selectedArtist) && $selectedArtist): ?>
    <p class="page-subtitle mb-4 text-primary">Booking with <strong><?= $selectedArtist->name ?></strong> (<?= $selectedArtist->category ?>)</p>
  <?php else: ?>
    <p class="page-subtitle mb-4">Choose a service, inspect artist availability and queue wait time, then join the live queue or book a session.</p>
  <?php endif; ?>

  <!-- Active Queue Alert Banner (If already in a queue today) -->
  @if($userActiveTicket)
  <div class="alert alert-warning border border-warning bg-dark-subtle p-3 mb-4 rounded-4 d-flex justify-content-between align-items-center">
      <div>
          <strong>You are currently queued!</strong> Ticket <span class="badge bg-warning text-dark fw-bold">#{{ $userActiveTicket->queue_number }}</span> with <strong>{{ $userActiveTicket->artist->name }}</strong>.
          <div class="small text-muted">Status: {{ strtoupper($userActiveTicket->status) }} • Position: #{{ $userActiveTicket->position }} • Est. Wait: ~{{ $userActiveTicket->computeEstimatedWait() }}m</div>
      </div>
      <a href="{{ route('customer.appointments') }}" class="btn btn-warning btn-sm fw-bold rounded-pill px-3">
          Track My Ticket →
      </a>
  </div>
  @endif

  <!-- Step Indicator -->
  <div class="step-indicator mb-4">
    <div class="step-dot active" id="dot1">1</div>
    <div class="step-line" id="line1"></div>
    <div class="step-dot" id="dot2">2</div>
    <div class="step-line" id="line2"></div>
    <div class="step-dot" id="dot3">3</div>
    <div class="step-line" id="line3"></div>
    <div class="step-dot" id="dot4">4</div>
  </div>

  <!-- Step 1: Select Service -->
  <div id="step1">
    <h5 style="font-weight:700;margin-bottom:1.5rem">Step 1: Select a Service</h5>
    
    <?php 
    $servicesColl = collect($services);
    $filteredServices = isset($selectedArtist) && $selectedArtist
      ? $servicesColl->filter(fn($s) => $s->category === $selectedArtist->category)
      : $servicesColl;
      
    $groupedServices = $filteredServices->groupBy('category');
    ?>

    <?php foreach ($groupedServices as $cat => $items): ?>
    <div class="category-section mb-4">
      <h6 style="font-size:0.75rem; text-transform:uppercase; letter-spacing:1px; color:var(--primary); margin-bottom:1rem; padding-bottom:0.5rem; border-bottom:1px solid rgba(212,175,55,0.1)"><?= $cat ?> Specialists</h6>
      <div class="row g-3">
        <?php foreach ($items as $s): ?>
        <div class="col-md-6 col-12">
          <button class="service-btn w-100 text-start p-3" onclick="selectService(<?= $s->id ?>, '<?= addslashes($s->name) ?>', <?= $s->price ?>, '<?= addslashes($s->category) ?>')">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div style="font-weight:600;font-size:0.95rem;margin-bottom:0.25rem"><?= $s->name ?></div>
                <div style="font-size:0.75rem;color:var(--muted-foreground);margin-bottom:0.5rem">⏱ <?= $s->duration ?? '30 min' ?></div>
              </div>
              <div class="service-price" style="font-weight:700; color:var(--primary); font-size:1.1rem;">₱<?= number_format($s->price, 2) ?></div>
            </div>
          </button>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Step 2: Choose Artist with Live Status & Queue -->
  <div id="step2" style="display:none">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 style="font-weight:700;margin-bottom:0">Step 2: Choose Your Artist</h5>
        <span class="small text-muted">Showing live queue length & status</span>
    </div>

    <div class="row g-3">
      <?php foreach ($artists as $a): 
        $queueLength = $a->activeQueue->count();
        $waitMinutes = $a->estimated_wait_minutes;
        $liveStatus = $a->live_status ?? 'available';
        $statusBadge = match($liveStatus) {
            'available' => '<span class="badge bg-success-subtle text-success border border-success" style="font-size:0.65rem;">🟢 AVAILABLE NOW</span>',
            'busy' => '<span class="badge bg-danger-subtle text-danger border border-danger" style="font-size:0.65rem;">🔴 BUSY (IN SERVICE)</span>',
            'on_break' => '<span class="badge bg-warning-subtle text-warning border border-warning" style="font-size:0.65rem;">🟡 ON BREAK</span>',
            'off' => '<span class="badge bg-secondary text-muted border border-secondary" style="font-size:0.65rem;">⚪ OFF DUTY</span>',
            default => '<span class="badge bg-success-subtle text-success">AVAILABLE</span>'
        };
      ?>
      <div class="col-md-6 col-12 artist-card-wrapper" data-category="<?= $a->category ?>">
        <div class="card p-3 border border-secondary h-100" style="background:var(--card); border-radius:16px;">
          <div class="d-flex align-items-center gap-3 mb-3">
            <img src="<?= $a->profile_photo ? (Str::startsWith($a->profile_photo, 'http') ? $a->profile_photo : asset('storage/'.$a->profile_photo)) : ($a->image_url ? (Str::startsWith($a->image_url, 'http') ? $a->image_url : asset('storage/'.$a->image_url)) : 'https://ui-avatars.com/api/?name='.urlencode($a->name).'&background=D4AF37&color=0B0B0B') ?>" 
                 alt="<?= $a->name ?>" 
                 style="width:52px; height:52px; border-radius:50%; object-fit:cover; border:2px solid var(--border)">
            <div class="flex-grow-1">
              <div class="d-flex justify-content-between align-items-start">
                <div style="font-weight:700; font-size:1rem; color:var(--foreground)"><?= $a->name ?></div>
                <?= $statusBadge ?>
              </div>
              <div style="font-size:0.75rem;color:var(--muted-foreground)"><?= is_array($a->specialty) ? implode(', ', $a->specialty) : $a->specialty ?></div>
              <div style="font-size:0.75rem;color:var(--primary)" class="mt-1">⭐ <?= $a->rating ?></div>
            </div>
          </div>

          <!-- Queue Stats Bar -->
          <div class="p-2 px-3 rounded-3 mb-3 d-flex justify-content-between align-items-center" style="background:rgba(255,255,255,0.03); border:1px solid var(--border)">
              <div class="small text-muted">
                  Queue: <strong class="text-white"><?= $queueLength ?> waiting</strong>
              </div>
              <div class="small" style="color:var(--primary)">
                  Est. Wait: <strong>~<?= $waitMinutes ?> min</strong>
              </div>
          </div>

          <!-- Action Buttons: Join Queue vs Schedule Appointment -->
          <div class="d-flex gap-2 mt-auto">
            <?php if($liveStatus !== 'off'): ?>
            <button class="btn btn-gold btn-sm flex-fill py-2 fw-bold" onclick="promptJoinQueue(<?= $a->id ?>, '<?= addslashes($a->name) ?>', <?= $queueLength ?>, <?= $waitMinutes ?>)">
              ⚡ Join Live Queue
            </button>
            <?php else: ?>
            <button class="btn btn-secondary btn-sm flex-fill py-2" disabled>
              Off Duty Today
            </button>
            <?php endif; ?>

            <button class="btn btn-outline-secondary btn-sm flex-fill py-2 text-white d-inline-flex align-items-center justify-content-center gap-2" onclick="selectArtist(<?= $a->id ?>, '<?= addslashes($a->name) ?>', '')">
              <span style="width:16px;height:16px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('calendar') ?></span> Schedule Later
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div id="noArtistsMessage" class="alert alert-warning mt-3" style="display:none;font-size:0.85rem">
      No specialized artists found for this service category.
    </div>
    <button onclick="goStep(1)" class="btn btn-muted mt-4">← Back to Services</button>
  </div>

  <!-- Step 3: Date & Time for Scheduled Appointment -->
  <div id="step3" style="display:none">
    <h5 style="font-weight:700;margin-bottom:1rem">Step 3: Pick Date & Time</h5>
    <div class="row g-3">
      <div class="col-12">
        <label class="form-label text-muted small fw-bold text-uppercase">Select Date</label>
        <input type="date" class="form-control bg-dark text-white border-secondary rounded-pill px-4 py-2" id="bookDate" min="<?= date('Y-m-d') ?>">
      </div>
      <div class="col-12">
        <label class="form-label text-muted small fw-bold text-uppercase">Available Time Slots</label>
        <div id="availableSlots" class="row g-2 mt-1">
           <div class="col-12 text-muted small py-4 px-2 border rounded text-center" style="border-style:dashed !important">
             Please select a date first to calculate available time slots based on artist working hours and queue load.
           </div>
        </div>
        <input type="hidden" id="bookTime">
      </div>
    </div>
    <div class="d-flex gap-2 mt-4">
      <button onclick="goStep(2)" class="btn btn-muted">← Back</button>
      <button id="btnContinueToStep4" onclick="goStep(4)" class="btn btn-gold" disabled>Continue to Confirm →</button>
    </div>
  </div>

  <!-- Step 4: Confirm Booking -->
  <div id="step4" style="display:none">
    <h5 style="font-weight:700;margin-bottom:1rem">Step 4: Confirm Booking</h5>
    <div class="card p-4 mb-3" style="background:var(--card); border:1px solid var(--border); border-radius:16px;">
      <h6 style="font-weight:700;margin-bottom:1rem;color:var(--primary)">Session Details</h6>
      <div class="d-flex justify-content-between mb-2"><span style="color:var(--muted-foreground)">Service</span><span id="sumService" style="font-weight:600">—</span></div>
      <div class="d-flex justify-content-between mb-2"><span style="color:var(--muted-foreground)">Artist</span><span id="sumArtist" style="font-weight:600">—</span></div>
      <div class="d-flex justify-content-between mb-2"><span style="color:var(--muted-foreground)">Date</span><span id="sumDate" style="font-weight:600">—</span></div>
      <div class="d-flex justify-content-between mb-3"><span style="color:var(--muted-foreground)">Time</span><span id="sumTime" style="font-weight:600">—</span></div>
      <div style="border-top:1px solid var(--border);padding-top:0.75rem;display:flex;justify-content:space-between;font-size:1.1rem;font-weight:700">
        <span>Total</span><span id="sumPrice" style="color:var(--primary)">—</span>
      </div>
    </div>
    
    <div id="bookingErrors" class="alert alert-danger d-none small p-2 mb-3"></div>

    <div class="d-flex gap-2">
      <button onclick="goStep(3)" class="btn btn-muted">← Back</button>
      <button id="btnConfirm" onclick="confirmBooking()" class="btn btn-gold flex-grow-1">✅ Confirm Appointment</button>
    </div>
  </div>

  <!-- Success Screen -->
  <div id="stepSuccess" style="display:none;text-align:center;padding:3rem 1rem">
    <div style="font-size:4rem;margin-bottom:1rem">🎉</div>
    <h3 style="font-family:var(--font-bebas);font-size:2rem;color:var(--primary);letter-spacing:0.05em">Appointment Scheduled!</h3>
    <p style="color:var(--muted-foreground);margin-bottom:2rem">Your session has been recorded. We look forward to seeing you!</p>
    <div class="d-flex gap-3 justify-content-center">
      <a href="<?= route('customer.appointments') ?>" class="btn btn-gold px-4">View My Appointments</a>
      <a href="<?= route('customer.home') ?>" class="btn btn-muted px-4">Back Home</a>
    </div>
  </div>
</div>

<!-- MODAL: Confirm Joining Live Queue -->
<div class="modal fade" id="joinQueueModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary" style="background:#141414; border-radius:20px;">
            <div class="modal-header border-secondary">
                <h5 class="modal-title fw-bold" style="color: var(--primary);">Join Live Walk-In Queue</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🎟</div>
                <h4 class="fw-bold mb-1" id="queueModalArtistName">Artist</h4>
                <p class="text-muted small mb-4">You are joining the live same-day walk-in queue for <span id="queueModalServiceName" class="text-white fw-bold"></span>.</p>

                <div class="p-3 rounded-4 mb-4" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border);">
                    <div class="d-flex justify-content-between mb-2 text-muted small">
                        <span>Current Queue Ahead:</span>
                        <strong class="text-white" id="queueModalAheadCount">0 clients</strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Estimated Waiting Time:</span>
                        <strong style="color: var(--primary);" id="queueModalEstWait">~0 mins</strong>
                    </div>
                </div>

                <div id="queueModalError" class="alert alert-danger d-none small p-2 mb-3"></div>

                <button type="button" id="btnConfirmJoinQueue" class="btn btn-gold w-100 py-3 fw-bold rounded-pill text-uppercase" style="letter-spacing:1px;" onclick="executeJoinQueue()">
                    Get My Live Queue Number
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let booking = { 
  service_id: null, 
  serviceName: '', 
  total_price: 0, 
  artist_id: <?= $selectedArtist ? $selectedArtist->id : 'null' ?>, 
  artistName: '<?= $selectedArtist ? $selectedArtist->name : '' ?>', 
  appointment_date: '', 
  appointment_time: '',
  appointment_time_label: ''
};

let queueTarget = {
    artist_id: null,
    artist_name: '',
    ahead: 0,
    wait: 0
};

function selectService(id, name, price, category) {
  booking.service_id = id; 
  booking.serviceName = name; 
  booking.total_price = price;
  
  if (category) {
    let found = 0;
    document.querySelectorAll('#step2 .artist-card-wrapper').forEach(card => {
      if (card.dataset.category === category) {
        card.style.display = '';
        found++;
      } else {
        card.style.display = 'none';
      }
    });
    
    let noMatchEl = document.getElementById('noArtistsMessage');
    if(noMatchEl) noMatchEl.style.display = found === 0 ? 'block' : 'none';
  }

  if (booking.artist_id) {
    goStep(3);
  } else {
    goStep(2);
  }
}

function selectArtist(id, name, role) {
  booking.artist_id = id; 
  booking.artistName = name; 
  goStep(3);
}

function promptJoinQueue(artistId, artistName, ahead, wait) {
    if (!booking.service_id) {
        alert('Please choose a service first.');
        goStep(1);
        return;
    }
    queueTarget.artist_id = artistId;
    queueTarget.artist_name = artistName;
    queueTarget.ahead = ahead;
    queueTarget.wait = wait;

    document.getElementById('queueModalArtistName').innerText = artistName;
    document.getElementById('queueModalServiceName').innerText = booking.serviceName;
    document.getElementById('queueModalAheadCount').innerText = `${ahead} clients ahead`;
    document.getElementById('queueModalEstWait').innerText = `~${wait} mins`;
    document.getElementById('queueModalError').classList.add('d-none');

    new bootstrap.Modal(document.getElementById('joinQueueModal')).show();
}

async function executeJoinQueue() {
    const btn = document.getElementById('btnConfirmJoinQueue');
    const errBox = document.getElementById('queueModalError');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating Queue Ticket...';
    errBox.classList.add('d-none');

    try {
        const res = await fetch('{{ route("customer.queue.join") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                artist_id: queueTarget.artist_id,
                service_id: booking.service_id
            })
        });

        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect || '{{ route("customer.appointments") }}';
        } else {
            throw new Error(data.message || 'Failed to join queue');
        }
    } catch (e) {
        errBox.innerText = e.message;
        errBox.classList.remove('d-none');
        btn.disabled = false;
        btn.innerText = 'Get My Live Queue Number';
    }
}

function selectTime(time, label, btn) {
  booking.appointment_time = time;
  booking.appointment_time_label = label;
  document.querySelectorAll('#availableSlots .day-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('bookTime').value = time;
  document.getElementById('btnContinueToStep4').disabled = false;
}

function goStep(n) {
  [1,2,3,4,'Success'].forEach(s => {
    const el = document.getElementById('step'+s);
    if(el) el.style.display = 'none';
  });
  document.getElementById('step'+n).style.display = '';
  
  for (let i = 1; i <= 4; i++) {
    const dot = document.getElementById('dot'+i);
    if(!dot) continue;
    dot.classList.remove('active','done');
    if (n === 'Success' || i < n) dot.classList.add('done');
    else if (i === n) dot.classList.add('active');
    
    if (i < 4) {
      const line = document.getElementById('line'+i);
      if(line) line.classList.toggle('done', n === 'Success' || i < n);
    }
  }
  
  if (n === 4) {
    booking.appointment_date = document.getElementById('bookDate').value;
    booking.appointment_time = document.getElementById('bookTime').value;

    if (booking.appointment_time) {
        let [hour, minute] = booking.appointment_time.split(':');
        let ampm = hour >= 12 ? 'PM' : 'AM';
        let hour12 = hour % 12;
        hour12 = hour12 ? hour12 : 12;
        booking.appointment_time_label = hour12 + ':' + minute + ' ' + ampm;
    }

    document.getElementById('sumService').textContent = booking.serviceName;
    document.getElementById('sumArtist').textContent = booking.artistName || 'Any Artist';
    
    if (booking.appointment_date) {
        const d = new Date(booking.appointment_date);
        document.getElementById('sumDate').textContent = d.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
    } else {
        document.getElementById('sumDate').textContent = 'Not set';
    }
    
    document.getElementById('sumTime').textContent = booking.appointment_time_label || 'Not set';
    document.getElementById('sumPrice').textContent = '₱' + parseFloat(booking.total_price).toLocaleString();
  }

  if (n === 3) {
      if (document.getElementById('bookDate').value) {
          fetchAvailableSlots();
      }
  }
}

async function fetchAvailableSlots() {
    const date = document.getElementById('bookDate').value;
    const artistId = booking.artist_id;
    const container = document.getElementById('availableSlots');
    const continueBtn = document.getElementById('btnContinueToStep4');

    if (!date || !artistId) return;

    container.innerHTML = '<div class="col-12 text-center py-3"><span class="spinner-border spinner-border-sm text-primary"></span><span class="ms-2 small text-muted">Checking artist availability & queue conflicts...</span></div>';
    continueBtn.disabled = true;

    try {
        const response = await fetch(`<?= route('available-slots') ?>?artist_id=${artistId}&date=${date}`);
        const data = await response.json();
        
        let slots = [];
        let message = 'No available slots for this date. The artist may be off or booked.';
        
        if (Array.isArray(data)) {
            slots = data;
        } else {
            slots = data.slots || [];
            if (data.message) message = data.message;
        }

        container.innerHTML = '';
        if (slots.length === 0) {
            container.innerHTML = `<div class="col-12 alert alert-warning small py-2">${message}</div>`;
        } else {
            slots.forEach(s => {
                const col = document.createElement('div');
                col.className = 'col-4 col-md-3';
                col.innerHTML = `<button type="button" class="day-btn w-100 py-2 ${booking.appointment_time === s.time ? 'active' : ''}" onclick="selectTime('${s.time}', '${s.label}', this)">${s.label}</button>`;
                container.appendChild(col);
            });
            
            if (slots.some(s => s.time === booking.appointment_time)) {
                continueBtn.disabled = false;
            }
        }
    } catch (error) {
        container.innerHTML = '<div class="col-12 alert alert-danger small py-2">Error loading slots.</div>';
    }
}

function updateTimeBounds() {
    const dateInput = document.getElementById('bookDate');
    const now = new Date();
    const pad = n => n < 10 ? '0' + n : n;
    const todayStr = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate());
    
    dateInput.min = todayStr;
    if (!dateInput.value) return;

    const [y, m, d] = dateInput.value.split('-');
    const selectedDate = new Date(y, m - 1, d);
    if (selectedDate.getDay() === 1) {
        alert("The shop is closed on Mondays. Please select another date.");
        dateInput.value = '';
        return;
    }

    fetchAvailableSlots();
}

document.getElementById('bookDate').addEventListener('change', updateTimeBounds);

async function confirmBooking() {
  if (!booking.appointment_date || !booking.appointment_time) {
    alert('Please select both date and time.');
    return;
  }

  const btn = document.getElementById('btnConfirm');
  const errorBox = document.getElementById('bookingErrors');
  
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
  errorBox.classList.add('d-none');

  try {
    const response = await fetch('<?= route('customer.book.store') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
      },
      body: JSON.stringify(booking)
    });

    const data = await response.json();

    if (data.success) {
      goStep('Success');
      if(data.redirect) {
        setTimeout(() => {
          window.location.href = data.redirect;
        }, 1500);
      }
    } else {
      throw new Error(data.message || 'Something went wrong');
    }
  } catch (error) {
    errorBox.textContent = error.message;
    errorBox.classList.remove('d-none');
    btn.disabled = false;
    btn.textContent = '✅ Confirm Appointment';
  }
}

document.addEventListener('DOMContentLoaded', () => {
    <?php if ($presetService): ?>
    selectService(<?= $presetService->id ?>, '<?= addslashes($presetService->name) ?>', <?= $presetService->price ?>, '<?= addslashes($presetService->category) ?>');
    <?php endif; ?>
});
</script>

<?php render_customer_layout_end(); ?>
