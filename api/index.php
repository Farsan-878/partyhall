<?php
/**
 * Cafe Coffea – Vercel Serverless Function Router
 */
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($requestUri, PHP_URL_PATH);
$path = rtrim($path, '/');
if ($path === '') {
    $path = '/';
}

$rootDir = dirname(__DIR__);

if ($path === '/' || $path === '/index.php') {
    require $rootDir . '/index.php';
} elseif ($path === '/gallery' || $path === '/gallery.php') {
    require $rootDir . '/gallery.php';
} elseif ($path === '/instructions' || $path === '/instructions.php') {
    require $rootDir . '/instructions.php';
} elseif (preg_match('#^/admin(/(.*))?$#', $path, $matches)) {
    $sub = $matches[2] ?? '';
    if ($sub === '' || $sub === 'index.php') {
        require $rootDir . '/admin/index.php';
    } elseif (file_exists($rootDir . '/admin/' . $sub)) {
        require $rootDir . '/admin/' . $sub;
    } elseif (file_exists($rootDir . '/admin/' . $sub . '.php')) {
        require $rootDir . '/admin/' . $sub . '.php';
    } else {
        http_response_code(404);
        echo "404 Not Found";
    }
} else {
    // Check if directly a root php file exists
    $candidate = $rootDir . $path;
    if (file_exists($candidate) && !is_dir($candidate) && pathinfo($candidate, PATHINFO_EXTENSION) === 'php') {
        require $candidate;
    } elseif (file_exists($candidate . '.php')) {
        require $candidate . '.php';
    } else {
        // Fallback to homepage
        require $rootDir . '/index.php';
    }
}
