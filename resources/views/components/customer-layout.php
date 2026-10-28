<?php
// components/customer-layout.php

function render_customer_layout_start($page, $title = '') {
  $customer_nav = [
    'homepage'     => ['label' => 'Home',               'icon' => 'home',             'url' => route('public.index')],
    'home'         => ['label' => 'Dashboard',          'icon' => 'layout-dashboard', 'url' => route('customer.home')],
    'book'         => ['label' => 'Book',               'icon' => 'calendar',         'url' => route('customer.book')],
    'appointments' => ['label' => 'Appointments',       'icon' => 'clock',            'url' => route('customer.appointments')],
    'inquiries'    => ['label' => 'Inquiries & Quotes', 'icon' => 'mail',             'url' => route('customer.inquiries.index')],
    'artists'      => ['label' => 'Artists',            'icon' => 'users',            'url' => route('customer.artists')],
    'services'     => ['label' => 'Services',           'icon' => 'scissors',         'url' => route('customer.services')],
    'reviews'      => ['label' => 'Reviews',            'icon' => 'star',             'url' => route('customer.reviews')],
  ];
  if (!function_exists('icon')) {
    require_once __DIR__ . '/admin-layout.php';
  }
  $user = Auth::user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?: ucfirst($page)) ?> – Pedro's Artisan</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/theme.css') ?>?v=<?= filemtime(public_path('assets/css/theme.css')) ?>">

</head>
<body class="layout-customer">

<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<aside id="sidebar">
  <div class="sidebar-header">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <img src="<?= asset('assets/img/logo.jpg') ?>?v=<?= time() ?>" alt="Pedro's Artisan Logo" style="height: 48px; border-radius: 50%; border: 2px solid var(--primary);">
        <div><div class="brand-name">PEDRO'S ARTISAN</div></div>
      </div>
      <button onclick="closeSidebar()" class="btn p-1" style="color:var(--foreground)"><?= icon('x') ?></button>
    </div>
  </div>
  <nav class="sidebar-nav flex-grow-1 overflow-auto py-2">
    <?php foreach ($customer_nav as $key => $item): ?>
      <a href="<?= $item['url'] ?>" class="<?= $page === $key ? 'active' : '' ?>">
        <?= icon($item['icon']) ?>
        <span><?= $item['label'] ?></span>
      </a>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <a href="<?= route('logout') ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn-sidebar-logout">
      <?= icon('logout') ?> <span>Logout</span>
    </a>
    <form id="logout-form" action="<?= route('logout') ?>" method="POST" class="d-none">
        <?= csrf_field() ?>
    </form>
  </div>
</aside>

<div class="main-wrapper">
<header class="app-header">
  <div class="container-fluid d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-3">
      <button onclick="toggleSidebar()" class="btn p-2 d-lg-none hamburger-trigger" style="color:var(--foreground)"><?= icon('menu') ?></button>
      <a href="<?= route('public.index') ?>" class="d-flex align-items-center gap-3 text-decoration-none">
        <img src="<?= asset('assets/img/logo.jpg') ?>?v=<?= time() ?>" alt="Pedro's Artisan Logo" style="height: 36px; border-radius: 50%; border: 1.5px solid var(--primary);">
        <div class="d-none d-sm-block">
          <div class="brand-name">PEDRO'S ARTISAN</div>
          <div class="brand-sub">One-Stop Shop</div>
        </div>
      </a>
    </div>

    <!-- Header Navigation Links -->
    <div class="d-flex align-items-center gap-3">
      <nav class="d-none d-lg-flex align-items-center gap-3 me-3">
        <?php foreach($customer_nav as $key => $item): ?>
          <a href="<?= $item['url'] ?>" class="nav-link-custom <?= $page === $key ? 'active' : '' ?>" style="font-size:0.84rem;"><?= $item['label'] ?></a>
        <?php endforeach; ?>
      </nav>

      <!-- Notification Bell -->
      <?php if ($user): ?>
      <div class="dropdown" id="notif-bell-wrapper">
        <button id="notif-bell-btn" class="btn p-2 position-relative" style="color:var(--foreground); background:rgba(255,255,255,0.05); border:1px solid var(--border); border-radius:50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center;" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:18px;height:18px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
          </svg>
          <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="background:#ef4444; font-size:0.6rem; min-width:18px; padding:2px 5px; display:none;">0</span>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0" id="notif-dropdown" style="width:360px; max-width:95vw; border:1px solid rgba(255,255,255,0.1); background:#111; border-radius:14px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.6);">
          <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid rgba(255,255,255,0.08);">
            <span class="fw-bold" style="color:var(--foreground); font-size:0.9rem;">Notifications</span>
            <button onclick="markAllRead(event)" class="btn btn-sm px-2 py-1" style="background:rgba(212,175,55,0.15); color:#D4AF37; border:1px solid rgba(212,175,55,0.3); border-radius:8px; font-size:0.72rem; font-weight:600;">Mark all read</button>
          </div>
          <div id="notif-list" style="max-height:380px; overflow-y:auto;">
            <div class="text-center py-5" style="color:rgba(255,255,255,0.3); font-size:0.85rem;">
              <div style="font-size:1.8rem; margin-bottom:8px;">🔔</div>
              Loading notifications...
            </div>
          </div>
          <div class="px-4 py-3 text-center" style="border-top:1px solid rgba(255,255,255,0.08);">
            <a href="<?= route('customer.inquiries.index') ?>" style="color:#D4AF37; font-size:0.8rem; font-weight:600; text-decoration:none;">View all Inquiries & Quotes →</a>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- User Profile Dropdown -->
      <?php if ($user): ?>
      <div class="dropdown">
        <a href="#" class="profile-dropdown-toggle d-flex align-items-center gap-2 text-decoration-none p-1 rounded-pill" 
           style="background:rgba(255,255,255,0.05); border:1px solid var(--border); padding-right:1rem !important"
           data-bs-toggle="dropdown" aria-expanded="false">
          <img src="<?= Auth::user()->profile_picture ? asset('storage/'.Auth::user()->profile_picture).'?v='.time() : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=D4AF37&color=0B0B0B&size=64' ?>" 
               style="width:32px;height:32px;border-radius:50%;object-fit:cover" alt="Profile">
          <span class="d-none d-sm-inline" style="color:var(--foreground); font-size:0.85rem; font-weight:600"><?= explode(' ', Auth::user()->name)[0] ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark-premium">
          <li><a class="dropdown-item dropdown-item-premium" href="<?= route('public.index') ?>">Home</a></li>
          <li><a class="dropdown-item dropdown-item-premium" href="<?= Auth::user()->isAdmin() ? route('admin.dashboard') : (Auth::user()->isStaff() ? route('appointments.index') : route('customer.home')) ?>">Dashboard</a></li>
          <?php if (Auth::user()->isCustomer()): ?>
            <li><a class="dropdown-item dropdown-item-premium" href="<?= route('customer.appointments') ?>">My Appointments</a></li>
            <li><a class="dropdown-item dropdown-item-premium" href="<?= route('customer.inquiries.index') ?>">Inquiries & Quotes</a></li>
            <li><a class="dropdown-item dropdown-item-premium" href="<?= route('customer.reviews') ?>">My Reviews</a></li>
            <li><a class="dropdown-item dropdown-item-premium" href="<?= route('customer.settings') ?>">Setting</a></li>
          <?php endif; ?>
          <li><hr class="dropdown-divider" style="border-color:rgba(255,255,255,0.05)"></li>
          <li>
            <a class="dropdown-item dropdown-item-premium text-danger" href="<?= route('logout') ?>" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
               Logout
            </a>
          </li>
        </ul>
      </div>
      <?php endif; ?>
    </div>
  </div>
</header>

<main class="main-content">
<?php
}

function render_customer_layout_end() {
?>
</main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebar-overlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('show');
}

// Password Visibility Toggle
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    const eyeIcon = btn.querySelector('.eye-icon');
    const eyeOffIcon = btn.querySelector('.eye-off-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        eyeIcon.classList.add('d-none');
        eyeOffIcon.classList.remove('d-none');
    } else {
        input.type = 'password';
        eyeIcon.classList.remove('d-none');
        eyeOffIcon.classList.add('d-none');
    }
}

// ==========================================
// NOTIFICATION BELL - Customer
// ==========================================
const NOTIF_FETCH_URL   = '<?= route('notifications.index') ?>';
const NOTIF_READ_URL    = '<?= url('/notifications') ?>';
const NOTIF_READ_ALL_URL = '<?= route('notifications.read-all') ?>';
const CSRF_TOKEN        = '<?= csrf_token() ?>';

function getNotifTypeStyle(type) {
  const map = {
    success: { icon: '🎉', color: '#22c55e', bg: 'rgba(34,197,94,0.08)', border: 'rgba(34,197,94,0.2)' },
    quote:   { icon: '📋', color: '#D4AF37', bg: 'rgba(212,175,55,0.08)', border: 'rgba(212,175,55,0.2)' },
    warning: { icon: '⚠️', color: '#f59e0b', bg: 'rgba(245,158,11,0.08)', border: 'rgba(245,158,11,0.2)' },
    info:    { icon: '💬', color: '#60a5fa', bg: 'rgba(96,165,250,0.08)', border: 'rgba(96,165,250,0.2)' },
  };
  return map[type] || map['info'];
}

function timeAgo(dateString) {
  const now = new Date();
  const date = new Date(dateString);
  const secs = Math.floor((now - date) / 1000);
  if (secs < 60) return 'just now';
  if (secs < 3600) return Math.floor(secs/60) + 'm ago';
  if (secs < 86400) return Math.floor(secs/3600) + 'h ago';
  return Math.floor(secs/86400) + 'd ago';
}

function escapeHtml(text) {
  if (!text) return '';
  const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
  return String(text).replace(/[&<>"']/g, m => map[m]);
}

function renderNotifications(data) {
  const badge = document.getElementById('notif-badge');
  const list  = document.getElementById('notif-list');
  if (!badge || !list) return;

  // Update badge
  if (data.unread_count > 0) {
    badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
    badge.style.display = 'block';
    // Pulse animation on new notifications
    badge.style.animation = 'notifPulse 0.4s ease';
    setTimeout(() => badge.style.animation = '', 400);
  } else {
    badge.style.display = 'none';
  }

  if (!data.notifications || data.notifications.length === 0) {
    list.innerHTML = `
      <div class="text-center py-5" style="color:rgba(255,255,255,0.3); font-size:0.85rem;">
        <div style="font-size:2rem; margin-bottom:8px;">🔕</div>
        <div>You're all caught up!</div>
      </div>`;
    return;
  }

  list.innerHTML = data.notifications.map(n => {
    const s = getNotifTypeStyle(n.type);
    const unreadDot = !n.is_read ? `<span style="width:8px;height:8px;border-radius:50%;background:#D4AF37;flex-shrink:0;margin-top:4px;"></span>` : `<span style="width:8px;flex-shrink:0;"></span>`;
    const itemBg = !n.is_read ? s.bg : 'transparent';
    const safeLink = n.link ? encodeURI(n.link) : '';
    return `
    <div class="notif-item" data-id="${n.id}" data-link="${safeLink}" onclick="handleNotifClick(event, this)"
         style="display:flex;align-items:flex-start;gap:12px;padding:14px 18px;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.05);background:${itemBg};transition:background 0.2s;">
      <div style="font-size:1.25rem;flex-shrink:0;margin-top:2px;">${s.icon}</div>
      <div style="flex:1;min-width:0;">
        <div style="color:${!n.is_read ? '#fff' : 'rgba(255,255,255,0.6)'};font-weight:${!n.is_read ? '600' : '400'};font-size:0.82rem;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(n.title)}</div>
        <div style="color:rgba(255,255,255,0.5);font-size:0.75rem;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">${escapeHtml(n.message)}</div>
        <div style="color:rgba(255,255,255,0.3);font-size:0.7rem;margin-top:4px;">${timeAgo(n.created_at)}</div>
      </div>
      ${unreadDot}
    </div>`;
  }).join('');
}

function handleNotifClick(e, el) {
  if (e) e.stopPropagation();
  const id = el.getAttribute('data-id');
  const link = el.getAttribute('data-link');
  markOneRead(id, link);
}

function fetchNotifications() {
  fetch(NOTIF_FETCH_URL, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(renderNotifications)
    .catch(() => {});
}

function markOneRead(id, link) {
  if (!id) return;

  try {
    fetch(`${NOTIF_READ_URL}/${id}/read`, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
      keepalive: true
    }).catch(() => {});
  } catch(err) {}

  if (link && link.trim() !== '') {
    try {
      const parsed = new URL(link, window.location.origin);
      if (parsed.origin === window.location.origin || parsed.hostname === '127.0.0.1' || parsed.hostname === 'localhost') {
        link = parsed.pathname + parsed.search + parsed.hash;
      }
    } catch(e) {}

    const currentBaseUrl = window.location.pathname;
    const targetBaseUrl = link.split('#')[0].split('?')[0];
    if (currentBaseUrl === targetBaseUrl && link.includes('#')) {
      const hash = '#' + link.split('#')[1];
      window.location.hash = hash;
      if (typeof scrollToHashTarget === 'function') {
        scrollToHashTarget();
      }
      fetchNotifications();
    } else {
      window.location.href = link;
    }
  } else {
    fetchNotifications();
  }
}

function markAllRead(e) {
  e.stopPropagation();
  fetch(NOTIF_READ_ALL_URL, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' }
  }).then(() => fetchNotifications()).catch(() => {});
}

// Initial load + polling every 30s
if (document.getElementById('notif-bell-btn')) {
  fetchNotifications();
  setInterval(fetchNotifications, 30000);

  // Load on dropdown open
  document.getElementById('notif-bell-btn').addEventListener('show.bs.dropdown', fetchNotifications);
}
</script>
<style>
@keyframes notifPulse {
  0%   { transform: translate(-50%, -50%) scale(1); }
  50%  { transform: translate(-50%, -50%) scale(1.4); }
  100% { transform: translate(-50%, -50%) scale(1); }
}
.notif-item:hover { background: rgba(255,255,255,0.04) !important; }
#notif-list::-webkit-scrollbar { width: 4px; }
#notif-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
</style>
<?php
}
