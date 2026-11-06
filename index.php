<?php
/**
 * Cafe Coffea – Cups & Moments
 * Premium Party Hall Website
 * Public Customer Homepage
 */
require_once __DIR__ . '/config/db.php';

$db = get_db();
$settings = get_all_settings();
$baseUrl = get_base_url();

// Fetch Hero Image
$heroImage = !empty($settings['hero_image']) ? $settings['hero_image'] : 'assets/images/IMG_1994.jpg';
$heroImageUrl = $baseUrl . '/' . ltrim($heroImage, '/');

// Fetch Photos for Gallery
$stmtPhotos = $db->query("SELECT * FROM photos WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
$photos = $stmtPhotos->fetchAll();

// Fetch Videos for Video Gallery
$stmtVideos = $db->query("SELECT * FROM videos WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
$videos = $stmtVideos->fetchAll();

// Fetch Instructions grouped by category
$stmtRules = $db->query("SELECT * FROM instructions WHERE is_active = 1 AND category = 'rules' ORDER BY display_order ASC, rule_number ASC");
$rules = $stmtRules->fetchAll();

$stmtDecoration = $db->query("SELECT * FROM instructions WHERE is_active = 1 AND category = 'decoration' ORDER BY display_order ASC, rule_number ASC");
$decorationRules = $stmtDecoration->fetchAll();

$stmtCake = $db->query("SELECT * FROM instructions WHERE is_active = 1 AND category = 'cake' ORDER BY display_order ASC, rule_number ASC");
$cakeRules = $stmtCake->fetchAll();

// Pricing calculations
$currency = $settings['currency_symbol'] ?? '₹';
$hallPrice = (float)($settings['party_hall_price'] ?? 1000);
$cleanPrice = (float)($settings['cleaning_charge'] ?? 200);
$totalStandardPrice = $hallPrice + $cleanPrice;
$offerThreshold = (float)($settings['offer_threshold'] ?? 5000);
$offerFreeHours = $settings['offer_free_hours'] ?? '1';

// WhatsApp configuration
$whatsappNumber = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '918157022220');
$prefillText = $settings['whatsapp_prefill_text'] ?? 'Hello Cafe Coffea! I would like to enquire about party hall booking at Cafe Coffea – Cups & Moments.';
$whatsappUrl = "https://wa.me/" . $whatsappNumber . "?text=" . rawurlencode($prefillText);

// Include Header
include __DIR__ . '/includes/header.php';
?>

    <!-- =========================================================================
         HERO SECTION (Actual Cafe Interior Wide Background)
         ========================================================================= -->
    <section class="hero-section" style="background-image: url('<?= $heroImageUrl ?>');">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <!-- Brand Badge with Original Logo -->
            <div class="hero-brand-badge">
                <img src="<?= $baseUrl ?>/assets/images/logo.png" alt="Cafe Coffea" class="hero-logo-img">
                <span class="hero-brand-name"><?= e($settings['brand_name'] ?? 'Cafe Coffea') ?></span>
            </div>

            <!-- Main Heading -->
            <h1 class="hero-title text-gold-gradient"><?= e($settings['hero_heading'] ?? 'Cups & Moments') ?></h1>

            <!-- Subheading -->
            <p class="hero-subtitle"><?= e($settings['hero_subheading'] ?? 'Celebrate Your Special Moments at Cafe Coffea.') ?></p>

            <!-- Description -->
            <p class="hero-description"><?= e($settings['hero_description'] ?? 'Create beautiful memories with your friends and family in our welcoming party space.') ?></p>

            <!-- Two Call To Action Buttons -->
            <div class="hero-cta-group">
                <a href="#gallery" class="btn btn-primary btn-lg">
                    <i class="fas fa-sparkles"></i>
                    <span>Explore Our Party Hall</span>
                </a>
                <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
                    <i class="fab fa-whatsapp"></i>
                    <span>Enquire Now</span>
                </a>
            </div>

            <!-- Mouse Scroll Indicator -->
            <a href="#quick-nav" class="hero-scroll-indicator" aria-label="Scroll to content">
                <span>Discover More</span>
                <div class="scroll-mouse">
                    <div class="scroll-wheel"></div>
                </div>
            </a>
        </div>
    </section>


    <!-- =========================================================================
         QUICK NAVIGATION (4 Attractive Shortcut Cards)
         ========================================================================= -->
    <section class="quick-nav-section" id="quick-nav">
        <div class="container">
            <div class="quick-nav-grid">
                <!-- 1. Gallery -->
                <a href="#gallery" class="quick-card">
                    <div class="quick-icon-wrap">
                        <i class="fas fa-images"></i>
                    </div>
                    <h3 class="quick-title">Gallery</h3>
                    <p class="quick-desc">Hall photos & setup</p>
                </a>

                <!-- 2. Videos -->
                <a href="#videos" class="quick-card">
                    <div class="quick-icon-wrap">
                        <i class="fas fa-play"></i>
                    </div>
                    <h3 class="quick-title">Videos</h3>
                    <p class="quick-desc">Atmosphere & tour</p>
                </a>

                <!-- 3. Packages & Charges -->
                <a href="#pricing" class="quick-card">
                    <div class="quick-icon-wrap">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h3 class="quick-title">Packages & Charges</h3>
                    <p class="quick-desc">Tariff & special offer</p>
                </a>

                <!-- 4. Instructions -->
                <a href="#instructions" class="quick-card">
                    <div class="quick-icon-wrap">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3 class="quick-title">Instructions</h3>
                    <p class="quick-desc">Rules & policies</p>
                </a>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         PHOTO GALLERY (Scanned Actual Photographs with Filter & Lightbox)
         ========================================================================= -->
    <section class="section section-dark" id="gallery">
        <div class="container">
            <div class="text-center">
                <div class="section-tag">
                    <i class="fas fa-camera-retro"></i>
                    <span>Real Experience</span>
                </div>
                <h2 class="section-title">Party Hall Photo Gallery</h2>
                <p class="section-subtitle">
                    Immerse yourself in our beautifully styled party hall interior, cozy stage seating, designer lighting, and inviting party setups.
                </p>

                <!-- Filter Tabs -->
                <div class="gallery-filters">
                    <button type="button" class="gallery-filter-btn active" data-filter="all">All Photos</button>
                    <button type="button" class="gallery-filter-btn" data-filter="stage">Stage & Backdrop</button>
                    <button type="button" class="gallery-filter-btn" data-filter="interior">Hall Interior</button>
                    <button type="button" class="gallery-filter-btn" data-filter="seating">Seating & Dining</button>
                    <button type="button" class="gallery-filter-btn" data-filter="decor">Ambiance & Decor</button>
                </div>
            </div>

            <!-- Responsive Gallery Grid -->
            <div class="gallery-grid">
                <?php if (!empty($photos)): ?>
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
                <?php else: ?>
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--text-muted);">
                        <i class="fas fa-images" style="font-size: 2.5rem; margin-bottom: 1rem; color: var(--color-gold);"></i>
                        <p>No gallery photographs uploaded yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         VIDEO GALLERY (Actual Uploaded Videos with HTML5 Player)
         ========================================================================= -->
    <section class="section section-surface" id="videos">
        <div class="container">
            <div class="text-center">
                <div class="section-tag">
                    <i class="fas fa-film"></i>
                    <span>Video Showcase</span>
                </div>
                <h2 class="section-title">Experience the Vibe in Motion</h2>
                <p class="section-subtitle">
                    Take a virtual walkthrough of our party space and feel the welcoming warmth and cozy aesthetic of Cafe Coffea.
                </p>
            </div>

            <div class="video-grid">
                <?php if (!empty($videos)): ?>
                    <?php foreach ($videos as $video): 
                        $videoSrc = $baseUrl . '/' . ltrim($video['file_path'], '/');
                        $posterSrc = !empty($video['poster_path']) ? $baseUrl . '/' . ltrim($video['poster_path'], '/') : '';
                    ?>
                    <div class="video-card">
                        <div class="video-player-container">
                            <video class="video-element" 
                                   controls 
                                   playsinline 
                                   preload="metadata" 
                                   poster="<?= $posterSrc ?>">
                                <source src="<?= $videoSrc ?>" type="video/mp4">
                                <source src="<?= $videoSrc ?>" type="video/quicktime">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="video-card-body">
                            <h3 class="video-title"><?= e($video['title'] ?? 'Party Hall Video Tour') ?></h3>
                            <p class="video-description"><?= e($video['description'] ?? 'Exclusive moments at Cafe Coffea Party Hall.') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        <p>No video files uploaded yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         PACKAGES & PRICING (Transparent Breakdown & Special Free Offer)
         ========================================================================= -->
    <section class="section section-dark" id="pricing">
        <div class="container">
            <div class="text-center">
                <div class="section-tag">
                    <i class="fas fa-gem"></i>
                    <span>Simple & Transparent</span>
                </div>
                <h2 class="section-title">Packages & Charges</h2>
                <p class="section-subtitle">
                    Enjoy our luxurious private party hall with honest, straightforward pricing and zero hidden fees.
                </p>
            </div>

            <div class="pricing-wrapper">
                <div class="pricing-card-main">
                    <div class="pricing-header">
                        <span class="section-tag">Standard Booking Rate</span>
                        <h3 style="font-family: var(--font-heading); font-size: 1.85rem; color: var(--text-primary); margin-top: 0.5rem;">
                            1-Hour Party Hall Package
                        </h3>
                    </div>

                    <!-- Itemized Breakdown -->
                    <div class="pricing-breakdown-list">
                        <!-- Party Hall Charge -->
                        <div class="pricing-item">
                            <div class="pricing-item-info">
                                <div class="pricing-item-icon">
                                    <i class="fas fa-door-open"></i>
                                </div>
                                <div>
                                    <div class="pricing-item-title">Party Hall Charge</div>
                                    <div class="pricing-item-subtitle">Exclusive use of the private party space for 1 full hour</div>
                                </div>
                            </div>
                            <div class="pricing-item-amount"><?= $currency ?><?= number_format($hallPrice) ?></div>
                        </div>

                        <!-- Cleaning Charge -->
                        <div class="pricing-item">
                            <div class="pricing-item-info">
                                <div class="pricing-item-icon">
                                    <i class="fas fa-broom"></i>
                                </div>
                                <div>
                                    <div class="pricing-item-title">Cleaning Charge</div>
                                    <div class="pricing-item-subtitle">Fixed post-event sanitization and hall cleanup</div>
                                </div>
                            </div>
                            <div class="pricing-item-amount"><?= $currency ?><?= number_format($cleanPrice) ?></div>
                        </div>
                    </div>

                    <!-- Total Standard Booking Box -->
                    <div class="pricing-total-box">
                        <div>
                            <div class="total-label">Total for Standard 1-Hour Booking</div>
                            <div class="total-sub">Includes 1 hour hall duration + thorough post-event cleaning</div>
                        </div>
                        <div class="total-amount"><?= $currency ?><?= number_format($totalStandardPrice) ?></div>
                    </div>

                    <!-- Special Offer Privilege Card -->
                    <div class="special-offer-card">
                        <div class="offer-badge">
                            <i class="fas fa-gift"></i>
                            <span><?= e($settings['offer_headline'] ?? 'Special Privilege Offer') ?></span>
                        </div>
                        <h4 class="offer-title">
                            Reach <?= $currency ?><?= number_format($offerThreshold) ?> in Cafe Coffea Orders = <?= e($offerFreeHours) ?> Hour Hall Usage FREE!
                        </h4>
                        <p class="offer-description">
                            <?= e($settings['offer_description'] ?? 'When your eligible Cafe Coffea purchase amount reaches ₹5,000 or above, receive 1 hour of party hall usage completely FREE.') ?>
                        </p>
                        <div class="offer-terms">
                            <strong>Terms & Conditions:</strong>
                            <?= e($settings['offer_terms'] ?? 'The free usage is strictly limited to exactly one hour. Valid on food and beverage bills on the event day. Standard cleaning charges and overtime policies apply.') ?>
                        </div>
                    </div>

                    <!-- Booking Actions -->
                    <div class="pricing-actions">
                        <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
                            <i class="fab fa-whatsapp"></i>
                            <span>Reserve via WhatsApp</span>
                        </a>
                        <a href="#instructions" class="btn btn-outline btn-lg">
                            <i class="fas fa-info-circle"></i>
                            <span>Review Rules & Policies</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         INSTRUCTIONS & TERMS AND CONDITIONS (Rules, Decoration, Cake Policies)
         ========================================================================= -->
    <section class="section section-surface" id="instructions">
        <div class="container">
            <div class="text-center">
                <div class="section-tag">
                    <i class="fas fa-shield-alt"></i>
                    <span>Guidelines for Guests</span>
                </div>
                <h2 class="section-title">Instructions & Policies</h2>
                <p class="section-subtitle">
                    To maintain our café's pristine ambiance and ensure a seamless experience for every guest, please review the party hall policies below.
                </p>
            </div>

            <div class="instructions-container">
                <!-- 1. Party Hall Rules -->
                <div class="policy-card">
                    <div class="policy-header">
                        <div class="policy-icon-wrap">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h3 class="policy-title">Party Hall Rules</h3>
                            <p class="policy-subtitle">Duration, booking tariffs, and timing adherence</p>
                        </div>
                    </div>
                    <div class="policy-rules-list">
                        <?php foreach ($rules as $r): ?>
                        <div class="rule-item">
                            <div class="rule-number"><?= e($r['rule_number']) ?></div>
                            <div class="rule-text">
                                <strong><?= e($r['title']) ?>:</strong> <?= e($r['description']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 2. Decoration Policy -->
                <div class="policy-card">
                    <div class="policy-header">
                        <div class="policy-icon-wrap">
                            <i class="fas fa-paint-brush"></i>
                        </div>
                        <div>
                            <h3 class="policy-title">Decoration Policy</h3>
                            <p class="policy-subtitle">Wallpaper preservation, safe methods, and property care</p>
                        </div>
                    </div>
                    <div class="policy-rules-list">
                        <?php foreach ($decorationRules as $d): ?>
                        <div class="rule-item">
                            <div class="rule-number"><?= e($d['rule_number']) ?></div>
                            <div class="rule-text">
                                <strong><?= e($d['title']) ?>:</strong> <?= e($d['description']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 3. Cake Policy -->
                <div class="policy-card">
                    <div class="policy-header">
                        <div class="policy-icon-wrap">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <div>
                            <h3 class="policy-title">Cake Policy</h3>
                            <p class="policy-subtitle">In-house cake orders, outside cakes, and food restrictions</p>
                        </div>
                    </div>
                    <div class="policy-rules-list">
                        <?php foreach ($cakeRules as $c): ?>
                        <div class="rule-item">
                            <div class="rule-number"><?= e($c['rule_number']) ?></div>
                            <div class="rule-text">
                                <strong><?= e($c['title']) ?>:</strong> <?= e($c['description']) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         WHATSAPP ENQUIRY BANNER (Quick Direct Booking Assistant)
         ========================================================================= -->
    <section class="section section-dark" style="padding-top: 0; padding-bottom: 2rem;">
        <div class="container">
            <div class="whatsapp-banner">
                <div class="whatsapp-content">
                    <div class="section-tag" style="background: rgba(37, 211, 102, 0.15); border-color: rgba(37, 211, 102, 0.4); color: #25D366;">
                        <i class="fab fa-whatsapp"></i>
                        <span>Instant Support</span>
                    </div>
                    <h3 class="whatsapp-title">Ready to Plan Your Celebration?</h3>
                    <p class="whatsapp-text">
                        Enquire about party hall availability, event dates, cake pre-orders, and eligible discounts directly with our manager on WhatsApp.
                    </p>
                </div>
                <div>
                    <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-whatsapp btn-lg">
                        <i class="fab fa-whatsapp" style="font-size: 1.25rem;"></i>
                        <span>Chat on WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </section>


    <!-- =========================================================================
         CONTACT & LOCATION SECTION (Verified Details & Google Maps)
         ========================================================================= -->
    <section class="section section-surface" id="contact">
        <div class="container">
            <div class="text-center">
                <div class="section-tag">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>Find & Visit Us</span>
                </div>
                <h2 class="section-title">Location & Contact</h2>
                <p class="section-subtitle">
                    Conveniently located at The Souk in Swagathamad, Kottakkal. Drop in for a cup of coffee or tour our party hall in person!
                </p>
            </div>

            <div class="contact-grid">
                <!-- Contact Information Card -->
                <div class="contact-card">
                    <div style="display: flex; align-items: center; gap: 0.85rem; margin-bottom: 1.5rem;">
                        <img src="<?= $baseUrl ?>/assets/images/logo.png" alt="Cafe Coffea" style="width: 44px; height: 44px; object-fit: contain;">
                        <div>
                            <h3 style="font-family: var(--font-heading); font-size: 1.25rem; color: var(--text-primary);">
                                <?= e($settings['brand_name'] ?? 'Cafe Coffea') ?>
                            </h3>
                            <span style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--color-gold);">
                                <?= e($settings['brand_tagline'] ?? 'Cups & Moments') ?>
                            </span>
                        </div>
                    </div>

                    <div class="contact-items-list">
                        <!-- Address -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <div class="contact-item-title">Address</div>
                                <div class="contact-item-val"><?= e($settings['address'] ?? 'Ground Floor, The Souk, Swagathamad, Kottakkal, Malappuram, Kerala 676510, India') ?></div>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <div class="contact-item-title">Phone Number</div>
                                <div class="contact-item-val">
                                    <a href="tel:<?= e($settings['phone_number'] ?? '+918157022220') ?>" style="color: var(--color-gold-light);">
                                        <?= e($settings['phone_number'] ?? '+91 81570 22220') ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="contact-item">
                            <div class="contact-icon" style="color: var(--color-whatsapp); background: rgba(37, 211, 102, 0.1);">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div>
                                <div class="contact-item-title">WhatsApp Booking</div>
                                <div class="contact-item-val">
                                    <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" style="color: var(--color-whatsapp);">
                                        <?= e($settings['whatsapp_number'] ?? '+91 81570 22220') ?>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Official Website -->
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <div class="contact-item-title">Official Website</div>
                                <div class="contact-item-val">
                                    <a href="<?= e($settings['official_website_url'] ?? 'https://cafecoffea.in/') ?>" target="_blank" rel="noopener noreferrer" style="color: var(--color-gold);">
                                        <?= e($settings['official_website_url'] ?? 'https://cafecoffea.in/') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Google Maps Embed / Direct Navigation -->
                <div class="contact-card" style="padding: 0; overflow: hidden;">
                    <div class="map-container">
                        <iframe 
                            src="https://maps.google.com/maps?q=Cafe+Coffea+The+Souk+Swagathamad+Kottakkal&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Cafe Coffea Kottakkal Location Map">
                        </iframe>
                    </div>
                    <div style="padding: 1.25rem; display: flex; justify-content: space-between; align-items: center; background: var(--color-bg-surface-elevated);">
                        <span style="font-size: 0.85rem; color: var(--text-muted);">
                            <i class="fas fa-directions text-gold" style="margin-right: 0.4rem;"></i> The Souk, Kottakkal
                        </span>
                        <a href="https://maps.google.com/?q=Cafe+Coffea+Kottakkal" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">
                            <span>Open in Google Maps</span>
                            <i class="fas fa-external-link-alt" style="font-size: 0.75rem;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Include Footer
include __DIR__ . '/includes/footer.php';
?>

