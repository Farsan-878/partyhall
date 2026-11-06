<?php
/**
 * Cafe Coffea – Admin Contact & Social Management
 */
require_once __DIR__ . '/../config/db.php';
require_admin_auth();

$db = get_db();
$baseUrl = get_base_url();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($csrf)) {
        $error = "Security token mismatch. Please try again.";
    } else {
        $fields = [
            'whatsapp_number',
            'phone_number',
            'email',
            'address',
            'google_maps_url',
            'instagram_url',
            'official_website_url'
        ];

        $stmt = $db->prepare("UPDATE settings SET value = :v WHERE key = :k");
        foreach ($fields as $f) {
            $val = trim($_POST[$f] ?? '');
            $stmt->execute([':v' => $val, ':k' => $f]);
        }

        $message = "Contact information and external links updated successfully!";
    }
}

$settings = get_all_settings();
$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management | Cafe Coffea Admin</title>
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
            <div class="page-header">
                <div>
                    <h1 class="page-title">Contact & Social Links</h1>
                    <p class="page-subtitle">Manage phone numbers, WhatsApp, café address, Google Maps, and Instagram link.</p>
                </div>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span><?= e($message) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="card">
                    <div class="card-title">
                        <span><i class="fab fa-whatsapp text-gold"></i> Phone & Messaging Details</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="whatsapp_number">WhatsApp Enquiry Number</label>
                            <input type="text" id="whatsapp_number" name="whatsapp_number" class="form-control" value="<?= e($settings['whatsapp_number'] ?? '+918157022220') ?>" required>
                            <div class="form-hint">Used for all customer booking enquiries and quick chats.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="phone_number">Voice Call Phone Number</label>
                            <input type="text" id="phone_number" name="phone_number" class="form-control" value="<?= e($settings['phone_number'] ?? '+91 81570 22220') ?>" required>
                            <div class="form-hint">Displayed on website footer and contact section.</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="email">Café Email Address</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= e($settings['email'] ?? 'contact@cafecoffea.in') ?>">
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <span><i class="fas fa-map-marker-alt text-gold"></i> Physical Location & Address</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="address">Full Street Address</label>
                        <textarea id="address" name="address" class="form-control" rows="2" required><?= e($settings['address'] ?? 'Ground Floor, The Souk, Swagathamad, Kottakkal, Malappuram, Kerala 676510, India') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="google_maps_url">Google Maps URL / Search Query</label>
                        <input type="url" id="google_maps_url" name="google_maps_url" class="form-control" value="<?= e($settings['google_maps_url'] ?? 'https://maps.google.com/?q=Cafe+Coffea+Kottakkal') ?>">
                        <div class="form-hint">Link that opens when customers click "Open in Google Maps".</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <span><i class="fas fa-link text-gold"></i> Social & Official Presence</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="official_website_url">Official Website URL</label>
                            <input type="url" id="official_website_url" name="official_website_url" class="form-control" value="<?= e($settings['official_website_url'] ?? 'https://cafecoffea.in/') ?>" required>
                            <div class="form-hint">Primary existing brand reference (https://cafecoffea.in/).</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="instagram_url">Instagram Profile Link</label>
                            <input type="url" id="instagram_url" name="instagram_url" class="form-control" value="<?= e($settings['instagram_url'] ?? 'https://instagram.com/cafecoffea') ?>">
                        </div>
                    </div>
                </div>

                <div style="margin-bottom: 3rem;">
                    <button type="submit" class="adm-btn adm-btn-primary" style="font-size: 1.05rem; padding: 0.9rem 2.2rem;">
                        <i class="fas fa-save"></i>
                        <span>Save Contact Information</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>

