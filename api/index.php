<?php

// Ensure all temporary storage directories exist with full 0777 permissions
$tmpDirs = [
    '/tmp',
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

foreach ($tmpDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// Copy pre-seeded SQLite database to /tmp/database.sqlite if missing
$dbPath = '/tmp/database.sqlite';
if (!file_exists($dbPath)) {
    $srcDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($srcDb)) {
        @copy($srcDb, $dbPath);
    } else {
        @touch($dbPath);
    }
}

// Set environment variables for Vercel Serverless environment
putenv('VERCEL=1');
putenv('LOG_CHANNEL=stderr');
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . $dbPath);
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
putenv('APP_STORAGE=/tmp/storage');

$_ENV['VERCEL'] = '1';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $dbPath;
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['APP_STORAGE'] = '/tmp/storage';

$_SERVER['VERCEL'] = '1';
$_SERVER['LOG_CHANNEL'] = 'stderr';
$_SERVER['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_DATABASE'] = $dbPath;
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Forward to Laravel entrypoint
require __DIR__ . '/../public/index.php';
