<?php
/**
 * Cafe Coffea – Admin Dashboard Overview
 */
require_once __DIR__ . '/../config/db.php';
require_admin_auth();

$db = get_db();
$settings = get_all_settings();
$baseUrl = get_base_url();

// Fetch summary metrics
$photoCount = (int)$db->query("SELECT COUNT(*) FROM photos WHERE is_active = 1")->fetchColumn();
$videoCount = (int)$db->query("SELECT COUNT(*) FROM videos WHERE is_active = 1")->fetchColumn();
$instructionsCount = (int)$db->query("SELECT COUNT(*) FROM instructions WHERE is_active = 1")->fetchColumn();

$hallPrice = (float)($settings['party_hall_price'] ?? 1000);
$cleanPrice = (float)($settings['cleaning_charge'] ?? 200);
$totalStandardPrice = $hallPrice + $cleanPrice;
$offerThreshold = (float)($settings['offer_threshold'] ?? 5000);
$offerFreeHours = $settings['offer_free_hours'] ?? '1';

// Current Hero Image
$heroImage = !empty($settings['hero_image']) ? $settings['hero_image'] : 'assets/images/IMG_1994.jpg';
$heroImageUrl = $baseUrl . '/' . ltrim($heroImage, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Cafe Coffea</title>
    <link rel="icon" type="image/png" href="<?= $baseUrl ?>/assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <?php include __DIR__ . '/../includes/admin_nav.php'; ?>

    <main class="admin-main">
        <div class="admin-container">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h1 class="page-title">Dashboard Overview</h1>
                    <p class="page-subtitle">Welcome to Cafe Coffea – Cups & Moments Party Hall Management System.</p>
                </div>
                <div>
                    <a href="<?= $baseUrl ?>/index.php" target="_blank" class="adm-btn adm-btn-primary">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Live Website</span>
                    </a>
                </div>
            </div>

            <!-- Stats Metrics Grid -->
            <div class="stats-grid">
                <!-- 1. Total Photos -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-camera"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $photoCount ?></div>
                        <div class="stat-label">Gallery Photos</div>
                    </div>
                </div>

                <!-- 2. Total Videos -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div class="stat-value"><?= $videoCount ?></div>
                        <div class="stat-label">Video Files</div>
                    </div>
                </div>

                <!-- 3. Current Standard Price -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <div class="stat-value">₹<?= number_format($totalStandardPrice) ?></div>
                        <div class="stat-label">1-Hr Booking (₹<?= number_format($hallPrice) ?>+₹<?= number_format($cleanPrice) ?>)</div>
                    </div>
                </div>

                <!-- 4. Special Offer Threshold -->
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div>
                        <div class="stat-value">₹<?= number_format($offerThreshold) ?></div>
                        <div class="stat-label">Free <?= e($offerFreeHours) ?>-Hr Threshold</div>
                    </div>
                </div>
            </div>

            <!-- Hero Image Preview & Fast Switch -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-image text-gold"></i> Active Homepage Hero Background</span>
                    <a href="<?= $baseUrl ?>/admin/photos.php" class="adm-btn adm-btn-outline adm-btn-sm">
                        <i class="fas fa-exchange-alt"></i>
                        <span>Change Hero Photo</span>
                    </a>
                </div>
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="width: 100%; height: 260px; border-radius: var(--adm-radius); overflow: hidden; position: relative; border: 1px solid var(--adm-border);">
                        <img src="<?= $heroImageUrl ?>" alt="Active Hero Image" style="width: 100%; height: 100%; object-fit: cover;">
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 1rem; background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,0.85) 100%);">
                            <span style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--adm-gold); font-weight: 700;">
                                Currently Live on Public Website
                            </span>
                            <div style="font-size: 1.1rem; font-weight: 600; color: #FFF;"><?= e(basename($heroImage)) ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Management Actions -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-bolt text-gold"></i> Quick Management Shortcuts</span>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
                    <a href="<?= $baseUrl ?>/admin/photos.php" class="adm-btn adm-btn-outline" style="justify-content: flex-start; padding: 1.1rem;">
                        <i class="fas fa-upload text-gold" style="font-size: 1.2rem;"></i>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; color: #FFF;">Manage Photos</div>
                            <div style="font-size: 0.75rem; color: var(--adm-text-muted);">Upload, reorder, delete photos</div>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?>/admin/videos.php" class="adm-btn adm-btn-outline" style="justify-content: flex-start; padding: 1.1rem;">
                        <i class="fas fa-video text-gold" style="font-size: 1.2rem;"></i>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; color: #FFF;">Manage Videos</div>
                            <div style="font-size: 0.75rem; color: var(--adm-text-muted);">Update titles & video clips</div>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?>/admin/pricing.php" class="adm-btn adm-btn-outline" style="justify-content: flex-start; padding: 1.1rem;">
                        <i class="fas fa-tags text-gold" style="font-size: 1.2rem;"></i>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; color: #FFF;">Update Pricing</div>
                            <div style="font-size: 0.75rem; color: var(--adm-text-muted);">Change tariffs & ₹5,000 offer</div>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?>/admin/instructions.php" class="adm-btn adm-btn-outline" style="justify-content: flex-start; padding: 1.1rem;">
                        <i class="fas fa-clipboard-check text-gold" style="font-size: 1.2rem;"></i>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; color: #FFF;">Rules & Policies</div>
                            <div style="font-size: 0.75rem; color: var(--adm-text-muted);">Edit hall, decor & cake rules</div>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?>/admin/contact.php" class="adm-btn adm-btn-outline" style="justify-content: flex-start; padding: 1.1rem;">
                        <i class="fab fa-whatsapp text-gold" style="font-size: 1.2rem;"></i>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; color: #FFF;">WhatsApp & Contact</div>
                            <div style="font-size: 0.75rem; color: var(--adm-text-muted);">Update phone, address & map</div>
                        </div>
                    </a>

                    <a href="<?= $baseUrl ?>/admin/settings.php" class="adm-btn adm-btn-outline" style="justify-content: flex-start; padding: 1.1rem;">
                        <i class="fas fa-sliders-h text-gold" style="font-size: 1.2rem;"></i>
                        <div style="text-align: left;">
                            <div style="font-weight: 600; color: #FFF;">Hero & Text Content</div>
                            <div style="font-size: 0.75rem; color: var(--adm-text-muted);">Edit headings, subheadings, passwords</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>

