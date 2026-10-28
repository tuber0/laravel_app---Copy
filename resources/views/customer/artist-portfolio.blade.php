<?php
require_once resource_path('views/components/admin-layout.php');
require_once resource_path('views/components/customer-layout.php');
$page = 'artists'; // Keep navigation active on 'artists'
render_customer_layout_start($page, $artist->name . "'s Portfolio");
?>

<div class="portfolio-container">
    <div class="d-flex align-items-center mb-4">
        <a href="<?= route('customer.artists') ?>" class="btn btn-sm btn-outline-secondary me-3" style="border-radius: 50%; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; padding: 0;">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
        <h2 class="page-title mb-0">Artist Portfolio</h2>
    </div>

    <!-- Artist Header -->
    <div class="glass-card p-4 mb-5" style="border-radius: 20px;">
        <div class="row align-items-center g-4">
            <div class="col-md-4 text-center text-md-start">
                <img src="<?= \Illuminate\Support\Str::startsWith($artist->image_url, 'http') ? $artist->image_url : ($artist->image_url ? asset('storage/' . $artist->image_url) : 'https://via.placeholder.com/400x200/1a1a1a/D4AF37?text=Artist') ?>" 
                     alt="<?= $artist->name ?> Profile" 
                     class="img-fluid rounded-4 shadow-sm" 
                     style="max-height: 300px; object-fit: cover; width: 100%; border: 1px solid var(--border);">
            </div>
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h1 style="font-family: var(--font-bebas); font-size: 3rem; color: var(--foreground); letter-spacing: 1px; margin-bottom: 0; line-height: 1.1;"><?= $artist->name ?></h1>
                        <p style="color: var(--primary); font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0.5rem;">
                            <?= $artist->category ?> &bull; <?= is_array($artist->specialties ?: $artist->specialty) ? implode(', ', $artist->specialties ?: $artist->specialty) : ($artist->specialties ?: $artist->specialty) ?>
                        </p>
                    </div>
                    <div class="badge" style="background: rgba(0,0,0,0.4); backdrop-filter: blur(8px); padding: 0.5rem 1rem; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); font-size: 1rem;">
                        ⭐ <?= number_format($artist->rating, 1) ?>
                    </div>
                </div>

                <!-- Experience & Starting Rate Highlights -->
                <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
                    <span class="badge bg-gold text-black fw-bold px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                        ⚡ <?= $artist->years_of_experience ?? 5 ?> Years Experience
                    </span>
                    <span class="badge bg-dark border border-secondary text-white px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                        Starting Rate: <strong class="text-gold">₱<?= number_format($artist->starting_rate ?? 300, 2) ?></strong>
                    </span>
                    <?php if(!empty($artist->languages) && is_array($artist->languages)): ?>
                    <span class="text-muted small">
                        🗣️ <?= implode(', ', $artist->languages) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Social Links -->
                <?php if(!empty($artist->social_links) && (isset($artist->social_links['instagram']) || isset($artist->social_links['facebook']))): ?>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if(!empty($artist->social_links['instagram'])): ?>
                    <a href="<?= $artist->social_links['instagram'] ?>" target="_blank" class="btn btn-sm btn-outline-gold d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1" style="font-size:0.75rem;">
                        <span>Instagram</span> ↗
                    </a>
                    <?php endif; ?>
                    <?php if(!empty($artist->social_links['facebook'])): ?>
                    <a href="<?= $artist->social_links['facebook'] ?>" target="_blank" class="btn btn-sm btn-outline-gold d-inline-flex align-items-center gap-1 rounded-pill px-3 py-1" style="font-size:0.75rem;">
                        <span>Facebook</span> ↗
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <h6 style="color: var(--muted-foreground); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; margin-bottom: 0.5rem;">About the Artist</h6>
                    <p style="color: var(--foreground); line-height: 1.6; font-size: 0.95rem;">
                        <?= $artist->bio ?: "Crafting exceptional looks and unique expressions. Passionate about quality, precise detailing, and ensuring every client walks out with a masterpiece." ?>
                    </p>
                </div>

                <!-- Skills & Certifications Badges -->
                <div class="mb-4">
                    <?php if(!empty($artist->skills) && is_array($artist->skills)): ?>
                    <div class="mb-2">
                        <span class="small fw-bold text-uppercase text-gold" style="font-size:0.7rem;letter-spacing:1px;">Core Skills: </span>
                        <?php foreach($artist->skills as $skill): ?>
                            <span class="badge bg-dark border border-secondary text-white me-1 mb-1" style="font-size:0.7rem;"><?= $skill ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($artist->certifications) && is_array($artist->certifications)): ?>
                    <div class="mb-2">
                        <span class="small fw-bold text-uppercase text-gold" style="font-size:0.7rem;letter-spacing:1px;">Accreditations: </span>
                        <?php foreach($artist->certifications as $cert): ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-1 mb-1" style="font-size:0.7rem;">✓ <?= $cert ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($artist->preferred_styles) && is_array($artist->preferred_styles)): ?>
                    <div>
                        <span class="small fw-bold text-uppercase text-gold" style="font-size:0.7rem;letter-spacing:1px;">Preferred Styles: </span>
                        <?php foreach($artist->preferred_styles as $style): ?>
                            <span class="badge bg-secondary bg-opacity-25 text-white me-1 mb-1" style="font-size:0.7rem;"><?= $style ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= route('customer.book', ['artist_id' => $artist->id]) ?>" class="btn btn-gold px-4 py-2 fw-bold" style="border-radius: 12px; font-size: 1.05rem;">
                        Book a Session with <?= explode(' ', trim($artist->name))[0] ?>
                    </a>
                    <a href="<?= route('customer.inquiries.create', ['artist_id' => $artist->id]) ?>" class="btn btn-outline-gold px-4 py-2 fw-bold" style="border-radius: 12px; font-size: 1.05rem;">
                        💬 Request Custom Quote
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Gallery -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h3 style="font-family: var(--font-bebas); font-size: 2rem; color: var(--foreground); letter-spacing: 1px; margin-bottom: 0;">Featured Work & Gallery</h3>
            <p class="text-muted small mb-0">High-resolution showcase of finished sessions.</p>
        </div>
    </div>

    <div class="row g-4">
        <?php 
        $hasItems = $artist->portfolioItems && $artist->portfolioItems->count() > 0;
        $legacyImages = $artist->portfolio_images ?: [];
        ?>
        <?php if($hasItems): ?>
            <?php foreach ($artist->portfolioItems as $pItem): ?>
                <?php $resolvedImg = \Illuminate\Support\Str::startsWith($pItem->image_path, 'http') ? $pItem->image_path : asset('storage/' . $pItem->image_path); ?>
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="portfolio-item-wrapper h-100 glass-card" style="overflow: hidden; border-radius: 14px; border: 1px solid var(--border); transition: transform 0.3s ease;">
                        <div style="position:relative; aspect-ratio: 1/1; overflow:hidden;">
                            <img src="<?= $resolvedImg ?>" class="img-fluid w-100 h-100 portfolio-img" alt="<?= htmlspecialchars($pItem->title ?: 'Work Sample') ?>" style="object-fit: cover; transition: transform 0.4s ease; cursor: zoom-in;" onclick="window.open('<?= $resolvedImg ?>', '_blank')">
                            <?php if($pItem->category): ?>
                            <span class="badge bg-dark bg-opacity-75 text-gold position-absolute bottom-0 start-0 m-2 border border-secondary" style="font-size:0.65rem;">
                                <?= $pItem->category ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-3">
                            <h6 class="fw-bold text-white mb-1 small text-truncate"><?= htmlspecialchars($pItem->title ?: 'Artisan Work Piece') ?></h6>
                            <?php if($pItem->caption): ?>
                            <p class="text-muted small mb-0" style="font-size:0.75rem;"><?= htmlspecialchars($pItem->caption) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($legacyImages)): ?>
            <?php foreach ($legacyImages as $img): ?>
                <?php $resolvedImg = \Illuminate\Support\Str::startsWith($img, 'http') ? $img : asset('storage/' . $img); ?>
                <div class="col-sm-6 col-md-4 col-xl-3">
                    <div class="portfolio-item-wrapper glass-card overflow-hidden" style="border-radius: 12px; border: 1px solid var(--border);">
                        <img src="<?= $resolvedImg ?>" class="img-fluid w-100 portfolio-img" alt="Portfolio Image" style="aspect-ratio: 1/1; object-fit: cover; transition: transform 0.4s ease; cursor: zoom-in;" onclick="window.open('<?= $resolvedImg ?>', '_blank')">
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 py-5 text-center">
                <div style="font-size: 3rem; color: var(--muted-foreground); margin-bottom: 1rem;">🖼️</div>
                <h5 class="text-muted">Stay tuned for amazing work.</h5>
                <p class="small text-muted">This artist is currently curating their online portfolio.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Artist Reviews -->
    <div class="mt-5 pt-4">
        <div class="d-flex justify-content-between align-items-end mb-4">
            <h3 style="font-family: var(--font-bebas); font-size: 2rem; color: var(--foreground); letter-spacing: 1px; margin-bottom: 0;">Client Feedback</h3>
            <div style="color: var(--muted-foreground); font-size: 0.9rem; font-weight: 600;"><?= $artist->reviews->count() ?> Reviews</div>
        </div>

        <?php if ($artist->reviews->isEmpty()): ?>
            <div class="glass-card p-4 text-center">
                <p class="text-muted mb-0">No reviews yet for this artist.</p>
            </div>
        <?php else: ?>
            <div class="row g-3">
                <?php foreach ($artist->reviews->sortByDesc('created_at') as $r): ?>
                <div class="col-md-6">
                    <div class="glass-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($r->user->name) ?>&background=D4AF37&color=0B0B0B&size=48" 
                                     style="width: 40px; height: 40px; border-radius: 50%;" alt="User">
                                <div>
                                    <div style="font-weight: 700; font-size: 0.9rem;"><?= htmlspecialchars($r->user->name) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--muted-foreground);"><?= $r->created_at->format('M d, Y') ?></div>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <div style="position: relative; font-size: 1.1rem; line-height: 1;">
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
                        </div>
                        <p style="font-size: 0.9rem; color: var(--foreground); font-style: italic; margin-bottom: 0;">
                            "<?= htmlspecialchars($r->comment) ?: 'No comment provided.' ?>"
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.portfolio-container {
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
.portfolio-img:hover {
    transform: scale(1.05);
}
@media (max-width: 768px) {
    .portfolio-container { padding: 1rem; }
}
</style>

<?php render_customer_layout_end(); ?>
