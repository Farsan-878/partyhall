<?php
/**
 * Cafe Coffea – Admin Video Management
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
        $error = "Security token invalid. Please try again.";
    } elseif ($action === 'delete') {
        $videoId = (int)($_POST['video_id'] ?? 0);
        $db->prepare("DELETE FROM videos WHERE id = :id")->execute([':id' => $videoId]);
        $message = "Video removed successfully.";
    } elseif ($action === 'edit') {
        $videoId = (int)($_POST['video_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $posterPath = trim($_POST['poster_path'] ?? '');

        $stmt = $db->prepare("UPDATE videos SET title = :t, description = :d, poster_path = :p WHERE id = :id");
        $stmt->execute([':t' => $title, ':d' => $description, ':p' => $posterPath, ':id' => $videoId]);
        $message = "Video details updated successfully.";
    } elseif ($action === 'reorder') {
        $videoId = (int)($_POST['video_id'] ?? 0);
        $direction = $_POST['direction'] ?? 'up';

        $stmt = $db->prepare("SELECT display_order FROM videos WHERE id = :id");
        $stmt->execute([':id' => $videoId]);
        $curr = (int)$stmt->fetchColumn();

        $target = ($direction === 'up') ? $curr - 1 : $curr + 1;
        if ($target > 0) {
            $db->prepare("UPDATE videos SET display_order = :curr WHERE display_order = :target")->execute([':curr' => $curr, ':target' => $target]);
            $db->prepare("UPDATE videos SET display_order = :target WHERE id = :id")->execute([':target' => $target, ':id' => $videoId]);
            $message = "Video order updated.";
        }
    } elseif ($action === 'upload') {
        if (!empty($_FILES['video_file']['name'])) {
            $file = $_FILES['video_file'];
            $title = trim($_POST['title'] ?? '') ?: pathinfo($file['name'], PATHINFO_FILENAME);
            $description = trim($_POST['description'] ?? '');
            $posterPath = trim($_POST['poster_path'] ?? 'assets/images/thumbs/IMG_1994.jpg');

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['mp4', 'mov', 'webm', 'm4v'];

            if (!in_array($ext, $allowedExts)) {
                $error = "Invalid video format. Allowed formats: MP4, MOV, WebM.";
            } elseif ($file['size'] > 150 * 1024 * 1024) {
                $error = "Video file exceeds maximum limit of 150MB.";
            } else {
                $uniqueName = 'video_' . time() . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $destPath = ROOT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $uniqueName;

                if (move_uploaded_file($file['tmp_name'], $destPath)) {
                    $relPath = 'uploads/videos/' . $uniqueName;
                    $maxOrder = (int)$db->query("SELECT MAX(display_order) FROM videos")->fetchColumn() + 1;

                    $stmt = $db->prepare("INSERT INTO videos (file_path, poster_path, title, description, display_order, is_active) VALUES (:f, :p, :t, :d, :ord, 1)");
                    $stmt->execute([
                        ':f' => $relPath,
                        ':p' => $posterPath,
                        ':t' => $title,
                        ':d' => $description,
                        ':ord' => $maxOrder
                    ]);
                    $message = "New video uploaded and published successfully!";
                } else {
                    $error = "Failed to upload video file to server.";
                }
            }
        } else {
            $error = "Please select a video file to upload.";
        }
    }
}

$videos = $db->query("SELECT * FROM videos ORDER BY display_order ASC, id ASC")->fetchAll();
$photos = $db->query("SELECT * FROM photos ORDER BY display_order ASC")->fetchAll();
$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Management | Cafe Coffea Admin</title>
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
                    <h1 class="page-title">Video Management</h1>
                    <p class="page-subtitle">Upload party hall walkthroughs, edit descriptions, assign poster images, and manage video order.</p>
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

            <!-- Upload Video Card -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-video text-gold"></i> Upload New Video</span>
                </div>
                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="upload">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="video_file">Video File (MP4, MOV, WebM)</label>
                            <input type="file" id="video_file" name="video_file" class="form-control" accept="video/mp4,video/quicktime,video/webm" required>
                            <div class="form-hint">Supports MP4 and iPhone MOV video files up to 150MB.</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="title">Video Title</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Stage Tour & Ambience" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="poster_path">Cover Poster Thumbnail</label>
                            <select id="poster_path" name="poster_path" class="form-control">
                                <?php foreach ($photos as $p): ?>
                                    <option value="<?= e($p['thumb_path'] ?: $p['file_path']) ?>">
                                        <?= e($p['title']) ?> (<?= basename($p['file_path']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="description">Short Description</label>
                            <input type="text" id="description" name="description" class="form-control" placeholder="e.g. Highlights of our party space in Kottakkal.">
                        </div>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Upload Video</span>
                    </button>
                </form>
            </div>

            <!-- Existing Videos Grid -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-film text-gold"></i> Live Videos (<?= count($videos) ?>)</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <?php foreach ($videos as $v): 
                        $videoSrc = $baseUrl . '/' . ltrim($v['file_path'], '/');
                        $posterSrc = !empty($v['poster_path']) ? $baseUrl . '/' . ltrim($v['poster_path'], '/') : '';
                    ?>
                    <div class="card" style="margin-bottom: 0; background: var(--adm-card-hover);">
                        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
                            <div style="width: 100%; max-width: 500px; aspect-ratio: 16 / 9; background: #000; border-radius: 8px; overflow: hidden;">
                                <video controls playsinline preload="metadata" poster="<?= $posterSrc ?>" style="width: 100%; height: 100%; object-fit: contain;">
                                    <source src="<?= $videoSrc ?>" type="video/mp4">
                                    <source src="<?= $videoSrc ?>" type="video/quicktime">
                                </video>
                            </div>

                            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 1rem;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="video_id" value="<?= $v['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Video Title</label>
                                        <input type="text" name="title" class="form-control" value="<?= e($v['title']) ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Video File Path</label>
                                        <input type="text" class="form-control" value="<?= e($v['file_path']) ?>" readonly style="opacity: 0.7;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2"><?= e($v['description']) ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Poster Image</label>
                                    <select name="poster_path" class="form-control">
                                        <?php foreach ($photos as $p): 
                                            $pVal = $p['thumb_path'] ?: $p['file_path'];
                                        ?>
                                            <option value="<?= e($pVal) ?>" <?= $v['poster_path'] === $pVal ? 'selected' : '' ?>>
                                                <?= e($p['title']) ?> (<?= basename($p['file_path']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                                    <button type="submit" class="adm-btn adm-btn-primary">
                                        <i class="fas fa-save"></i>
                                        <span>Update Video Details</span>
                                    </button>

                                    <div style="display: flex; gap: 0.5rem;">
                                        <button type="submit" formaction="" name="action" value="reorder" class="adm-btn adm-btn-outline" title="Reorder Video">
                                            <input type="hidden" name="direction" value="up">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                        <button type="submit" formaction="" name="action" value="reorder" class="adm-btn adm-btn-outline" title="Reorder Video">
                                            <input type="hidden" name="direction" value="down">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                        <button type="submit" formaction="" name="action" value="delete" class="adm-btn adm-btn-danger" onclick="return confirm('Delete this video?');">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
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

