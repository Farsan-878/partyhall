<?php
/**
 * Cafe Coffea – Cups & Moments
 * Dedicated Photo Gallery Page
 */
require_once __DIR__ . '/config/db.php';

$db = get_db();
$settings = get_all_settings();
$baseUrl = get_base_url();

$stmt = $db->query("SELECT * FROM photos WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
$photos = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>
    <!-- Page Header Banner -->
    <div style="padding: 7rem 0 3rem; background: var(--color-bg-surface); border-bottom: 1px solid var(--border-subtle); text-align: center;">
        <div class="container">
            <div class="section-tag">
                <i class="fas fa-camera"></i>
                <span>Visual Journey</span>
            </div>
            <h1 class="section-title">Party Hall Photo Gallery</h1>
            <p class="section-subtitle">
                Explore our welcoming private party space, luxury stage decor, and ambient evening setups.
            </p>

            <div class="gallery-filters" style="margin-bottom: 0;">
                <button type="button" class="gallery-filter-btn active" data-filter="all">All Photos</button>
                <button type="button" class="gallery-filter-btn" data-filter="stage">Stage & Backdrop</button>
                <button type="button" class="gallery-filter-btn" data-filter="interior">Hall Interior</button>
                <button type="button" class="gallery-filter-btn" data-filter="seating">Seating & Dining</button>
                <button type="button" class="gallery-filter-btn" data-filter="decor">Ambiance & Decor</button>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="section section-dark">
        <div class="container">
            <div class="gallery-grid">
                <?php foreach ($photos as $photo): 
                    $thumb = !empty($photo['thumb_path']) ? $baseUrl . '/' . ltrim($photo['thumb_path'], '/') : $baseUrl . '/' . ltrim($photo['file_path'], '/');
                    $full = $baseUrl . '/' . ltrim($photo['file_path'], '/');
                    $cat = e($photo['category'] ?? 'general');
                ?>
                <div class="gallery-card" 
                     data-category="<?= $cat ?>" 
                     data-full="<?= $full ?>" 
                     data-title="<?= e($photo['title'] ?? 'Cafe Coffea Party Hall') ?>">
                    <img src="<?= $thumb ?>" alt="<?= e($photo['title'] ?? 'Party Hall Photograph') ?>" class="gallery-img" loading="lazy">
                    <div class="gallery-card-overlay">
                        <span class="gallery-card-category">
                            <i class="fas fa-tag text-gold"></i> <?= ucfirst($cat) ?>
                        </span>
                        <h4 class="gallery-card-title"><?= e($photo['title'] ?? 'Cafe Coffea Moments') ?></h4>
                    </div>
                    <div class="gallery-zoom-badge">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="text-align: center; margin-top: 3.5rem;">
                <a href="<?= $baseUrl ?>/index.php#pricing" class="btn btn-primary btn-lg">
                    <i class="fas fa-tag"></i>
                    <span>View Packages & Pricing</span>
                </a>
            </div>
        </div>
    </div>

<?php
include __DIR__ . '/includes/footer.php';
?>

