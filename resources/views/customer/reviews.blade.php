<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'reviews';
render_customer_layout_start($page, 'My Reviews');

$my_reviews = [
  ['service'=>'Haircut & Beard Trim','artist'=>'Pedro','rating'=>5,'date'=>'2026-03-18','comment'=>"Pedro is amazing! Best fade I've ever had. The attention to detail is incredible.",'status'=>'published'],
  ['service'=>'Fade Cut','artist'=>'Pedro','rating'=>5,'date'=>'2026-03-05','comment'=>'Always leave feeling fresh. The whole shop has a great atmosphere.','status'=>'published'],
];

$pending_review = ['service'=>'Box Braids','artist'=>'Maya','date'=>'2026-03-28'];

function render_stars_inline($rating) {
  $html = '<div class="stars">';
  for ($i = 1; $i <= 5; $i++) {
    $class = $i <= $rating ? 'star-filled' : 'star-empty';
    $html .= '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="'.$class.'" style="width:16px;height:16px"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354l-4.543 2.826c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005z" clip-rule="evenodd"/></svg>';
  }
  return $html . '</div>';
}
?>

<div class="p-3">
  <h2 class="page-title mb-1">My Reviews</h2>
  <p class="page-subtitle mb-4">Share your experience with our team</p>

  <!-- Pending Review -->
  <div class="card p-4 mb-4" style="border-color:var(--primary)">
    <div style="color:var(--primary);font-size:0.8rem;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;margin-bottom:0.5rem">⭐ Awaiting Your Review</div>
    <div style="font-weight:700;margin-bottom:0.25rem"><?= $pending_review['service'] ?></div>
    <div style="color:var(--muted-foreground);font-size:0.85rem;margin-bottom:1.25rem">with <?= $pending_review['artist'] ?> • <?= date('M j, Y', strtotime($pending_review['date'])) ?></div>

    <!-- Star Picker -->
    <div style="margin-bottom:1rem">
      <label style="font-size:0.85rem;color:var(--muted-foreground);display:block;margin-bottom:0.5rem">Your Rating</label>
      <div class="d-flex gap-2" id="starPicker">
        <?php for ($i = 1; $i <= 5; $i++): ?>
        <button onclick="setRating(<?= $i ?>)" id="star<?= $i ?>" style="background:none;border:none;cursor:pointer;padding:2px">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:28px;height:28px;color:var(--muted);transition:color 0.15s"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354l-4.543 2.826c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005z" clip-rule="evenodd"/></svg>
        </button>
        <?php endfor; ?>
      </div>
    </div>

    <div style="margin-bottom:1rem">
      <label class="form-label" style="font-size:0.85rem">Your Comment</label>
      <textarea class="form-control" rows="3" placeholder="Tell us about your experience..."></textarea>
    </div>
    <button class="btn btn-gold w-100">Submit Review</button>
  </div>

  <!-- Published Reviews -->
  <h5 style="font-weight:700;margin-bottom:1rem">Your Reviews</h5>
  <?php foreach ($my_reviews as $r): ?>
  <div class="card p-4 mb-3">
    <div class="d-flex justify-content-between align-items-start mb-2">
      <div>
        <div style="font-weight:600"><?= $r['service'] ?></div>
        <div style="color:var(--muted-foreground);font-size:0.8rem">with <?= $r['artist'] ?> • <?= date('M j, Y', strtotime($r['date'])) ?></div>
      </div>
      <?= render_stars_inline($r['rating']) ?>
    </div>
    <p style="font-size:0.875rem;color:var(--muted-foreground);margin:0"><?= htmlspecialchars($r['comment']) ?></p>
    <div style="margin-top:0.75rem">
      <span style="font-size:0.75rem;padding:0.2rem 0.6rem;background:rgba(193,18,31,0.15);color:#ff6b6b;border-radius:999px">✅ Published</span>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<script>
let selectedRating = 0;
function setRating(n) {
  selectedRating = n;
  for (let i = 1; i <= 5; i++) {
    const star = document.getElementById('star' + i).querySelector('svg');
    star.style.color = i <= n ? 'var(--primary)' : 'var(--muted)';
  }
}
</script>

<?php render_customer_layout_end(); ?>

