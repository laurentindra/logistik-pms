<?php

// Prepare SQLite database in /tmp for Vercel serverless environment
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    $srcDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($srcDb)) {
        copy($srcDb, $dbPath);
    } else {
        touch($dbPath);
    }
}

// Ensure storage & bootstrap cache directories exist in /tmp
$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
    '/tmp/bootstrap',
    '/tmp/bootstrap/cache',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Copy pre-compiled bootstrap cache files to /tmp/bootstrap/cache if present
$srcBootstrapCache = __DIR__ . '/../bootstrap/cache';
if (is_dir($srcBootstrapCache)) {
    foreach (scandir($srcBootstrapCache) as $file) {
        if ($file !== '.' && $file !== '..' && str_ends_with($file, '.php')) {
            @copy("$srcBootstrapCache/$file", "/tmp/bootstrap/cache/$file");
        }
    }
}

// Environment variables for Vercel Serverless
putenv('VERCEL=1');
putenv('LOG_CHANNEL=stderr');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . $dbPath);
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_STORAGE=/tmp/storage');
putenv('APP_PACKAGES_CACHE=/tmp/bootstrap/cache/packages.php');
putenv('APP_SERVICES_CACHE=/tmp/bootstrap/cache/services.php');
putenv('APP_CONFIG_CACHE=/tmp/bootstrap/cache/config.php');
putenv('APP_ROUTES_CACHE=/tmp/bootstrap/cache/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/bootstrap/cache/events.php');

$_ENV['VERCEL'] = '1';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $dbPath;
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

$_SERVER['VERCEL'] = '1';
$_SERVER['LOG_CHANNEL'] = 'stderr';
$_SERVER['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_DATABASE'] = $dbPath;
$_SERVER['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_SERVER['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_SERVER['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_SERVER['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
$_SERVER['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

// Forward to Laravel entrypoint
require __DIR__ . '/../public/index.php';
