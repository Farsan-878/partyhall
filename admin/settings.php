<?php
/**
 * Cafe Coffea – Admin Website Content & Settings
 */
require_once __DIR__ . '/../config/db.php';
require_admin_auth();

$db = get_db();
$baseUrl = get_base_url();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        $error = "Security token mismatch. Please try again.";
    } elseif ($action === 'update_content') {
        $fields = [
            'site_title',
            'brand_name',
            'brand_tagline',
            'hero_heading',
            'hero_subheading',
            'hero_description',
            'hero_image',
            'whatsapp_prefill_text'
        ];

        $stmt = $db->prepare("UPDATE settings SET value = :v WHERE key = :k");
        foreach ($fields as $f) {
            if (isset($_POST[$f])) {
                $stmt->execute([':v' => trim($_POST[$f]), ':k' => $f]);
            }
        }

        // Also sync photo table is_hero flag
        $selectedHero = trim($_POST['hero_image'] ?? '');
        if (!empty($selectedHero)) {
            $db->exec("UPDATE photos SET is_hero = 0");
            $db->prepare("UPDATE photos SET is_hero = 1 WHERE file_path = :f")->execute([':f' => $selectedHero]);
        }

        $message = "Website hero content and branding settings updated successfully!";
    } elseif ($action === 'change_password') {
        $currPass = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confPass = $_POST['confirm_password'] ?? '';

        $adminId = $_SESSION['admin_id'];
        $stmt = $db->prepare("SELECT password_hash FROM admins WHERE id = :id");
        $stmt->execute([':id' => $adminId]);
        $hash = $stmt->fetchColumn();

        if (!password_verify($currPass, $hash)) {
            $error = "Incorrect current password.";
        } elseif (strlen($newPass) < 6) {
            $error = "New password must be at least 6 characters long.";
        } elseif ($newPass !== $confPass) {
            $error = "New password and confirmation do not match.";
        } else {
            $newHash = password_hash($newPass, PASSWORD_BCRYPT);
            $db->prepare("UPDATE admins SET password_hash = :h WHERE id = :id")->execute([':h' => $newHash, ':id' => $adminId]);
            $message = "Admin password updated successfully!";
        }
    }
}

$settings = get_all_settings();
$photos = $db->query("SELECT * FROM photos ORDER BY display_order ASC")->fetchAll();
$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Settings | Cafe Coffea Admin</title>
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
                    <h1 class="page-title">Website Settings</h1>
                    <p class="page-subtitle">Configure hero titles, descriptions, WhatsApp templates, and admin security.</p>
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

            <!-- Hero Section & Branding Settings -->
            <form method="POST" action="">
                <input type="hidden" name="action" value="update_content">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="card">
                    <div class="card-title">
                        <span><i class="fas fa-magic text-gold"></i> Hero Section Presentation</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="hero_heading">Hero Main Heading</label>
                            <input type="text" id="hero_heading" name="hero_heading" class="form-control" value="<?= e($settings['hero_heading'] ?? 'Cups & Moments') ?>" required>
                            <div class="form-hint">Prominent large title (Default: Cups & Moments).</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="hero_subheading">Hero Subheading</label>
                            <input type="text" id="hero_subheading" name="hero_subheading" class="form-control" value="<?= e($settings['hero_subheading'] ?? 'Celebrate Your Special Moments at Cafe Coffea.') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="hero_description">Hero Description</label>
                        <textarea id="hero_description" name="hero_description" class="form-control" rows="3"><?= e($settings['hero_description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="hero_image">Main Hero Background Photograph</label>
                        <select id="hero_image" name="hero_image" class="form-control">
                            <?php foreach ($photos as $p): ?>
                                <option value="<?= e($p['file_path']) ?>" <?= ($settings['hero_image'] ?? '') === $p['file_path'] ? 'selected' : '' ?>>
                                    <?= e($p['title']) ?> (<?= basename($p['file_path']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-hint">Choose any wide café interior photograph as the background.</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <span><i class="fab fa-whatsapp text-gold"></i> WhatsApp Pre-Filled Message Template</span>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="whatsapp_prefill_text">Pre-filled Message Template</label>
                        <textarea id="whatsapp_prefill_text" name="whatsapp_prefill_text" class="form-control" rows="4"><?= e($settings['whatsapp_prefill_text'] ?? '') ?></textarea>
                        <div class="form-hint">Text automatically typed into the customer's WhatsApp when they tap "Enquire Now".</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">
                        <span><i class="fas fa-tag text-gold"></i> Brand Identity & Page Title</span>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="brand_name">Brand Name</label>
                            <input type="text" id="brand_name" name="brand_name" class="form-control" value="<?= e($settings['brand_name'] ?? 'Cafe Coffea') ?>" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="brand_tagline">Tagline</label>
                            <input type="text" id="brand_tagline" name="brand_tagline" class="form-control" value="<?= e($settings['brand_tagline'] ?? 'Cups & Moments') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="site_title">Browser Page Title</label>
                        <input type="text" id="site_title" name="site_title" class="form-control" value="<?= e($settings['site_title'] ?? 'Cafe Coffea – Cups & Moments') ?>" required>
                    </div>
                </div>

                <div style="margin-bottom: 2rem;">
                    <button type="submit" class="adm-btn adm-btn-primary" style="font-size: 1.05rem; padding: 0.9rem 2.2rem;">
                        <i class="fas fa-save"></i>
                        <span>Save Website Settings</span>
                    </button>
                </div>
            </form>

            <!-- Admin Password Change Card -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-key text-gold"></i> Change Admin Password</span>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="new_password">New Password (min 6 characters)</label>
                            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm New Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6">
                        </div>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-outline">
                        <i class="fas fa-lock"></i>
                        <span>Update Password</span>
                    </button>
                </form>
            </div>
        </div>
    </main>
</div>
</body>
</html>

