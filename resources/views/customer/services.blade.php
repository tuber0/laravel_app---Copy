<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'services';
render_customer_layout_start($page, 'Services');
$cats = collect(['All'])->merge($categories);
$cat_filter = $_GET['cat'] ?? 'All';
$filtered = $cat_filter === 'All' ? $services : collect($services)->filter(fn($s) => $s->category === $cat_filter);
?>
<div class="p-3">
  <h2 class="page-title mb-1">Services</h2>
  <p class="page-subtitle mb-3">Browse all our available services</p>

  <div class="d-flex gap-2 overflow-auto pb-2 mb-4">
    <?php foreach ($cats as $c): ?>
    <a href="?cat=<?= urlencode($c) ?>" class="day-btn <?= $cat_filter===$c?'active':'' ?>" style="font-size:0.8rem;padding:0.4rem 0.75rem"><?= $c ?></a>
    <?php endforeach; ?>
  </div>

  <div class="row g-3">
    <?php foreach ($filtered as $s): ?>
    <div class="col-md-6">
      <div class="card p-3 d-flex flex-row align-items-center gap-3">
        <div class="flex-grow-1">
          <div style="font-weight:600"><?= $s->name ?></div>
          <div class="d-flex align-items-center gap-2 mt-1">
            <div style="font-size:0.8rem;color:var(--muted-foreground)">⏱ <?= $s->duration ?></div>
            <span class="badge" style="background:rgba(212,175,55,0.1);color:var(--primary);font-size:0.65rem;letter-spacing:0.5px;text-transform:uppercase"><?= $s->category ?></span>
          </div>
        </div>
        <div class="text-end">
          <div style="font-weight:700;color:var(--primary);font-size:1.1rem">₱<?= $s->price ?></div>
          <a href="<?= route('customer.book', ['service_id' => $s->id]) ?>" class="btn btn-gold btn-sm mt-1" style="font-size:0.75rem">Book</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php render_customer_layout_end(); ?>

