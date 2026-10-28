<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'home';
render_customer_layout_start($page, 'Dashboard');

// Logic for Status Tracker
$statusStep = 1; // Default
$lineWidth = '0%';
if ($activeAppointment) {
    if (in_array($activeAppointment->status, ['confirmed', 'in-progress'])) {
        $statusStep = 2;
        $lineWidth = '50%';
    } elseif ($activeAppointment->status === 'completed') {
        $statusStep = 3;
        $lineWidth = '100%';
    }
}
?>

  <style>
    .specialty-card { position: relative; border-radius: 1rem; overflow: hidden; height: 120px; border: 1px solid var(--border); transition: all 0.4s ease; cursor: pointer; }
    .specialty-default-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.6; transition: opacity 0.4s ease; z-index: 1; }
    .specialty-card:hover .specialty-default-img { opacity: 0; }
    .specialty-slideshow { position: absolute; inset: 0; opacity: 0; transition: opacity 0.4s ease; z-index: 2; }
    .specialty-card:hover .specialty-slideshow { opacity: 1; }
    .specialty-slideshow .slide { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 1s ease; }
    .specialty-slideshow .slide.active { opacity: 1; }
    .specialty-label { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.4); z-index: 5; transition: all 0.3s ease; opacity: 1; pointer-events: none; }
    .specialty-card:hover .specialty-label { opacity: 0; }
  </style>
  
  <div class="p-3">
  <!-- User Profile Header -->
  <div class="glass-card p-4 mb-4 d-flex align-items-center gap-4">
    <div class="position-relative">
      <img src="<?= Auth::user()->profile_picture ? asset('storage/'.Auth::user()->profile_picture).'?v='.time() : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=D4AF37&color=0B0B0B&size=128' ?>" 
           class="profile-avatar shadow" style="width:80px;height:80px;object-fit:cover" alt="Profile">
      <div class="position-absolute bottom-0 end-0 bg-success border border-2 border-dark rounded-circle" style="width:14px;height:14px;transform:translate(25%, 25%)"></div>
    </div>
    <div class="flex-grow-1">
      <div class="d-flex justify-content-between align-items-start">
        <div>
          <h2 class="mb-0" style="font-family:var(--font-bebas);letter-spacing:1px;font-size:1.75rem"><?= $user->name ?></h2>
          <p class="text-muted-custom mb-1" style="font-size:0.9rem"><?= $user->email ?></p>
          <div class="d-flex align-items-center gap-2" style="font-size:0.8rem;color:var(--primary)">
             <span><?= icon('phone') ?></span>
             <span><?= $user->phone ?: 'No phone set' ?></span>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a href="<?= route('customer.book') ?>" class="btn btn-gold btn-sm px-3 rounded-pill text-decoration-none shadow-sm fw-semibold" style="font-size:0.8rem">+ Book / Queue</a>
          <a href="<?= route('customer.appointments') ?>" class="btn btn-muted btn-sm px-3 rounded-pill text-decoration-none" style="font-size:0.75rem;font-weight:600;color:var(--foreground)">My Appointments</a>
          <a href="<?= route('customer.inquiries.index') ?>" class="btn btn-muted btn-sm px-3 rounded-pill text-decoration-none" style="font-size:0.75rem;font-weight:600;color:var(--foreground)">Inquiries & Quotes</a>
          <a href="<?= route('customer.settings') ?>" class="btn btn-muted btn-sm px-3 rounded-pill text-decoration-none" style="font-size:0.75rem;font-weight:600;color:var(--foreground)">Edit Profile</a>
        </div>
      </div>
    </div>
  </div>

  <?php if ($activeAppointment): ?>
  <!-- Active Appointment Tracker -->
  <div class="mb-4">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5 class="mb-0" style="font-family:var(--font-bebas);letter-spacing:0.05em;color:var(--primary)">Active Appointment Status</h5>
        <span class="badge rounded-pill <?= 'badge-'.($activeAppointment->status === 'confirmed' ? 'confirmed' : ($activeAppointment->status === 'pending' ? 'pending' : 'inprogress')) ?>" style="font-size:0.7rem;text-transform:uppercase"><?= $activeAppointment->status ?></span>
    </div>

    <div class="glass-card p-4">
      <div class="status-tracker">
        <div class="status-tracker-line-fill" style="width: <?= $lineWidth ?>"></div>
        
        <!-- Step 1 -->
        <div class="status-step <?= $statusStep >= 1 ? ($statusStep > 1 ? 'completed' : 'active') : '' ?>">
          <div class="status-dot shadow-sm">
            <span class="status-icon"><?= $statusStep > 1 ? icon('check') : icon('clock') ?></span>
          </div>
          <span class="status-label">Pending Approval</span>
        </div>

        <!-- Step 2 -->
        <div class="status-step <?= $statusStep >= 2 ? ($statusStep > 2 ? 'completed' : 'active') : '' ?>">
          <div class="status-dot shadow-sm">
            <span class="status-icon"><?= $statusStep > 2 ? icon('check') : icon('user') ?></span>
          </div>
          <span class="status-label">Approved</span>
        </div>

        <!-- Step 3 -->
        <div class="status-step <?= $statusStep >= 3 ? 'active' : '' ?>">
          <div class="status-dot shadow-sm">
            <span class="status-icon"><?= icon('flag') ?></span>
          </div>
          <span class="status-label">Completed</span>
        </div>
      </div>

      <hr class="my-4" style="border-color:rgba(255,255,255,0.05)">

      <!-- Appointment Details Card -->
      <div class="info-grid">
        <div class="info-item">
            <div class="label">Service & Artist</div>
            <div class="value"><?= $activeAppointment->service->name ?? 'Service Removed' ?> <span class="text-muted-custom mx-1">•</span> <span style="color:var(--primary)">with <?= $activeAppointment->artist->name ?? 'Artist Removed' ?></span></div>
        </div>
        <div class="info-item">
            <div class="label">Date & Time</div>
            <div class="value d-flex align-items-center gap-1 flex-wrap">
              <span style="width:16px;height:16px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('calendar') ?></span>
              <?= date('M d, Y', strtotime($activeAppointment->appointment_date)) ?> 
              <span class="mx-1 text-muted-custom">at</span> 
              <span style="width:16px;height:16px;color:#ffffff;display:inline-block;vertical-align:-2px;"><?= icon('clock') ?></span>
              <?= $activeAppointment->appointment_time ?>
            </div>
        </div>
        <div class="info-item">
            <div class="label">Total Price</div>
            <div class="value" style="font-size:1.1rem;color:var(--primary)">₱<?= number_format($activeAppointment->total_price, 2) ?></div>
        </div>
        <div class="info-item">
            <div class="label">Payment Method</div>
            <div class="value">Cash on Shop</div>
        </div>
      </div>

      <div class="d-flex gap-3 mt-4 pt-2">
         <button type="button" class="btn btn-gold flex-fill py-2 rounded-3 shadow-sm" style="font-size:0.85rem;font-weight:600" data-bs-toggle="modal" data-bs-target="#rescheduleModal">Reschedule</button>
         <button type="button" class="btn btn-outline-gold flex-fill py-2 rounded-3" style="font-size:0.85rem;background:rgba(220,38,38,0.1);color:var(--destructive);border-color:rgba(220,38,38,0.2)" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel</button>
      </div>
    </div>
  </div>
  <?php else: ?>
  <!-- Empty State -->
  <div class="glass-card p-5 text-center mb-4">
    <div style="font-size:3rem;margin-bottom:1rem">✨</div>
    <h4 style="font-family:var(--font-bebas);letter-spacing:1px">No Active Bookings</h4>
    <p class="text-muted-custom mb-4">Ready for a fresh look? Book your next artisan experience today.</p>
    <a href="<?= route('customer.book') ?>" class="btn btn-gold px-5 py-2 shadow-lg">Book Now</a>
  </div>
  <?php endif; ?>

  <!-- Appointment Overview -->
  <div class="row g-3 mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h5 class="mb-0" style="font-family:var(--font-bebas);letter-spacing:0.05em;color:var(--muted-foreground)">Appointment Overview</h5>
        <a href="<?= route('customer.appointments') ?>" class="text-primary text-decoration-none fw-semibold" style="font-size:0.85rem">View All →</a>
    </div>
    <div class="col-6">
      <div class="glass-card p-3 text-center h-100">
        <div style="width:26px;height:26px;margin:0 auto 0.25rem;color:#ffffff;display:flex;align-items:center;justify-content:center;"><?= icon('calendar') ?></div>
        <div style="font-weight:700;font-size:1.25rem;color:var(--primary)"><?= $upcomingCount ?></div>
        <div class="text-muted-custom small text-uppercase fw-bold" style="letter-spacing:0.5px;font-size:0.65rem">Upcoming</div>
      </div>
    </div>
    <div class="col-6">
      <div class="glass-card p-3 text-center h-100">
        <div style="font-size:1.5rem;margin-bottom:0.25rem">📜</div>
        <div style="font-weight:700;font-size:1.25rem;color:var(--foreground)"><?= $totalCount ?></div>
        <div class="text-muted-custom small text-uppercase fw-bold" style="letter-spacing:0.5px;font-size:0.65rem">Total Bookings</div>
      </div>
    </div>
  </div>

  <!-- Reschedule Modal -->
  <?php if ($activeAppointment): ?>
  <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-card border-0" style="background:rgba(26,26,26,0.98)">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title" style="font-family:var(--font-bebas);letter-spacing:1px;color:var(--primary)">Reschedule Appointment</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="rescheduleForm">
          <div class="modal-body py-4">
            <div class="mb-3">
              <label class="form-label text-muted-custom small fw-bold text-uppercase">New Date</label>
              <input type="date" name="appointment_date" class="form-control" value="<?= $activeAppointment->appointment_date ?? date('Y-m-d') ?>" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label text-muted-custom small fw-bold text-uppercase">New Time</label>
              <input type="time" name="appointment_time" class="form-control" value="<?= isset($activeAppointment->appointment_time) ? date('H:i', strtotime($activeAppointment->appointment_time)) : '10:00' ?>" required>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-muted px-4" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-gold px-4">Update Appointment</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Cancel Modal -->
  <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content glass-card border-0" style="background:rgba(26,26,26,0.98)">
        <div class="modal-body p-4 text-center">
          <div style="font-size:3rem;margin-bottom:1rem">⚠️</div>
          <h5 style="font-family:var(--font-bebas);letter-spacing:1px;color:var(--secondary)">Cancel Appointment?</h5>
          <p class="text-muted-custom small mb-4">Are you sure you want to cancel this appointment? This action cannot be undone.</p>
          <div class="d-flex gap-2">
             <button type="button" class="btn btn-muted flex-fill" data-bs-dismiss="modal">No, Keep</button>
             <button type="button" class="btn btn-red flex-fill" onclick="confirmCancel(<?= $activeAppointment->id ?>)">Yes, Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Studio Showcase Preview -->
  <div class="row g-3 mb-4">
    <div class="col-12">
        <h5 style="font-family:var(--font-bebas);letter-spacing:0.05em;color:var(--muted-foreground)">Our Specialty</h5>
    </div>
    <?php
    $displayCats = [
      ['name' => 'Barbering', 'category' => 'Barbering', 'icon' => '💈', 'img' => 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400&q=80'],
      ['name' => 'Tattoo Art', 'category' => 'Tattoo Art', 'icon' => '🖋️', 'img' => 'https://images.unsplash.com/photo-1598371839696-5c5bb00bdc28?w=400&q=80'],
    ];
    foreach($displayCats as $cat): 
      $images = $categoryImages->get($cat['category']) ?: collect();
    ?>
    <div class="col-6">
      <a href="<?= route('customer.services', ['cat' => $cat['category']]) ?>" class="text-decoration-none">
        <div class="specialty-card" onmouseenter="startSlideshow(this)" onmouseleave="stopSlideshow(this)">
          <img src="<?= $cat['img'] ?>" class="specialty-default-img" alt="Default">
          <div class="specialty-slideshow">
            <?php foreach($images as $idx => $img): ?>
              <img src="<?= asset('storage/'.$img) ?>" class="slide <?= $idx === 0 ? 'active' : '' ?>" alt="Sample work">
            <?php endforeach; ?>
          </div>
          <div class="specialty-label">
             <div class="text-center">
               <div style="font-size:1.5rem;margin-bottom:0.25rem"><?= $cat['icon'] ?></div>
               <span style="font-family:var(--font-bebas);font-size:1.1rem;letter-spacing:1px;color:white"><?= $cat['name'] ?></span>
             </div>
          </div>
        </div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Quick Services -->
  <h5 style="font-family:var(--font-bebas);letter-spacing:0.05em;color:var(--muted-foreground);margin-bottom:1.25rem">Popular Services</h5>
  <div class="row g-3 mb-4">
    <?php
    $icons = [
      'Barbering' => '💈',
      'Tattoo Art' => '🖋️',
      'Piercing' => '💎',
      'Hair Styling' => '💇'
    ];
    foreach ($popularServices as $s): 
      $icon = $icons[$s->category] ?? '✨';
    ?>
    <div class="col-4">
      <a href="<?= route('customer.services', ['cat' => $s->category]) ?>" class="quick-action-card p-3" style="text-align:center">
        <div style="font-size:1.5rem;margin-bottom:0.5rem"><?= $icon ?></div>
        <div style="font-weight:600;font-size:0.75rem;margin-bottom:0.25rem"><?= $s->name ?></div>
        <div style="font-size:0.7rem;color:var(--primary)">₱<?= number_format($s->price, 2) ?></div>
      </a>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Studio Location -->
  <div class="glass-card p-4 mt-4 mb-4">
      <div class="d-flex align-items-center gap-3">
          <div style="background:rgba(212,175,55,0.1);color:var(--primary);border-radius:50%;width:40px;height:40px;display:flex;align-items:center;justify-content:center;font-size:1.2rem">📍</div>
          <div class="flex-grow-1">
              <h6 class="mb-1" style="font-family:var(--font-bebas);letter-spacing:1px;color:var(--primary)">Visit Our Studio</h6>
              <a href="https://www.google.com/maps/search/?api=1&query=208+Tubuan+2+Bypass+Road+Silang+Cavite+04118" target="_blank" class="text-decoration-none d-block" style="color:var(--muted-foreground);font-size:0.85rem">
                  208 Tubuan 2 Bypass Road, Silang, Cavite 04118
              </a>
          </div>
          <a href="https://www.google.com/maps/search/?api=1&query=208+Tubuan+2+Bypass+Road+Silang+Cavite+04118" target="_blank" class="btn btn-outline-gold btn-sm rounded-pill px-3" style="font-size:0.7rem">Open Maps</a>
      </div>
  </div>
</div>

<?php render_customer_layout_end(); ?>

<script>
let slideshowIntervals = new Map();

function startSlideshow(card) {
    const slides = card.querySelectorAll('.specialty-slideshow .slide');
    if (slides.length <= 1) return;

    let currentIdx = 0;
    const interval = setInterval(() => {
        slides[currentIdx].classList.remove('active');
        currentIdx = (currentIdx + 1) % slides.length;
        slides[currentIdx].classList.add('active');
    }, 2000);
    
    slideshowIntervals.set(card, interval);
}

function stopSlideshow(card) {
    const interval = slideshowIntervals.get(card);
    if (interval) {
        clearInterval(interval);
        slideshowIntervals.delete(card);
        
        // Reset to first slide
        const slides = card.querySelectorAll('.specialty-slideshow .slide');
        slides.forEach((s, idx) => s.classList.toggle('active', idx === 0));
    }
}

// Existing reschedule logic...
document.getElementById('rescheduleForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const appointmentId = <?= $activeAppointment->id ?? 0 ?>;
    
    fetch(`/customer/appointments/${appointmentId}/reschedule`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});

// Handle Cancel Confirmation
function confirmCancel(id) {
    fetch(`/customer/appointments/${id}/cancel`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Something went wrong');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>
