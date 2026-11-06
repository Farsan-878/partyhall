<?php
/**
 * Cafe Coffea – Cups & Moments
 * Public Site Footer Component
 */
$settings = get_all_settings();
$baseUrl = get_base_url();

$whatsappNumber = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '918157022220');
$prefillText = $settings['whatsapp_prefill_text'] ?? 'Hello Cafe Coffea! I would like to enquire about party hall booking at Cafe Coffea – Cups & Moments.';
$whatsappUrl = "https://wa.me/" . $whatsappNumber . "?text=" . rawurlencode($prefillText);
?>
    <!-- Floating WhatsApp Action on Mobile -->
    <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" class="floating-whatsapp" aria-label="Enquire on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Lightbox Modal Container for Fullscreen Image Viewing -->
    <div id="gallery-lightbox" class="lightbox-modal" role="dialog" aria-modal="true" aria-label="Image Lightbox">
        <button class="lightbox-btn lightbox-close" aria-label="Close Lightbox">
            <i class="fas fa-times"></i>
        </button>
        <button class="lightbox-btn lightbox-prev" aria-label="Previous Image">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button class="lightbox-btn lightbox-next" aria-label="Next Image">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="lightbox-content-wrap">
            <img src="" alt="Enlarged cafe photograph" class="lightbox-img">
            <div class="lightbox-caption"></div>
        </div>
    </div>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-top">
                <!-- Column 1: Brand Info -->
                <div class="footer-brand-col">
                    <a href="<?= $baseUrl ?>/index.php" class="brand-link" style="margin-bottom: 0.5rem;">
                        <img src="<?= $baseUrl ?>/assets/images/logo.png" alt="Cafe Coffea" class="brand-logo-img">
                        <div class="brand-text-block">
                            <span class="brand-title"><?= e($settings['brand_name'] ?? 'Cafe Coffea') ?></span>
                            <span class="brand-subtitle"><?= e($settings['brand_tagline'] ?? 'Cups & Moments') ?></span>
                        </div>
                    </a>
                    <p class="footer-brand-desc">
                        <?= e($settings['hero_description'] ?? 'Create beautiful memories with your friends and family in our welcoming, premium party space in Kottakkal.') ?>
                    </p>
                    <div style="margin-top: 1.25rem;">
                        <a href="<?= e($settings['official_website_url'] ?? 'https://cafecoffea.in/') ?>" target="_blank" rel="noopener noreferrer" class="footer-official-badge">
                            <i class="fas fa-globe"></i>
                            <span>Visit Official Website: cafecoffea.in</span>
                        </a>
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="footer-links-col">
                    <h4 class="footer-col-title">Explore</h4>
                    <div class="footer-links-list">
                        <a href="<?= $baseUrl ?>/index.php#gallery" class="footer-link">Photo Gallery</a>
                        <a href="<?= $baseUrl ?>/index.php#videos" class="footer-link">Video Gallery</a>
                        <a href="<?= $baseUrl ?>/index.php#pricing" class="footer-link">Packages & Charges</a>
                        <a href="<?= $baseUrl ?>/index.php#instructions" class="footer-link">Hall Rules & Policies</a>
                        <a href="<?= $baseUrl ?>/index.php#contact" class="footer-link">Location & Direction</a>
                        <a href="<?= $baseUrl ?>/admin/index.php" class="footer-link" style="color: var(--color-gold);">
                            <i class="fas fa-lock" style="font-size: 0.75rem; margin-right: 0.25rem;"></i> Admin Dashboard
                        </a>
                    </div>
                </div>

                <!-- Column 3: Contact Details -->
                <div class="footer-contact-col">
                    <h4 class="footer-col-title">Connect</h4>
                    <div class="footer-links-list">
                        <div style="font-size: 0.9rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 0.5rem;">
                            <i class="fas fa-map-marker-alt text-gold" style="margin-right: 0.4rem;"></i>
                            <?= e($settings['address'] ?? 'Ground Floor, The Souk, Swagathamad, Kottakkal, Malappuram, Kerala 676510') ?>
                        </div>
                        <a href="tel:<?= e($settings['phone_number'] ?? '+918157022220') ?>" class="footer-link">
                            <i class="fas fa-phone text-gold" style="margin-right: 0.4rem;"></i>
                            <?= e($settings['phone_number'] ?? '+91 81570 22220') ?>
                        </a>
                        <a href="<?= $whatsappUrl ?>" target="_blank" rel="noopener noreferrer" class="footer-link" style="color: var(--color-whatsapp);">
                            <i class="fab fa-whatsapp" style="margin-right: 0.4rem;"></i>
                            WhatsApp: <?= e($settings['whatsapp_number'] ?? '+91 81570 22220') ?>
                        </a>
                        <?php if (!empty($settings['instagram_url'])): ?>
                        <a href="<?= e($settings['instagram_url']) ?>" target="_blank" rel="noopener noreferrer" class="footer-link">
                            <i class="fab fa-instagram text-gold" style="margin-right: 0.4rem;"></i>
                            Follow us on Instagram
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    &copy; <?= date('Y') ?> <strong><?= e($settings['brand_name'] ?? 'Cafe Coffea') ?></strong> – <?= e($settings['brand_tagline'] ?? 'Cups & Moments') ?>. All Rights Reserved.
                </div>
                <div>
                    Designed for memorable celebrations and private gatherings.
                </div>
            </div>
        </div>
    </footer>

    <!-- Main JS Script -->
    <script src="<?= $baseUrl ?>/assets/js/main.js"></script>
</body>
</html>

