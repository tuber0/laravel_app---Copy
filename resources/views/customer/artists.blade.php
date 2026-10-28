<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'artists';
render_customer_layout_start($page, 'Our Artists');
?>
<div class="p-3">
  <div class="d-flex justify-content-between align-items-end mb-4">
    <div>
      <h2 class="page-title mb-1">Our Artists</h2>
      <p class="page-subtitle mb-0">Discover our talented team of specialists</p>
    </div>
  </div>

  <!-- Category Classification Bar -->
  <div class="mb-4" style="overflow-x:auto; white-space:nowrap; padding-bottom:0.5rem">
    <a href="<?= route('customer.artists', ['category' => 'All']) ?>" 
       class="btn btn-sm me-2 <?= (!$category || $category === 'All') ? 'btn-gold' : 'btn-muted' ?>" 
       style="border-radius:20px; padding:0.4rem 1.25rem">All</a>
    <?php foreach ($categories as $cat): ?>
    <a href="<?= route('customer.artists', ['category' => $cat]) ?>" 
       class="btn btn-sm me-2 <?= ($category === $cat) ? 'btn-gold' : 'btn-muted' ?>" 
       style="border-radius:20px; padding:0.4rem 1.25rem"><?= $cat ?></a>
    <?php endforeach; ?>
  </div>

  <?php if($artists->isEmpty()): ?>
    <div class="text-center py-5">
      <div style="font-size:3rem;margin-bottom:1rem">👥</div>
      <h5 class="text-muted">No artists found in this category.</h5>
    </div>
  <?php else: ?>
  <div class="row g-4">
    <?php foreach ($artists as $a): ?>
    <div class="col-sm-6 col-md-4 col-lg-3">
      <div class="card h-100 border-0 shadow-sm" style="overflow:hidden; cursor:pointer;" onclick="window.location.href='<?= route('customer.artists.portfolio', $a->id) ?>'">
        <div style="position:relative">
          <img src="<?= \Illuminate\Support\Str::startsWith($a->image_url, 'http') ? $a->image_url : ($a->image_url ? asset('storage/' . $a->image_url) : 'https://via.placeholder.com/400x200/1a1a1a/D4AF37?text=Artist') ?>" 
               style="width:100%;height:180px;object-fit:cover" >
          <div class="artist-badge" style="position:absolute;top:10px;right:10px;background:rgba(0,0,0,0.6);backdrop-filter:blur(4px);padding:0.25rem 0.6rem;border-radius:12px;font-size:0.75rem;color:#fff;border:1px solid rgba(255,255,255,0.1)">
            ⭐ <?= number_format($a->rating, 1) ?>
          </div>
          <div style="position:absolute;bottom:0;left:0;right:0;padding:2rem 1rem 0.5rem;background:linear-gradient(to top, rgba(0,0,0,0.85), transparent)">
            <span class="badge" style="background:var(--primary);color:#000;font-size:0.65rem;text-transform:uppercase;letter-spacing:0.5px;padding:0.35rem 0.6rem"><?= $a->category ?></span>
          </div>
        </div>
        <div class="card-body p-3">
          <div class="d-flex justify-content-between align-items-start mb-1">
            <h5 style="font-weight:700;margin-bottom:0"><?= $a->name ?></h5>
            <span class="text-gold fw-bold" style="font-size:0.75rem;">
              ₱<?= number_format($a->starting_rate ?? 300, 0) ?>+
            </span>
          </div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-secondary bg-opacity-25 text-white" style="font-size:0.65rem;">
              <?= $a->years_of_experience ?? 5 ?> yrs exp
            </span>
            <span style="color:var(--muted-foreground);font-size:0.75rem;"><?= is_array($a->specialties ?: $a->specialty) ? implode(', ', array_slice($a->specialties ?: $a->specialty, 0, 2)) : ($a->specialties ?: $a->specialty) ?></span>
          </div>
          
          <?php if(!empty($a->skills) && is_array($a->skills)): ?>
          <div class="d-flex flex-wrap gap-1 mb-3">
            <?php foreach(array_slice($a->skills, 0, 2) as $sk): ?>
              <span class="badge bg-dark border border-secondary text-white" style="font-size:0.6rem;"><?= $sk ?></span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
          
          <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top border-secondary border-opacity-25">
            <a href="<?= route('customer.artists.portfolio', $a->id) ?>" class="text-decoration-none" style="font-size:0.75rem;color:var(--primary);font-weight:600">Portfolio →</a>
            <div class="d-flex gap-1">
              <a href="<?= route('customer.inquiries.create', ['artist_id' => $a->id]) ?>" onclick="event.stopPropagation()" class="btn btn-outline-gold btn-sm px-2" style="font-size:0.7rem;" title="Request Custom Quote">💬 Quote</a>
              <a href="<?= route('customer.book', ['artist_id' => $a->id]) ?>" onclick="event.stopPropagation()" class="btn btn-gold btn-sm px-2" style="font-size:0.75rem;">Book</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>



<style>
.btn-muted {
  background: rgba(255,255,255,0.05);
  color: var(--muted-foreground);
  border: 1px solid var(--border);
}
.btn-muted:hover {
  background: rgba(255,255,255,0.1);
  color: #fff;
}
</style>

<?php render_customer_layout_end(); ?>

