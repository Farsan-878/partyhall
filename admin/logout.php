<?php
/**
 * Cafe Coffea – Admin Logout
 */
require_once __DIR__ . '/../config/db.php';

$baseUrl = get_base_url();

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header("Location: " . $baseUrl . "/admin/login.php");
exit;

