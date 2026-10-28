<?php
// components/admin-layout.php
// Usage: include at top of each admin page
// Set $page = 'dashboard' | 'appointments' | 'pos' | 'services' | 'artists' | 'inventory' | 'schedule' | 'ratings' | 'settings'
// Set $title = page title string

if (!function_exists('icon')) {
function icon($name) {
  $icons = [
    'layout-dashboard' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>',
    'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-calendar" style="color:#ffffff;stroke:#ffffff;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>',
    'shopping-cart' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>',
    'scissors' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.848 8.25l1.536.887M7.848 8.25a3 3 0 11-5.196-3 3 3 0 015.196 3zm1.536.887a2.165 2.165 0 011.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 4.092a6.048 6.048 0 008.352 2.675 6.048 6.048 0 01-8.352-2.675zm0 0L9 12m3.45 2.409a2.4 2.4 0 00-.944.962l-1.476 2.898M9.384 9.137l.344-.537c.203-.317.427-.614.667-.89M12 9a3 3 0 11-6 0 3 3 0 016 0zm0 0l1.875 3.375"/></svg>',
    'users' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>',
    'package' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>',
    'clock' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-clock" style="color:#ffffff;stroke:#ffffff;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
    'star' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>',
    'settings' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>',
    'logout' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>',
    'menu' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>',
    'x' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
    'wifi' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:20px;height:20px;color:#C1121F"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/></svg>',
    'home' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>',
    'mail' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>',
    'bell' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>',
    'check' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>',
    'user' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>',
    'flag' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21v-6m0 0l2.77-.693a9 9 0 016.208.682l.108.054a9 9 0 006.086.71l3.114-.732a48.524 48.524 0 01-.005-10.499l-3.11.732a9 9 0 01-6.085-.711l-.108-.054a9 9 0 00-6.208-.682L3 4.5M3 15V4.5" /></svg>',
    'phone' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.622c0-1.108.806-2.056 1.912-2.135a33.847 33.847 0 0111.676 0c1.106.079 1.912 1.027 1.912 2.135v.819a3.856 3.856 0 01-1.012 2.57L13.74 13.08a6.268 6.268 0 01-3.15 1.884l-2.098.42a19.444 19.444 0 01-7.04-3.326 3.856 3.856 0 01-1.012-2.57V6.622z" /></svg>',
  ];
  return $icons[$name] ?? '';
}
}

function render_admin_layout_start($page, $title = '') {
  $nav_items = [
    'dashboard'    => ['label' => 'Overview',           'icon' => 'layout-dashboard', 'url' => route('admin.dashboard')],
    'appointments' => ['label' => 'Appointment Record', 'icon' => 'calendar',         'url' => route('admin.appointments.index')], 
    'staff-create' => ['label' => 'Register Personnel', 'icon' => 'users',        'url' => route('admin.staff.create')], 
    'customers'    => ['label' => 'Customer record',    'icon' => 'users',            'url' => route('admin.customers.index')], 
    'oversight'    => ['label' => 'Personnel Database', 'icon' => 'clock',            'url' => route('admin.staff.oversight')], 
    'services'     => ['label' => 'Service Portfolio',  'icon' => 'scissors',         'url' => route('admin.services.index')],
    'schedule'     => ['label' => 'Schedule Requests',  'icon' => 'clock',            'url' => route('admin.schedule-requests.index')],
    'inventory'    => ['label' => 'Sales History',      'icon' => 'package',          'url' => route('admin.inventory.index')], 
    'inquiries'    => ['label' => 'Inquiries & Quotes', 'icon' => 'mail',             'url' => route('admin.inquiries.index')], 
    'pos'          => ['label' => 'Point Of Sale',      'icon' => 'shopping-cart',    'url' => route('admin.pos')], 
    'settings'     => ['label' => 'Profile Settings',   'icon' => 'settings',         'url' => route('admin.settings.index')], 
  ];
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
          <div class="brand-sub" style="color: var(--muted-foreground);">Admin Panel</div>
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
                        <p class="small text-muted mb-0 d-none d-sm-block" style="color: var(--muted-foreground) !important;">Pedro's Artisan Personnel Panel</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-4">
                    <?php
                        $pendingAppointments = \App\Models\Appointment::where('status', 'pending')->count();
                        $pendingScheduleReqs = \App\Models\ScheduleRequest::where('status', 'pending')->count();
                        $pendingInquiries = \App\Models\Inquiry::where('status', 'pending')->count();
                        $adminNotifs = auth()->check() ? \App\Models\InAppNotification::where('user_id', auth()->id())->where('is_read', false)->latest()->take(5)->get() : collect();
                        $totalNotifications = $pendingAppointments + $pendingScheduleReqs + $pendingInquiries + $adminNotifs->count();
                    ?>
                    
                    <!-- Notification Bell -->
                    <div class="dropdown">
                        <button class="btn btn-link position-relative p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: var(--foreground); text-decoration: none;">
                            <?= icon('bell') ?>
                            <?php if ($totalNotifications > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow" style="font-size: 0.55rem; padding: 0.35em 0.5em; border: 2px solid var(--card);">
                                <?= $totalNotifications ?>
                            </span>
                            <?php endif; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" style="background: var(--card); border: 1px solid var(--border); min-width: 290px; border-radius: 15px; padding: 0.5rem 0; margin-top: 10px;">
                            <li class="px-3 py-2 border-bottom mb-2">
                                <h6 class="mb-0 fw-bold text-primary" style="font-size: 0.85rem; letter-spacing: 0.5px; text-transform: uppercase;">Notifications</h6>
                            </li>
                            
                            <?php if ($pendingAppointments > 0): ?>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-3" href="<?= route('admin.appointments.index') ?>">
                                    <div class="bg-primary text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 800; font-size: 0.8rem;">
                                        <?= $pendingAppointments ?>
                                    </div>
                                    <div class="small fw-semibold text-white">Pending<br>Appointments</div>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if ($pendingScheduleReqs > 0): ?>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-3" href="<?= route('admin.schedule-requests.index') ?>">
                                    <div class="bg-primary text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 800; font-size: 0.8rem;">
                                        <?= $pendingScheduleReqs ?>
                                    </div>
                                    <div class="small fw-semibold text-white">Pending<br>Schedule Requests</div>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if ($pendingInquiries > 0): ?>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-3" href="<?= route('admin.inquiries.index', ['status' => 'pending']) ?>">
                                    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-weight: 800; font-size: 0.8rem;">
                                        <?= $pendingInquiries ?>
                                    </div>
                                    <div class="small fw-semibold text-white">Pending Inquiries<br>(Awaiting Quotation)</div>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php foreach ($adminNotifs as $aNotif): ?>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-start gap-2" href="<?= $aNotif->link ?: route('admin.inquiries.index') ?>">
                                    <span style="font-size: 1.1rem; line-height: 1;">🔔</span>
                                    <div>
                                        <div class="small fw-semibold text-white"><?= htmlspecialchars($aNotif->title) ?></div>
                                        <div class="text-muted" style="font-size: 0.72rem;"><?= htmlspecialchars(\Illuminate\Support\Str::limit($aNotif->message, 45)) ?></div>
                                    </div>
                                </a>
                            </li>
                            <?php endforeach; ?>
                            
                            <?php if ($totalNotifications === 0): ?>
                            <li class="px-4 py-4 text-center opacity-50">
                                <?= icon('bell') ?>
                                <p class="small mb-0 mt-2">You're all caught up!</p>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- User Profile Area -->
                    <a href="<?= route('admin.settings.index') ?>" class="text-decoration-none d-flex align-items-center gap-2 border-start ps-3" style="border-color: var(--border) !important;">
                        <div class="text-end d-none d-md-block px-2">
                            <p class="small fw-bold mb-0 header-user-name" style="color: var(--foreground);"><?= auth()->user()->name ?? 'Administrator' ?></p>
                            <p class="small mb-0 text-uppercase" style="font-size: 0.65rem; letter-spacing: 1px; color: var(--muted-foreground);"><?= auth()->user()->role ?? 'Admin' ?></p>
                        </div>
                        <div class="avatar-box" style="padding: 0; width: 38px; height: 38px; border-radius: 50%; overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--primary); flex-shrink: 0;">
                            <?php if(auth()->user() && auth()->user()->profile_picture): ?>
                                <img src="<?= asset('storage/' . auth()->user()->profile_picture) ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 50%; color: #000; font-weight: 800; font-size: 1rem;">
                                    <?= strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) ?>
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

function render_admin_layout_end() {
?>
    </main>
</div>

<!-- Global Email Modal -->
<div class="modal fade" id="globalEmailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background:#1a1a1a; color:white; border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color:var(--primary); font-family:var(--font-bebas); letter-spacing:1px">Send Email to Client</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="globalEmailForm">
                <div class="modal-body py-4">
                    <input type="hidden" name="to" id="emailTo">
                    <input type="hidden" name="client_name" id="emailClientName">
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Recipient</label>
                        <div id="emailRecipientDisplay" class="form-control-plaintext text-white fw-bold px-2"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Subject</label>
                        <input type="text" name="subject" id="emailSubject" class="form-control bg-dark border-secondary text-white" placeholder="Enter subject line" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold text-uppercase">Message Body</label>
                        <textarea name="message" id="emailMessage" class="form-control bg-dark border-secondary text-white" rows="6" placeholder="Type your message here..." required></textarea>
                    </div>

                    <div id="emailStatus" class="alert d-none small p-2 mb-0"></div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-muted px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="btnSendEmail" class="btn btn-gold px-5 fw-bold shadow">Send Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Global Success Modal -->
<div class="modal fade" id="globalSuccessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content border-0 shadow-lg text-center p-4" style="background:#1a1a1a; color:white; border-radius: 30px;">
            <div class="modal-body">
                <div class="mb-4 d-inline-flex align-items-center justify-content-center bg-success bg-opacity-10 rounded-circle" style="width:80px; height:80px">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="text-success" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                </div>
                <h4 class="fw-bold mb-2" style="font-family:var(--font-bebas); letter-spacing:1px; color:var(--primary)">Success!</h4>
                <p id="globalSuccessMessage" class="text-muted small mb-4"></p>
                <button type="button" class="btn btn-gold w-100 py-2 fw-bold rounded-pill" data-bs-dismiss="modal">Great!</button>
            </div>
        </div>
    </div>
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

    // Global Email Modal Logic
    function openEmailModal(email, name, subject = '') {
        document.getElementById('emailTo').value = email;
        document.getElementById('emailClientName').value = name;
        document.getElementById('emailRecipientDisplay').innerText = name + ' (' + email + ')';
        document.getElementById('emailSubject').value = subject || 'Update regarding your appointment - Pedro\'s Artisan';
        document.getElementById('emailMessage').value = 'Hello ' + (name.split(' ')[0]) + ',\n\n';
        
        const status = document.getElementById('emailStatus');
        status.classList.add('d-none');
        
        const modal = new bootstrap.Modal(document.getElementById('globalEmailModal'));
        modal.show();
    }

    document.getElementById('globalEmailForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSendEmail');
        const status = document.getElementById('emailStatus');
        const formData = new FormData(this);
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
        status.classList.add('d-none');

        try {
            const response = await fetch('<?= route("admin.send-email") ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '<?= csrf_token() ?>',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            
            status.classList.remove('d-none', 'alert-danger', 'alert-success');
            status.classList.add(data.success ? 'alert-success' : 'alert-danger');
            status.innerText = data.message;

            if (data.success) {
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('globalEmailModal')).hide();
                    btn.disabled = false;
                    btn.innerText = 'Send Now';
                }, 2000);
            } else {
                btn.disabled = false;
                btn.innerText = 'Send Now';
            }
        } catch (error) {
            status.classList.remove('d-none', 'alert-success');
            status.classList.add('alert-danger');
            status.innerText = 'An error occurred. Please try again.';
            btn.disabled = false;
            btn.innerText = 'Send Now';
        }
    });

    // Auto-show Success Modal if session has success
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(session('success')): ?>
            const successMsg = "<?= addslashes(session('success')) ?>";
            const successEl = document.getElementById('globalSuccessMessage');
            if (successEl) {
                successEl.innerText = successMsg;
                const successModal = new bootstrap.Modal(document.getElementById('globalSuccessModal'));
                successModal.show();
                
                // Auto-hide after 3 seconds
                setTimeout(() => {
                    successModal.hide();
                }, 3000);
            }
        <?php endif; ?>
    });
</script>
<?php
}
