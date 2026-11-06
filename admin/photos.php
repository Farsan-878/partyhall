<?php
/**
 * Cafe Coffea – Admin Photo Management
 */
require_once __DIR__ . '/../config/db.php';
require_admin_auth();

$db = get_db();
$baseUrl = get_base_url();
$message = '';
$error = '';

// Handle Actions: Upload, Delete, Set Hero, Reorder, Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        $error = "Security token mismatch. Please try again.";
    } elseif ($action === 'set_hero') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $stmt = $db->prepare("SELECT file_path FROM photos WHERE id = :id");
        $stmt->execute([':id' => $photoId]);
        $filePath = $stmt->fetchColumn();

        if ($filePath) {
            // Update settings table
            $db->prepare("UPDATE settings SET value = :v WHERE key = 'hero_image'")->execute([':v' => $filePath]);
            // Update photos table hero flag
            $db->exec("UPDATE photos SET is_hero = 0");
            $db->prepare("UPDATE photos SET is_hero = 1 WHERE id = :id")->execute([':id' => $photoId]);
            $message = "Homepage hero background updated successfully!";
        }
    } elseif ($action === 'delete') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $stmt = $db->prepare("SELECT file_path, thumb_path FROM photos WHERE id = :id");
        $stmt->execute([':id' => $photoId]);
        $row = $stmt->fetch();

        if ($row) {
            $db->prepare("DELETE FROM photos WHERE id = :id")->execute([':id' => $photoId]);
            $message = "Photograph removed from gallery.";
        }
    } elseif ($action === 'reorder') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $direction = $_POST['direction'] ?? 'up';

        $stmt = $db->prepare("SELECT display_order FROM photos WHERE id = :id");
        $stmt->execute([':id' => $photoId]);
        $currentOrder = (int)$stmt->fetchColumn();

        if ($direction === 'up' && $currentOrder > 1) {
            $newOrder = $currentOrder - 1;
            $db->prepare("UPDATE photos SET display_order = :curr WHERE display_order = :target")->execute([':curr' => $currentOrder, ':target' => $newOrder]);
            $db->prepare("UPDATE photos SET display_order = :target WHERE id = :id")->execute([':target' => $newOrder, ':id' => $photoId]);
        } elseif ($direction === 'down') {
            $newOrder = $currentOrder + 1;
            $db->prepare("UPDATE photos SET display_order = :curr WHERE display_order = :target")->execute([':curr' => $currentOrder, ':target' => $newOrder]);
            $db->prepare("UPDATE photos SET display_order = :target WHERE id = :id")->execute([':target' => $newOrder, ':id' => $photoId]);
        }
        $message = "Photo ordering updated.";
    } elseif ($action === 'edit_caption') {
        $photoId = (int)($_POST['photo_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $category = trim($_POST['category'] ?? 'general');

        $stmt = $db->prepare("UPDATE photos SET title = :t, category = :c WHERE id = :id");
        $stmt->execute([':t' => $title, ':c' => $category, ':id' => $photoId]);
        $message = "Photograph details updated successfully.";
    } elseif ($action === 'upload') {
        if (!empty($_FILES['photo_file']['name'])) {
            $file = $_FILES['photo_file'];
            $title = trim($_POST['title'] ?? '') ?: pathinfo($file['name'], PATHINFO_FILENAME);
            $category = trim($_POST['category'] ?? 'general');

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'heic'];

            if (!in_array($ext, $allowedExts)) {
                $error = "Invalid file type. Allowed formats: JPG, PNG, WEBP, HEIC.";
            } elseif ($file['size'] > 25 * 1024 * 1024) {
                $error = "File is too large. Maximum size is 25MB.";
            } else {
                $uniqueName = 'cafe_' . time() . '_' . bin2hex(random_bytes(4));
                $destFileName = $uniqueName . '.' . ($ext === 'heic' ? 'jpg' : $ext);
                $destPath = ROOT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . $destFileName;
                $thumbPath = ROOT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'photos' . DIRECTORY_SEPARATOR . 'thumb_' . $destFileName;

                $uploadSuccess = false;

                if ($ext === 'heic') {
                    // Convert HEIC via PowerShell script
                    $tempSource = $file['tmp_name'];
                    $psScript = ROOT_DIR . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'convert_heic.ps1';
                    // Run WIC conversion command
                    $escapedTemp = escapeshellarg($tempSource);
                    $escapedDest = escapeshellarg($destPath);
                    $cmd = "powershell -ExecutionPolicy Bypass -Command \"Add-Type -AssemblyName PresentationCore; \$uri = New-Object System.Uri('$escapedTemp'); \$dec = [System.Windows.Media.Imaging.BitmapDecoder]::Create(\$uri, [System.Windows.Media.Imaging.BitmapCreateOptions]::None, [System.Windows.Media.Imaging.BitmapCacheOption]::OnLoad); \$enc = New-Object System.Windows.Media.Imaging.JpegBitmapEncoder; \$enc.QualityLevel = 88; \$enc.Frames.Add(\$dec.Frames[0]); \$fs = [System.IO.File]::Create('$escapedDest'); \$enc.Save(\$fs); \$fs.Close();\"";
                    @exec($cmd);
                    if (file_exists($destPath)) {
                        $uploadSuccess = true;
                    }
                } else {
                    if (move_uploaded_file($file['tmp_name'], $destPath)) {
                        $uploadSuccess = true;
                    }
                }

                if ($uploadSuccess) {
                    // Try to generate thumbnail or use same image as fallback
                    copy($destPath, $thumbPath);

                    $relPath = 'uploads/photos/' . $destFileName;
                    $relThumb = 'uploads/photos/' . 'thumb_' . $destFileName;

                    $maxOrder = (int)$db->query("SELECT MAX(display_order) FROM photos")->fetchColumn() + 1;
                    $stmt = $db->prepare("INSERT INTO photos (file_path, thumb_path, title, category, display_order, is_hero, is_active) VALUES (:f, :t, :title, :c, :ord, 0, 1)");
                    $stmt->execute([
                        ':f' => $relPath,
                        ':t' => $relThumb,
                        ':title' => $title,
                        ':c' => $category,
                        ':ord' => $maxOrder
                    ]);

                    $message = "Photograph uploaded and added to the gallery successfully!";
                } else {
                    $error = "Failed to upload or process the image file.";
                }
            }
        } else {
            $error = "Please select an image file to upload.";
        }
    }
}

// Fetch current active Hero Image
$currentHero = $db->query("SELECT value FROM settings WHERE key = 'hero_image'")->fetchColumn() ?: 'assets/images/IMG_1994.jpg';

// Fetch all photos
$photos = $db->query("SELECT * FROM photos ORDER BY display_order ASC, id ASC")->fetchAll();
$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Management | Cafe Coffea Admin</title>
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
                    <h1 class="page-title">Photo Management</h1>
                    <p class="page-subtitle">Upload new photos, reorder images, set hero background, and manage gallery display.</p>
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

            <!-- Upload Card -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-cloud-upload-alt text-gold"></i> Upload New Photograph</span>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="photo_file">Select Photo (JPG, PNG, WEBP, HEIC)</label>
                            <input type="file" id="photo_file" name="photo_file" class="form-control" accept="image/jpeg,image/png,image/webp,.heic" required>
                            <div class="form-hint">Supports high-res mobile and camera photos up to 25MB.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="title">Photo Title / Caption</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="e.g. VIP Dining Area or Cake Cutting Stage">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="category">Category Filter</label>
                            <select id="category" name="category" class="form-control">
                                <option value="stage">Stage & Backdrop</option>
                                <option value="interior">Hall Interior</option>
                                <option value="seating">Seating & Dining</option>
                                <option value="decor">Ambiance & Decor</option>
                                <option value="general">General Hall</option>
                            </select>
                        </div>

                        <div class="form-group" style="display: flex; align-items: flex-end;">
                            <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%;">
                                <i class="fas fa-upload"></i>
                                <span>Upload to Gallery</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Gallery Photos -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-images text-gold"></i> Existing Photos (<?= count($photos) ?>)</span>
                </div>

                <div class="media-grid">
                    <?php foreach ($photos as $photo): 
                        $imgUrl = $baseUrl . '/' . ltrim($photo['file_path'], '/');
                        $thumbUrl = !empty($photo['thumb_path']) ? $baseUrl . '/' . ltrim($photo['thumb_path'], '/') : $imgUrl;
                        $isHero = ($photo['file_path'] === $currentHero || $photo['is_hero'] == 1);
                    ?>
                    <div class="media-card" style="<?= $isHero ? 'border-color: var(--adm-gold); box-shadow: 0 0 15px rgba(197,155,99,0.3);' : '' ?>">
                        <div style="position: relative;">
                            <img src="<?= $thumbUrl ?>" alt="<?= e($photo['title']) ?>" class="media-card-img" loading="lazy">
                            <?php if ($isHero): ?>
                                <span style="position: absolute; top: 8px; left: 8px; background: var(--adm-gold); color: #120D0A; font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.6rem; border-radius: 99px; text-transform: uppercase;">
                                    <i class="fas fa-star"></i> Hero Image
                                </span>
                            <?php endif; ?>
                            <span style="position: absolute; top: 8px; right: 8px; background: rgba(0,0,0,0.7); color: #FFF; font-size: 0.7rem; padding: 0.2rem 0.5rem; border-radius: 4px;">
                                #<?= $photo['display_order'] ?>
                            </span>
                        </div>

                        <div class="media-card-body">
                            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 0.5rem;">
                                <input type="hidden" name="action" value="edit_caption">
                                <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                                <input type="text" name="title" class="form-control" value="<?= e($photo['title']) ?>" style="padding: 0.4rem 0.6rem; font-size: 0.85rem;" title="Edit Title">
                                
                                <div style="display: flex; gap: 0.5rem;">
                                    <select name="category" class="form-control" style="padding: 0.35rem 0.5rem; font-size: 0.8rem;">
                                        <option value="stage" <?= $photo['category'] === 'stage' ? 'selected' : '' ?>>Stage</option>
                                        <option value="interior" <?= $photo['category'] === 'interior' ? 'selected' : '' ?>>Interior</option>
                                        <option value="seating" <?= $photo['category'] === 'seating' ? 'selected' : '' ?>>Seating</option>
                                        <option value="decor" <?= $photo['category'] === 'decor' ? 'selected' : '' ?>>Decor</option>
                                        <option value="general" <?= $photo['category'] === 'general' ? 'selected' : '' ?>>General</option>
                                    </select>
                                    <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm" title="Save Caption Changes">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </form>

                            <div class="media-card-actions">
                                <!-- Set as Hero Button -->
                                <?php if (!$isHero): ?>
                                    <form method="POST" action="" style="flex: 1;">
                                        <input type="hidden" name="action" value="set_hero">
                                        <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                        <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm" style="width: 100%; color: var(--adm-gold);">
                                            <i class="fas fa-crown"></i>
                                            <span>Set as Hero</span>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="flex: 1; font-size: 0.75rem; color: var(--adm-gold); font-weight: 700; text-align: center;">
                                        <i class="fas fa-check"></i> Active Hero
                                    </span>
                                <?php endif; ?>

                                <!-- Reorder Up/Down -->
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="reorder">
                                    <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                    <input type="hidden" name="direction" value="up">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm" title="Move Left / Earlier">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                </form>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="reorder">
                                    <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                    <input type="hidden" name="direction" value="down">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <button type="submit" class="adm-btn adm-btn-outline adm-btn-sm" title="Move Right / Later">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </form>

                                <!-- Delete -->
                                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to remove this photo from the gallery?');" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                                    <button type="submit" class="adm-btn adm-btn-danger adm-btn-sm" title="Delete Photo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</div>
</body>
</html>

