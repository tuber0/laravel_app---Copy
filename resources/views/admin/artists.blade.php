<?php
require_once resource_path('views/components/admin-layout.php');
$page = 'artists';
render_admin_layout_start($page, 'Artists');

$artists = [
  ['name'=>'Pedro Martinez','role'=>'Master Barber',       'skills'=>['Fades','Classic Cuts','Beard Styling','Hot Towel Shaves'],'exp'=>'15 years','rating'=>4.9,'reviews'=>247,'bio'=>'Master barber with over 15 years of experience. Specializing in classic and modern cuts.','img'=>'https://images.unsplash.com/photo-1568702846914-96b305d2aaeb?w=400&q=80'],
  ['name'=>'Maya Johnson',  'role'=>'Braiding Specialist', 'skills'=>['Box Braids','Cornrows','Twists','Protective Styles'],     'exp'=>'10 years','rating'=>4.8,'reviews'=>189,'bio'=>'Expert in protective hairstyles and natural hair care.','img'=>'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400&q=80'],
  ['name'=>'Rico Santos',   'role'=>'Tattoo Artist',       'skills'=>['Black & Grey','Realism','Traditional','Cover-ups'],       'exp'=>'12 years','rating'=>5.0,'reviews'=>156,'bio'=>'Award-winning tattoo artist specializing in realistic portraits and custom designs.','img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80'],
  ['name'=>'Lisa Chen',     'role'=>'Piercing Specialist', 'skills'=>['Ear Piercings','Nose Piercings','Body Piercings','Jewelry Fitting'],'exp'=>'8 years','rating'=>4.9,'reviews'=>142,'bio'=>'Certified piercing professional with focus on safety and hygiene.','img'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&q=80'],
  ['name'=>'Jay Williams',  'role'=>'Barber & Cap Specialist','skills'=>['Haircuts','Cap Cleaning','Cap Restoration','Sneaker Cleaning'],'exp'=>'6 years','rating'=>4.7,'reviews'=>98,'bio'=>'Young and talented barber with expertise in cap and sneaker restoration.','img'=>'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&q=80'],
  ['name'=>'Marcus Brown',  'role'=>'Barber',              'skills'=>['Modern Cuts','Fades','Line-ups','Kids Cuts'],             'exp'=>'7 years','rating'=>4.8,'reviews'=>134,'bio'=>'Passionate barber with a modern approach to classic styles. Great with kids.','img'=>'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&q=80'],
];
?>

<div class="mb-4">
  <h1 class="page-title">Our Artists</h1>
  <p class="page-subtitle">Meet our talented team of specialists</p>
</div>

<div class="row g-4">
  <?php foreach ($artists as $a): ?>
  <div class="col-md-6 col-lg-4">
    <div class="card artist-card" style="overflow:hidden">
      <div style="position:relative">
        <img src="<?= $a['img'] ?>" alt="<?= $a['name'] ?>" onerror="this.src='https://via.placeholder.com/400x220/1a1a1a/D4AF37?text=<?= urlencode($a['name']) ?>'">
        <div class="artist-badge">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354l-4.543 2.826c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005z" clip-rule="evenodd"/></svg>
          <?= $a['rating'] ?>
        </div>
      </div>
      <div class="card-body p-4">
        <h5 style="font-weight:700;margin-bottom:0.25rem"><?= $a['name'] ?></h5>
        <p style="color:var(--primary);margin-bottom:0.75rem;font-size:0.9rem"><?= $a['role'] ?></p>
        <p style="color:var(--muted-foreground);font-size:0.85rem;margin-bottom:1rem"><?= $a['bio'] ?></p>
        <div class="d-flex gap-3 mb-3" style="font-size:0.8rem;color:var(--muted-foreground)">
          <span>⏱ <?= $a['exp'] ?></span>
          <span>🏆 <?= $a['reviews'] ?> reviews</span>
        </div>
        <div style="margin-bottom:1rem">
          <div style="font-size:0.85rem;font-weight:600;margin-bottom:0.5rem">Skills</div>
          <div class="d-flex flex-wrap gap-2">
            <?php foreach ($a['skills'] as $skill): ?>
            <span class="skill-tag"><?= $skill ?></span>
            <?php endforeach; ?>
          </div>
        </div>
        <a href="/customer/book.php" class="btn btn-gold w-100">Book with <?= explode(' ', $a['name'])[0] ?></a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<?php render_admin_layout_end(); ?>

