<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pedro's Artisan One-Stop Shop</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">

  <style>
    .service-card-landing { transition: all 0.4s ease; cursor: pointer; }
    .service-slideshow { position: absolute; inset: 0; opacity: 0; transition: opacity 0.4s ease; z-index: 1; }
    .service-card-landing:hover .service-slideshow { opacity: 1; }
    .service-card-landing:hover .service-content { transform: translateY(-5px); z-index: 2; position: relative; }
    .service-content { transition: all 0.4s ease; background: rgba(11,11,11,0.2); height: 100%; padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; backdrop-filter: blur(1px); }
    .service-card-landing:hover .service-content { background: rgba(11,11,11,0.4); }
    .service-slideshow .slide { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 1s ease; }
    .service-slideshow .slide.active { opacity: 1; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav style="background:rgba(11,11,11,0.95);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:100;padding:1rem 0;backdrop-filter:blur(8px);">
  <div class="container d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <img src="{{ asset('assets/img/logo.jpg') }}?v={{ time() }}" alt="Pedro's Artisan Logo" style="height: 48px; border-radius: 50%; border: 2px solid var(--primary);">
      <div>
        <div class="brand-name">PEDRO'S ARTISAN</div>
        <div class="brand-sub">One-Stop Shop</div>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      @guest
        <a href="{{ route('register') }}" class="nav-link-custom px-3" style="font-size:0.85rem;">Join Us</a>
        <a href="{{ route('login') }}" class="nav-link-custom px-3" style="font-size:0.85rem;">Login</a>
        <a href="{{ route('login') }}" class="btn btn-gold px-4 py-2" style="font-size:0.85rem; border-radius:30px;">Book Now</a>
      @else
        <div class="d-none d-lg-flex align-items-center gap-4 me-3">
          <a href="{{ route('public.index') }}" class="nav-link-custom {{ Route::currentRouteName() == 'public.index' ? 'active' : '' }}">Home</a>
          @if(Auth::user()->isCustomer())
            <a href="{{ route('customer.home') }}" class="nav-link-custom">Dashboard</a>
            <a href="{{ route('customer.book') }}" class="nav-link-custom">Book</a>
            <a href="{{ route('customer.appointments') }}" class="nav-link-custom">Appointments</a>
            <a href="{{ route('customer.inquiries.index') }}" class="nav-link-custom">Inquiries</a>
            <a href="{{ route('customer.artists') }}" class="nav-link-custom">Artist</a>
            <a href="{{ route('customer.services') }}" class="nav-link-custom">Service</a>
          @else
            <a href="{{ route('admin.dashboard') }}" class="nav-link-custom px-3 py-1 rounded-pill" style="background:rgba(212,175,55,0.1); border:1px solid rgba(212,175,55,0.2); color:var(--primary); font-weight:600">Admin Panel</a>
          @endif
        </div>

        <div class="dropdown">
          <a href="#" class="profile-dropdown-toggle d-flex align-items-center gap-2 text-decoration-none p-1 rounded-pill" 
             style="background:rgba(255,255,255,0.05); border:1px solid var(--border); padding-right:1rem !important"
             data-bs-toggle="dropdown" aria-expanded="false">
            <img src="{{ Auth::user()->profile_picture ? asset('storage/'.Auth::user()->profile_picture).'?v='.time() : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=D4AF37&color=0B0B0B&size=64' }}" 
                 style="width:32px;height:32px;border-radius:50%;object-fit:cover" alt="Profile">
            <span class="d-none d-sm-inline" style="color:var(--foreground); font-size:0.85rem; font-weight:600">{{ explode(' ', Auth::user()->name)[0] }}</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-premium">
            <li><a class="dropdown-item dropdown-item-premium" href="{{ route('public.index') }}">Home</a></li>
            <li><a class="dropdown-item dropdown-item-premium" href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : (Auth::user()->isStaff() ? route('appointments.index') : route('customer.home')) }}">Dashboard</a></li>
            @if(Auth::user()->isCustomer())
              <li><a class="dropdown-item dropdown-item-premium" href="{{ route('customer.appointments') }}">My Appointments</a></li>
              <li><a class="dropdown-item dropdown-item-premium" href="{{ route('customer.inquiries.index') }}">Inquiries & Quotes</a></li>
              <li><a class="dropdown-item dropdown-item-premium" href="{{ route('customer.reviews') }}">My Reviews</a></li>
              <li><a class="dropdown-item dropdown-item-premium" href="{{ route('customer.settings') }}">Setting</a></li>
            @endif
            <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.05)"></li>
            <li>
              <a class="dropdown-item dropdown-item-premium text-danger" href="{{ route('logout') }}" 
                 onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                 Logout
              </a>
            </li>
          </ul>
        </div>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
          @csrf
        </form>
      @endguest
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero-section">
  <div class="container position-relative" style="z-index:1">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="hero-glass-box">
          <p style="color:var(--primary);font-family:var(--font-montserrat);font-size:0.9rem;letter-spacing:0.15em;text-transform:uppercase;margin-bottom:1rem;">Premium Artisan Services</p>
        <h1 class="hero-tagline mb-3">
          Trimmed, Pierced,<br>and <span>Inked to</span><br>Perfection.
        </h1>
        <p style="color:var(--muted-foreground);font-family:var(--font-montserrat);font-size:1rem;max-width:480px;margin-bottom:2rem;">
          Fresh Fade? New Ink? Box Braids? Book your session today and experience premium artisan services under one roof.
        </p>
          <div class="d-flex gap-3 flex-wrap">
            <a href="{{ route('customer.book') }}" class="btn btn-gold px-4 py-3" style="font-size:1rem;">Book Appointment</a>
            <a href="{{ route('customer.artists') }}" class="btn btn-outline-gold px-4 py-3" style="font-size:1rem;color:#fff;border-color:var(--primary);">View Artists</a>
          </div>
        </div>
      </div>
      <div class="col-lg-5 d-none d-lg-block text-end">
        <div style="font-family:var(--font-bebas);font-size:9rem;line-height:1;color:rgba(212,175,55,0.08);user-select:none;">PA</div>
      </div>
    </div>
  </div>
</section>

<!-- Services -->
<section style="padding:5rem 0;background:var(--background);">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">Our <span>Services</span></h2>
      <p style="color:var(--muted-foreground);font-family:var(--font-montserrat)">Everything you need, all in one place.</p>
    </div>
    <div class="row g-4">
      <?php
      $services = [
        ['icon' => '💈', 'name' => 'Barbering',     'category' => 'Barbering',   'desc' => 'Classic cuts, fades, beard trims & hot towel shaves by master barbers.'],
        ['icon' => '🪢', 'name' => 'Braiding',       'category' => 'Hair Styling', 'desc' => 'Box braids, cornrows, twists & protective styles by expert braiders.'],
        ['icon' => '🖋️', 'name' => 'Tattoo',         'category' => 'Tattoo Art',  'desc' => 'Custom designs, realism, black & grey by award-winning artists.'],
        ['icon' => '💎', 'name' => 'Piercing',        'category' => 'Piercing',    'desc' => 'Ear, nose & body piercings with certified professionals.'],
        ['icon' => '🧢', 'name' => 'Cap Cleaning',   'category' => 'Other',       'desc' => 'Deep clean & restoration for your favorite caps and sneakers.'],
      ];
      foreach ($services as $s): 
        $images = $categoryImages->get($s['category']) ?: collect();
      ?>
      <div class="col-6 col-md-4 col-lg">
        <div class="service-card-landing h-100 position-relative overflow-hidden" onmouseenter="startSlideshow(this)" onmouseleave="stopSlideshow(this)">
          <div class="service-slideshow">
            <?php foreach($images as $idx => $img): ?>
              <img src="<?= asset('storage/'.$img) ?>" class="slide <?= $idx === 0 ? 'active' : '' ?>" alt="Work sample">
            <?php endforeach; ?>
            <?php if($images->isEmpty()): ?>
              <div class="slide active d-flex align-items-center justify-content-center bg-dark" style="font-size:2rem;opacity:0.3"><?= $s['icon'] ?></div>
            <?php endif; ?>
          </div>
          <div class="service-content">
            <div class="service-icon"><?= $s['icon'] ?></div>
            <h5 style="font-family:var(--font-bebas);font-size:1.3rem;letter-spacing:0.05em;color:var(--primary)"><?= $s['name'] ?></h5>
            <p style="color:var(--muted-foreground);font-size:0.85rem;font-family:var(--font-montserrat)"><?= $s['desc'] ?></p>
            <a href="{{ route('customer.book') }}" class="btn btn-gold btn-sm mt-2">Book Now</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- How It Works -->
<section style="padding:5rem 0;background:var(--card);">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">How It <span>Works</span></h2>
    </div>
    <div class="row g-4 text-center">
      <div class="col-md-4">
        <div class="step-number">1</div>
        <h5 style="font-family:var(--font-bebas);font-size:1.3rem;color:var(--primary)">Choose a Service</h5>
        <p style="color:var(--muted-foreground);font-family:var(--font-montserrat);font-size:0.9rem">Browse our services and pick the one that fits your style.</p>
      </div>
      <div class="col-md-4">
        <div class="step-number">2</div>
        <h5 style="font-family:var(--font-bebas);font-size:1.3rem;color:var(--primary)">Pick Your Artist</h5>
        <p style="color:var(--muted-foreground);font-family:var(--font-montserrat);font-size:0.9rem">Select from our talented team of specialists.</p>
      </div>
      <div class="col-md-4">
        <div class="step-number">3</div>
        <h5 style="font-family:var(--font-bebas);font-size:1.3rem;color:var(--primary)">Confirm & Arrive</h5>
        <p style="color:var(--muted-foreground);font-family:var(--font-montserrat);font-size:0.9rem">Book your slot and show up — we'll take care of the rest.</p>
      </div>
    </div>
  </div>
</section>

<!-- Featured Artists -->
<section style="padding:5rem 0;background:var(--background);">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">Meet Our <span>Artists</span></h2>
    </div>
    <div class="row g-4">
      <?php foreach ($artists as $a): ?>
      <div class="col-md-4">
        <div class="card" style="overflow:hidden">
          <div style="position:relative">
            <img src="<?= \Illuminate\Support\Str::startsWith($a->image_url, 'http') ? $a->image_url : ($a->image_url ? asset('storage/' . $a->image_url) : 'https://via.placeholder.com/400x200/1a1a1a/D4AF37?text=Artist') ?>" alt="<?= $a->name ?>" style="width:100%;height:220px;object-fit:cover;">
            <div class="artist-badge" style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);padding:0.25rem 0.6rem;border-radius:12px;font-size:0.75rem;color:#fff;border:1px solid rgba(255,255,255,0.1)">⭐ <?= number_format($a->rating ?? 4.9, 1) ?></div>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-1">
              <h5 style="font-weight:700;margin-bottom:0"><?= $a->name ?></h5>
              <span class="text-gold fw-bold" style="font-size:0.8rem;">From ₱<?= number_format($a->starting_rate ?? 300, 0) ?></span>
            </div>
            <p style="color:var(--primary);font-size:0.85rem;margin-bottom:0.25rem"><?= $a->category ?> &bull; <?= $a->years_of_experience ?? 5 ?> yrs exp</p>
            <p style="color:var(--muted-foreground);font-size:0.8rem;margin-bottom:0.75rem"><?= is_array($a->specialties ?: $a->specialty) ? implode(', ', array_slice($a->specialties ?: $a->specialty, 0, 2)) : ($a->specialties ?: $a->specialty) ?> &bull; <?= $a->reviews_count ?> reviews</p>
            <div class="d-flex gap-2">
              <a href="{{ route('customer.artists.portfolio', $a->id) }}" class="btn btn-outline-gold btn-sm flex-grow-1" style="font-size:0.75rem;">View Work</a>
              <a href="{{ route('customer.book', ['artist_id' => $a->id]) }}" class="btn btn-gold btn-sm flex-grow-1" style="font-size:0.75rem;">Book Now</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <div class="text-center mt-4">
      <a href="{{ route('customer.artists') }}" class="btn btn-outline-gold px-4">View All Artists</a>
    </div>
  </div>
</section>

<!-- Reviews -->
<section style="padding:5rem 0;background:var(--card);">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">What Clients <span>Say</span></h2>
    </div>
    <div class="row g-4">
      <?php if($recentReviews->isEmpty()): ?>
        <div class="col-12 text-center py-4">
          <p class="text-muted-custom">No reviews yet. Be the first to share your experience!</p>
        </div>
      <?php else: ?>
        <?php foreach ($recentReviews as $r): ?>
        <div class="col-md-4">
          <div class="review-card">
            <div class="stars mb-2 d-flex gap-1">
              <?php for ($i = 1; $i <= 5; $i++): ?>
                <div style="position: relative; font-size: 1rem; line-height: 1;">
                  <span style="color: rgba(255,255,255,0.1);">★</span>
                  <?php 
                  $fill = 0;
                  if ($r->rating >= $i) $fill = 100;
                  elseif ($r->rating > $i - 1) $fill = ($r->rating - ($i - 1)) * 100;
                  ?>
                  <span style="position: absolute; top: 0; left: 0; color: var(--primary); overflow: hidden; width: <?= $fill ?>%; white-space: nowrap;">★</span>
                </div>
              <?php endfor; ?>
            </div>
            <p style="color:var(--foreground);font-size:0.9rem;font-family:var(--font-montserrat);margin-bottom:1rem">"<?= htmlspecialchars($r->comment) ?: 'No comment provided.' ?>"</p>
            <div class="d-flex align-items-center justify-content-between mt-auto">
               <div style="font-weight:700;font-size:0.85rem">— <?= htmlspecialchars($r->user->name) ?></div>
               <div style="font-size:0.7rem;color:var(--primary)">via <span class="text-white"><?= htmlspecialchars($r->artist->name) ?></span></div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- Footer -->
<footer style="background:var(--sidebar);border-top:1px solid var(--border);padding:3rem 0;">
  <div class="container">
    <div class="row g-4">
      <div class="col-md-4">
        <div class="d-flex align-items-center gap-3 mb-3">
          <img src="{{ asset('assets/img/logo.jpg') }}?v={{ time() }}" alt="Pedro's Artisan Logo" style="height: 48px; border-radius: 50%; border: 2px solid var(--primary);">
          <div class="brand-name">PEDRO'S ARTISAN</div>
        </div>
        <p style="color:var(--muted-foreground);font-size:0.85rem;font-family:var(--font-montserrat)">Premium barbering, braiding, tattoo, piercing & cap cleaning services.</p>
      </div>
      <div class="col-md-4">
        <h6 style="color:var(--primary);font-family:var(--font-bebas);font-size:1.1rem;letter-spacing:0.1em">HOURS</h6>
        <p style="color:var(--muted-foreground);font-size:0.85rem;font-family:var(--font-montserrat)">
          Tue–Sat: 10:00 AM – 8:00 PM<br>
          Sunday: 1:00 PM – 8:00 PM<br>
          Monday: Closed
        </p>
      </div>
      <div class="col-md-4">
        <h6 style="color:var(--primary);font-family:var(--font-bebas);font-size:1.1rem;letter-spacing:0.1em">CONTACT</h6>
        <p style="color:var(--muted-foreground);font-size:0.85rem;font-family:var(--font-montserrat)">
          <a href="https://www.google.com/maps/search/?api=1&query=208+Tubuan+2+Bypass+Road+Silang+Cavite+04118" target="_blank" style="color:inherit; text-decoration:none;">
            208 Tubuan 2 Bypass Road<br>Silang, Cavite 04118
          </a><br>
          +1 (555) 123-4567<br>info@pedrosshop.com
        </p>
      </div>
    </div>
    <div style="border-top:1px solid var(--border);margin-top:2rem;padding-top:1.5rem;text-align:center;color:var(--muted-foreground);font-size:0.8rem;font-family:var(--font-montserrat)">
      © <?= date('Y') ?> Pedro's Artisan One-Stop Shop. All rights reserved.
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let slideshowIntervals = new Map();

function startSlideshow(card) {
    const slides = card.querySelectorAll('.service-slideshow .slide');
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
        const slides = card.querySelectorAll('.service-slideshow .slide');
        slides.forEach((s, idx) => s.classList.toggle('active', idx === 0));
    }
}
</script>
</body>
</html>
