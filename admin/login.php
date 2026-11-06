<?php
/**
 * Cafe Coffea – Cups & Moments
 * Secure Admin Login
 */
require_once __DIR__ . '/../config/db.php';

$baseUrl = get_base_url();
$error = '';

// If already logged in, redirect to admin dashboard
if (is_admin_logged_in()) {
    header("Location: " . $baseUrl . "/admin/index.php");
    exit;
}

// Handle login post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';

    if (!verify_csrf_token($csrf)) {
        $error = "Security token expired or invalid. Please try again.";
    } elseif (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $db = get_db();
        $stmt = $db->prepare("SELECT * FROM admins WHERE username = :u OR email = :u LIMIT 1");
        $stmt->execute([':u' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Login successful
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            
            // Regenerate session id for security
            session_regenerate_id(true);

            header("Location: " . $baseUrl . "/admin/index.php");
            exit;
        } else {
            $error = "Invalid username/email or password.";
        }
    }
}

$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Cafe Coffea</title>
    <link rel="icon" type="image/png" href="<?= $baseUrl ?>/assets/images/logo.png">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/admin.css">
    <style>
        .login-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            background: radial-gradient(circle at 50% 50%, rgba(38, 28, 20, 0.7) 0%, rgba(15, 11, 8, 0.98) 100%);
        }
        .login-box {
            width: 100%;
            max-width: 420px;
            background: #1A130E;
            border: 1.5px solid var(--adm-border);
            border-radius: var(--adm-radius);
            padding: 2.5rem 2rem;
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
            text-align: center;
        }
        .login-logo {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-box">
            <img src="<?= $baseUrl ?>/assets/images/logo.png" alt="Cafe Coffea" class="login-logo">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; color: #FFF; margin-bottom: 0.25rem;">Cafe Coffea</h2>
            <p style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--adm-gold); margin-bottom: 1.75rem;">Party Hall Admin Portal</p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" style="text-align: left;">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" style="text-align: left;">
                <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

                <div class="form-group">
                    <label class="form-label" for="username">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Enter admin username" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="adm-btn adm-btn-primary" style="width: 100%; margin-top: 1rem; font-size: 1rem;">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In to Dashboard</span>
                </button>
            </form>

            <div style="margin-top: 2rem; border-top: 1px solid var(--adm-border); padding-top: 1.25rem; font-size: 0.85rem; color: var(--adm-text-muted);">
                <a href="<?= $baseUrl ?>/index.php" style="color: var(--adm-gold); text-decoration: none;">
                    <i class="fas fa-arrow-left"></i> Back to Public Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>

