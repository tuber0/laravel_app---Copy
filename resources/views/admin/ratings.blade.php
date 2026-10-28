<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'ratings';
render_admin_layout_start($page, 'Ratings & Feedback');

$reviews = [
  ['client'=>'Marcus Johnson','artist'=>'Pedro','service'=>'Haircut & Beard Trim','rating'=>5,'date'=>'2026-03-18','comment'=>"Pedro is amazing! Best fade I've ever had. The attention to detail is incredible. Will definitely be back!",'helpful'=>12],
  ['client'=>'Sarah Williams','artist'=>'Maya','service'=>'Box Braids','rating'=>5,'date'=>'2026-03-17','comment'=>'Maya did an excellent job with my box braids. She was patient, professional, and the braids look perfect! Highly recommend.','helpful'=>8],
  ['client'=>'David Lee','artist'=>'Rico','service'=>'Tattoo Session','rating'=>5,'date'=>'2026-03-16','comment'=>"Rico is a true artist! My tattoo came out even better than I imagined. Clean work, professional setup, and great conversation.",'helpful'=>15],
  ['client'=>'Emma Davis','artist'=>'Lisa','service'=>'Ear Piercing','rating'=>5,'date'=>'2026-03-15','comment'=>'Lisa made the piercing process so comfortable. Very professional and the jewelry selection is great. No issues with healing!','helpful'=>6],
  ['client'=>'Michael Brown','artist'=>'Jay','service'=>'Cap Cleaning','rating'=>4,'date'=>'2026-03-14','comment'=>'Great job cleaning my vintage caps. They look brand new! Would give 5 stars if the turnaround was a bit faster.','helpful'=>4],
  ['client'=>'Lisa Anderson','artist'=>'Marcus','service'=>'Fade Cut','rating'=>5,'date'=>'2026-03-13','comment'=>"Marcus knows exactly what he's doing. Clean fade, great vibe, and reasonable prices. This is my new go-to spot!",'helpful'=>9],
];

function render_stars($rating) {
  $html = '<div class="stars d-flex gap-1">';
  for ($i = 1; $i <= 5; $i++) {
    $fill = 0;
    if ($rating >= $i) $fill = 100;
    elseif ($rating > $i - 1) $fill = ($rating - ($i - 1)) * 100;
    
    $html .= '<div style="position: relative; font-size: 1rem; line-height: 1;">';
    $html .= '<span style="color: rgba(255,255,255,0.1);">★</span>';
    $html .= '<span style="position: absolute; top: 0; left: 0; color: var(--primary); overflow: hidden; width: '.$fill.'%; white-space: nowrap;">★</span>';
    $html .= '</div>';
  }
  return $html . '</div>';
}
?>

<div class="mb-4">
  <h1 class="page-title">Ratings & Feedback</h1>
  <p class="page-subtitle">Customer reviews and satisfaction metrics</p>
</div>

<!-- Stats -->
<div class="row g-4 mb-4">
  <?php
  $stats = [
    ['label'=>'Average Rating','value'=>'4.9','icon'=>'⭐','color'=>'var(--primary)'],
    ['label'=>'Total Reviews','value'=>'247','icon'=>'💬','color'=>'var(--secondary)'],
    ['label'=>'Satisfaction Rate','value'=>'98%','icon'=>'📈','color'=>'var(--primary)'],
    ['label'=>'5-Star Reviews','value'=>'235','icon'=>'👍','color'=>'var(--secondary)'],
  ];
  foreach ($stats as $s): ?>
  <div class="col-6 col-lg-3">
    <div class="card stat-card">
      <div style="font-size:2rem;margin-bottom:0.75rem"><?= $s['icon'] ?></div>
      <div class="stat-value"><?= $s['value'] ?></div>
      <div class="stat-label"><?= $s['label'] ?></div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Rating Distribution -->
<div class="card p-4 mb-4">
  <h5 style="font-weight:700;margin-bottom:1rem">Rating Distribution</h5>
  <?php
  $dist = [5=>235, 4=>10, 3=>2, 2=>0, 1=>0];
  $total = 247;
  foreach ($dist as $star => $count):
    $pct = ($count / $total) * 100;
  ?>
  <div class="d-flex align-items-center gap-3 mb-2">
    <div style="width:60px;display:flex;align-items:center;gap:4px;font-size:0.9rem">
      <?= $star ?> <?= render_stars(1) ?>
    </div>
    <div class="rating-bar-wrap">
      <div class="rating-bar-fill" style="width:<?= $pct ?>%"></div>
    </div>
    <span style="font-size:0.85rem;color:var(--muted-foreground);width:30px;text-align:right"><?= $count ?></span>
  </div>
  <?php endforeach; ?>
</div>

<!-- Reviews -->
<h5 style="font-weight:700;margin-bottom:1rem">Recent Reviews</h5>
<?php foreach ($reviews as $r): ?>
<div class="card p-4 mb-3">
  <div class="d-flex justify-content-between align-items-start mb-3">
    <div class="d-flex align-items-center gap-3">
      <div style="width:40px;height:40px;border-radius:50%;background:rgba(212,175,55,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;color:var(--primary)">
        <?= strtoupper(substr($r['client'], 0, 1)) ?>
      </div>
      <div>
        <div style="font-weight:600"><?= htmlspecialchars($r['client']) ?></div>
        <div style="font-size:0.8rem;color:var(--muted-foreground)"><?= $r['service'] ?> • <?= $r['artist'] ?></div>
      </div>
    </div>
    <div class="text-end">
      <?= render_stars($r['rating']) ?>
      <div style="font-size:0.75rem;color:var(--muted-foreground);margin-top:4px"><?= date('M j, Y', strtotime($r['date'])) ?></div>
    </div>
  </div>
  <p style="font-size:0.9rem;color:var(--foreground);margin-bottom:1rem;line-height:1.6"><?= htmlspecialchars($r['comment']) ?></p>
  <div style="border-top:1px solid var(--border);padding-top:0.75rem;display:flex;gap:1.5rem">
    <button class="btn btn-sm p-0" style="color:var(--muted-foreground);font-size:0.85rem;background:none">👍 Helpful (<?= $r['helpful'] ?>)</button>
    <button class="btn btn-sm p-0" style="color:var(--muted-foreground);font-size:0.85rem;background:none">💬 Reply</button>
  </div>
</div>
<?php endforeach; ?>

<div class="text-center mt-2">
  <button class="btn btn-muted px-5">Load More Reviews</button>
</div>

<?php render_admin_layout_end(); ?>

