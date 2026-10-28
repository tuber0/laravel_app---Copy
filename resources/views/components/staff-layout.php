<?php
// components/staff-layout.php
// Usage: include at top of each staff page
// Set $page = 'dashboard' | 'appointments' | 'portfolio'
// Set $title = page title string

if (!function_exists('icon')) {
function icon($name) {
  $icons = [
    'layout-dashboard' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>',
    'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-calendar" style="color:#ffffff;stroke:#ffffff;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
    'clock' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-clock" style="color:#ffffff;stroke:#ffffff;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'image' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>',
    'logout' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>',
    'menu' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>',
    'x' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
    'settings' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    'mail' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>',
  ];
  return $icons[$name] ?? '';
}
}

function render_staff_layout_start($page, $title = '') {
  $nav_items = [
    'dashboard'    => ['label' => 'Overview',           'icon' => 'layout-dashboard', 'url' => route('staff.dashboard')],
    'appointments' => ['label' => 'My Schedule',        'icon' => 'calendar',         'url' => route('staff.appointments')], 
    'inquiries'    => ['label' => 'Client Inquiries',   'icon' => 'mail',             'url' => route('staff.inquiries.index')], 
    'portfolio'    => ['label' => 'My Portfolio',       'icon' => 'image',            'url' => route('staff.portfolio')], 
    'settings'     => ['label' => 'Profile Settings',   'icon' => 'settings',         'url' => route('staff.settings.index')], 
  ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?: ucfirst($page)) ?> – Pedro's Artisan Staff</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= asset('assets/css/theme.css') ?>?v=<?= filemtime(public_path('assets/css/theme.css')) ?>">
</head>
<body>

<!-- Sidebar Overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<aside id="sidebar">
  <div class="sidebar-header">
    <div class="d-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center gap-3">
        <img src="<?= asset('assets/img/logo.jpg') ?>?v=<?= time() ?>" alt="Pedro's Artisan Logo" style="height: 48px; border-radius: 50%; border: 2px solid var(--primary);">
        <div>
          <div class="brand-name">PEDRO'S ARTISAN</div>
          <div class="brand-sub" style="color: var(--muted-foreground);">Staff Panel</div>
        </div>
      </div>
      <button onclick="closeSidebar()" class="btn p-1 d-lg-none" style="color:var(--foreground)"><?= icon('x') ?></button>
    </div>
  </div>
  <nav class="sidebar-nav flex-grow-1 overflow-auto py-2">
    <?php foreach ($nav_items as $key => $item): ?>
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
    <!-- Header -->
    <header class="app-header">
        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <button id="sidebar-toggle-btn" onclick="toggleSidebar()" class="btn p-1" style="color:var(--foreground)">
                        <?= icon('menu') ?>
                    </button>
                    <div class="header-title-box">
                        <h1 class="h5 mb-0 fw-bold"><?= $title ?></h1>
                        <p class="small text-muted mb-0 d-none d-sm-block" style="color: var(--muted-foreground) !important;">Pedro's Artisan Staff Panel</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    <!-- Notification Bell -->
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
                          <a href="<?= route('staff.inquiries.index') ?>" style="color:#D4AF37; font-size:0.8rem; font-weight:600; text-decoration:none;">View all Client Inquiries →</a>
                        </div>
                      </div>
                    </div>

                    <!-- User Profile Area -->
                    <a href="<?= route('staff.settings.index') ?>" class="text-decoration-none d-flex align-items-center gap-2 border-start ps-3" style="border-color: var(--border) !important;">
                        <div class="text-end d-none d-md-block px-2">
                            <p class="small fw-bold mb-0 header-user-name" style="color: var(--foreground);"><?= auth()->user()->name ?? 'Staff' ?></p>
                            <p class="small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px; color: var(--muted-foreground);"><?= auth()->user()->role ?? 'Staff' ?></p>
                        </div>
                        <div class="avatar-box" style="padding: 0; width: 38px; height: 38px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--primary); flex-shrink: 0;">
                            <?php if(auth()->user()->profile_picture): ?>
                                <img src="<?= asset('storage/' . auth()->user()->profile_picture) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #000; font-weight: 800; font-size: 1rem;">
                                    <?= strtoupper(substr(auth()->user()->name ?? 'S', 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
<?php
}

function render_staff_layout_end() {
?>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('show');
    }
    function closeSidebar() {
      document.getElementById('sidebar').classList.remove('open');
      document.getElementById('sidebar-overlay').classList.remove('show');
    }
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
    // NOTIFICATION BELL - Staff / Artist
    // ==========================================
    const NOTIF_FETCH_URL    = '<?= route('notifications.index') ?>';
    const NOTIF_READ_URL     = '<?= url('/notifications') ?>';
    const NOTIF_READ_ALL_URL = '<?= route('notifications.read-all') ?>';
    const CSRF_TOKEN         = '<?= csrf_token() ?>';

    function getNotifTypeStyle(type) {
      const map = {
        success: { icon: '🎉', color: '#22c55e', bg: 'rgba(34,197,94,0.08)' },
        quote:   { icon: '📋', color: '#D4AF37', bg: 'rgba(212,175,55,0.08)' },
        warning: { icon: '⚠️', color: '#f59e0b', bg: 'rgba(245,158,11,0.08)' },
        info:    { icon: '💬', color: '#60a5fa', bg: 'rgba(96,165,250,0.08)' },
      };
      return map[type] || map['info'];
    }

    function timeAgo(dateString) {
      const now  = new Date();
      const date = new Date(dateString);
      const secs = Math.floor((now - date) / 1000);
      if (secs < 60)    return 'just now';
      if (secs < 3600)  return Math.floor(secs / 60) + 'm ago';
      if (secs < 86400) return Math.floor(secs / 3600) + 'h ago';
      return Math.floor(secs / 86400) + 'd ago';
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

      if (data.unread_count > 0) {
        badge.textContent = data.unread_count > 99 ? '99+' : data.unread_count;
        badge.style.display = 'block';
        badge.style.animation = 'notifPulse 0.4s ease';
        setTimeout(() => badge.style.animation = '', 400);
      } else {
        badge.style.display = 'none';
      }

      if (!data.notifications || data.notifications.length === 0) {
        list.innerHTML = `
          <div class="text-center py-5" style="color:rgba(255,255,255,0.3); font-size:0.85rem;">
            <div style="font-size:2rem; margin-bottom:8px;">🔕</div>
            <div>No new notifications</div>
          </div>`;
        return;
      }

      list.innerHTML = data.notifications.map(n => {
        const s = getNotifTypeStyle(n.type);
        const dot = !n.is_read
          ? `<span style="width:8px;height:8px;border-radius:50%;background:#D4AF37;flex-shrink:0;margin-top:4px;"></span>`
          : `<span style="width:8px;flex-shrink:0;"></span>`;
        const bg = !n.is_read ? s.bg : 'transparent';
        const safeLink = n.link ? encodeURI(n.link) : '';
        return `
        <div class="notif-item" data-id="${n.id}" data-link="${safeLink}" onclick="handleNotifClick(event, this)"
             style="display:flex;align-items:flex-start;gap:12px;padding:14px 18px;cursor:pointer;border-bottom:1px solid rgba(255,255,255,0.05);background:${bg};transition:background 0.2s;">
          <div style="font-size:1.25rem;flex-shrink:0;margin-top:2px;">${s.icon}</div>
          <div style="flex:1;min-width:0;">
            <div style="color:${!n.is_read ? '#fff' : 'rgba(255,255,255,0.6)'};font-weight:${!n.is_read ? '600' : '400'};font-size:0.82rem;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(n.title)}</div>
            <div style="color:rgba(255,255,255,0.5);font-size:0.75rem;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">${escapeHtml(n.message)}</div>
            <div style="color:rgba(255,255,255,0.3);font-size:0.7rem;margin-top:4px;">${timeAgo(n.created_at)}</div>
          </div>
          ${dot}
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

    if (document.getElementById('notif-bell-btn')) {
      fetchNotifications();
      setInterval(fetchNotifications, 30000);
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
</body>
</html>
<?php
}
