<?php
/**
 * Cafe Coffea – Cups & Moments
 * Public Site Header Component
 */
require_once __DIR__ . '/../config/db.php';

$baseUrl = get_base_url();
$settings = get_all_settings();

$siteTitle = $settings['site_title'] ?? 'Cafe Coffea – Cups & Moments';
$brandName = $settings['brand_name'] ?? 'Cafe Coffea';
$brandTagline = $settings['brand_tagline'] ?? 'Cups & Moments';
$whatsappNumber = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '918157022220');
$prefillText = $settings['whatsapp_prefill_text'] ?? 'Hello Cafe Coffea! I would like to enquire about party hall booking at Cafe Coffea – Cups & Moments.';
$whatsappUrl = "https://wa.me/" . $whatsappNumber . "?text=" . rawurlencode($prefillText);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?= e($siteTitle) ?> | Premium Party Hall in Kottakkal</title>
    
    <!-- SEO & OpenGraph Meta -->
    <meta name="description" content="Celebrate your birthdays, anniversaries, and special moments at Cafe Coffea's premium party hall. Cozy ambiance, elegant decor, and handcrafted cakes.">
    <meta property="og:title" content="<?= e($siteTitle) ?>">
    <meta property="og:description" content="Celebrate Your Special Moments at Cafe Coffea. Premium party hall space in Kottakkal.">
    <meta property="og:image" content="<?= $baseUrl ?>/assets/images/logo.png">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= $baseUrl ?>/assets/images/logo.png">
    <link rel="apple-touch-icon" href="<?= $baseUrl ?>/assets/images/logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>
<body>

    <!-- Header & Navigation -->
    <header class="site-header">
        <div class="container header-container">
            <a href="<?= $baseUrl ?>/index.php" class="brand-link" aria-label="Cafe Coffea Homepage">
                <img src="<?= $baseUrl ?>/assets/images/logo.png" alt="Cafe Coffea Logo" class="brand-logo-img">
                <div class="brand-text-block">
                    <span class="brand-title"><?= e($brandName) ?></span>
                    <span class="brand-subtitle"><?= e($brandTagline) ?></span>
                </div>
            </a>

            <!-- Desktop Navigation -->
            <nav class="desktop-nav" aria-label="Main Navigation">
                <a href="<?= $baseUrl ?>/index.php#gallery" class="nav-link">Gallery</a>
                <a href="<?= $baseUrl ?>/index.php#videos" class="nav-link">Videos</a>
                <a href="<?= $baseUrl ?>/index.php#pricing" class="nav-link">Packages & Charges</a>
                <a href="<?= $baseUrl ?>/index.php#instructions" class="nav-link">Instructions</a>
                <a href="<?= $baseUrl ?>/index.php#contact" class="nav-link">Contact</a>
            </nav>

            <div class="header-actions">
                <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-sm d-none-mobile">
                    <i class="fab fa-whatsapp"></i>
                    <span>Enquire Now</span>
                </a>

                <!-- Mobile Hamburger Toggle -->
                <button type="button" class="mobile-nav-toggle" aria-label="Toggle navigation menu">
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                    <span class="hamburger-line"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer Menu -->
    <div class="mobile-drawer" id="mobileDrawer">
        <div class="mobile-menu-list">
            <a href="<?= $baseUrl ?>/index.php#gallery" class="mobile-nav-link">
                <span>Photo Gallery</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="<?= $baseUrl ?>/index.php#videos" class="mobile-nav-link">
                <span>Video Tour</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="<?= $baseUrl ?>/index.php#pricing" class="mobile-nav-link">
                <span>Packages & Pricing</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="<?= $baseUrl ?>/index.php#instructions" class="mobile-nav-link">
                <span>Party Hall Rules & Policies</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="<?= $baseUrl ?>/index.php#contact" class="mobile-nav-link">
                <span>Location & Contact</span>
                <i class="fas fa-chevron-right"></i>
            </a>
            <a href="<?= $baseUrl ?>/admin/index.php" class="mobile-nav-link" style="color: var(--color-gold);">
                <span>Admin Dashboard</span>
                <i class="fas fa-lock"></i>
            </a>
        </div>

        <div class="mobile-drawer-footer">
            <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
                <i class="fab fa-whatsapp"></i>
                <span>Enquire via WhatsApp</span>
            </a>
            <a href="tel:<?= e($settings['phone_number'] ?? '+918157022220') ?>" class="btn btn-outline btn-lg">
                <i class="fas fa-phone-alt"></i>
                <span>Call Us Directly</span>
            </a>
        </div>
    </div>

