<?php

/**
 * PHP Built-in Server Router for Flynax
 * Bu dosya Coolify/Nixpacks ortamında .htaccess yerine kullanılır
 */

// Session ve SSL Fix (Coolify/Traefik için)
ini_set('session.save_path', '/tmp');

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Statik dosyalar için (css, js, images, etc.)
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    // MIME types
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
        'pdf'  => 'application/pdf',
        'xml'  => 'application/xml',
        'zip'  => 'application/zip',
        'webp' => 'image/webp',
        'mp4'  => 'video/mp4',
        'webm' => 'video/webm',
    ];

    $ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile(__DIR__ . $uri);
        return true;
    }

    // PHP dosyaları için
    if ($ext === 'php') {
        return false; // PHP'nin işlemesine izin ver
    }

    // Diğer statik dosyalar
    return false;
}

// API endpoint'leri
if (preg_match('#^/api/v[0-9]+/?.*$#', $uri)) {
    $_SERVER['REQUEST_URI'] = $uri;
    require __DIR__ . '/plugins/api/request.php';
    return true;
}

// Sitemap
if ($uri === '/sitemap.xml') {
    require __DIR__ . '/plugins/sitemap/sitemap.php';
    return true;
}

// Admin panel
if (strpos($uri, '/admin') === 0) {
    // Admin dizini için
    $adminPath = __DIR__ . '/admin' . str_replace('/admin', '', $uri);

    if (is_dir($adminPath) && file_exists($adminPath . '/index.php')) {
        require $adminPath . '/index.php';
        return true;
    }

    if (file_exists($adminPath . '.php')) {
        require $adminPath . '.php';
        return true;
    }

    if (file_exists($adminPath)) {
        return false;
    }

    // Admin ana sayfası
    require __DIR__ . '/admin/index.php';
    return true;
}

// AJAX işlemleri
if ($uri === '/request.ajax.php' || strpos($uri, 'request.ajax.php') !== false) {
    require __DIR__ . '/request.ajax.php';
    return true;
}

// Flynax URL rewriting mantığı
// Listing URL'leri: /category/subcategory-l123.html
if (preg_match('#^/([^/]+)(/?.*)?\-l?([0-9]+)\.html$#', $uri, $matches)) {
    $_GET['page'] = $matches[1];
    $_GET['rlVareables'] = trim($matches[2], '/');
    $_GET['listing_id'] = $matches[3];
    require __DIR__ . '/index.php';
    return true;
}

// Paging URL'leri: /page/index2.html
if (preg_match('#^/([^/]+)/?(.*)?/index([0-9]*)\.html$#', $uri, $matches)) {
    $_GET['page'] = $matches[1];
    $_GET['rlVareables'] = $matches[2];
    $_GET['pg'] = $matches[3];
    require __DIR__ . '/index.php';
    return true;
}

// Standard page URL'leri: /page.html
if (preg_match('#^/([^/]+)\.html$#', $uri, $matches)) {
    $_GET['page'] = $matches[1];
    require __DIR__ . '/index.php';
    return true;
}

// Category/subcategory URL'leri: /category/subcategory/
if (preg_match('#^/([^/]+)/(.*)/?$#', $uri, $matches)) {
    $_GET['page'] = $matches[1];
    $_GET['rlVareables'] = rtrim($matches[2], '/');
    require __DIR__ . '/index.php';
    return true;
}

// Trailing slash ile gelen URL'ler
if (preg_match('#^/([^/]+)/$#', $uri, $matches)) {
    $_GET['page'] = $matches[1];
    require __DIR__ . '/index.php';
    return true;
}

// Ana sayfa ve diğer tüm istekler
require __DIR__ . '/index.php';
return true;
