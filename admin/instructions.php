<?php
/**
 * Cafe Coffea – Admin Instructions & Policy Management
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
    } elseif ($action === 'add') {
        $category = trim($_POST['category'] ?? 'rules');
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if (empty($description)) {
            $error = "Instruction description cannot be empty.";
        } else {
            $maxRule = (int)$db->query("SELECT MAX(rule_number) FROM instructions WHERE category = " . $db->quote($category))->fetchColumn() + 1;
            $maxOrder = (int)$db->query("SELECT MAX(display_order) FROM instructions")->fetchColumn() + 1;

            $stmt = $db->prepare("INSERT INTO instructions (category, rule_number, title, description, display_order, is_active) VALUES (:c, :r, :t, :d, :o, 1)");
            $stmt->execute([
                ':c' => $category,
                ':r' => $maxRule,
                ':t' => $title,
                ':d' => $description,
                ':o' => $maxOrder
            ]);
            $message = "New policy instruction added successfully.";
        }
    } elseif ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $ruleNumber = (int)($_POST['rule_number'] ?? 1);

        $stmt = $db->prepare("UPDATE instructions SET title = :t, description = :d, rule_number = :rn WHERE id = :id");
        $stmt->execute([':t' => $title, ':d' => $description, ':rn' => $ruleNumber, ':id' => $id]);
        $message = "Instruction updated successfully.";
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare("DELETE FROM instructions WHERE id = :id")->execute([':id' => $id]);
        $message = "Instruction removed.";
    }
}

// Fetch instructions grouped by category
$categories = [
    'rules' => ['name' => 'Party Hall Rules', 'icon' => 'fa-clock'],
    'decoration' => ['name' => 'Decoration Policy', 'icon' => 'fa-paint-brush'],
    'cake' => ['name' => 'Cake Policy', 'icon' => 'fa-birthday-cake'],
    'general' => ['name' => 'General / Cancellation Terms', 'icon' => 'fa-file-contract']
];

$allInstructions = $db->query("SELECT * FROM instructions ORDER BY category ASC, display_order ASC, rule_number ASC")->fetchAll();
$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructions & Policies | Cafe Coffea Admin</title>
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
                    <h1 class="page-title">Instructions & Policies Management</h1>
                    <p class="page-subtitle">Add, edit, and update Party Hall Rules, Decoration Policies, and Cake Policies.</p>
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

            <!-- Add New Instruction Form -->
            <div class="card">
                <div class="card-title">
                    <span><i class="fas fa-plus-circle text-gold"></i> Add New Policy Rule / Instruction</span>
                </div>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="category">Category</label>
                            <select id="category" name="category" class="form-control">
                                <option value="rules">Party Hall Rules</option>
                                <option value="decoration">Decoration Policy</option>
                                <option value="cake">Cake Policy</option>
                                <option value="general">General / Cancellation Policy</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="title">Rule Heading / Title</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="e.g. Wallpaper Protection or Advance Notice">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Rule Content / Policy Detail</label>
                        <textarea id="description" name="description" class="form-control" rows="2" placeholder="State the rule clearly for guests..." required></textarea>
                    </div>

                    <button type="submit" class="adm-btn adm-btn-primary">
                        <i class="fas fa-plus"></i>
                        <span>Add Instruction</span>
                    </button>
                </form>
            </div>

            <!-- Existing Instructions Grouped by Category -->
            <?php foreach ($categories as $catKey => $catMeta): 
                $items = array_filter($allInstructions, fn($i) => $i['category'] === $catKey);
            ?>
            <div class="card">
                <div class="card-title">
                    <span><i class="fas <?= $catMeta['icon'] ?> text-gold"></i> <?= e($catMeta['name']) ?> (<?= count($items) ?> Rules)</span>
                </div>

                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                        <div style="background: rgba(10, 7, 5, 0.5); border: 1px solid var(--adm-border); border-radius: 8px; padding: 1.25rem;">
                            <form method="POST" action="" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                                <div class="form-row">
                                    <div class="form-group" style="max-width: 120px;">
                                        <label class="form-label">Rule #</label>
                                        <input type="number" name="rule_number" class="form-control" value="<?= e($item['rule_number']) ?>" min="1">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Title</label>
                                        <input type="text" name="title" class="form-control" value="<?= e($item['title']) ?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="2" required><?= e($item['description']) ?></textarea>
                                </div>

                                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                                    <button type="submit" class="adm-btn adm-btn-primary adm-btn-sm">
                                        <i class="fas fa-save"></i>
                                        <span>Save Rule Changes</span>
                                    </button>

                                    <button type="submit" formaction="" name="action" value="delete" class="adm-btn adm-btn-danger adm-btn-sm" onclick="return confirm('Delete this instruction?');">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: var(--adm-text-muted); font-size: 0.9rem;">No rules added in this category yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>

