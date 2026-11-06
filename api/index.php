<?php
/**
 * Cafe Coffea – Vercel Serverless Function Router
 */
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

try {
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
        $candidate = $rootDir . $path;
        if (file_exists($candidate) && !is_dir($candidate) && pathinfo($candidate, PATHINFO_EXTENSION) === 'php') {
            require $candidate;
        } elseif (file_exists($candidate . '.php')) {
            require $candidate . '.php';
        } else {
            require $rootDir . '/index.php';
        }
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo "<h1>PHP Execution Error</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

