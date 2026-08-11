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

// Ensure storage directories exist in /tmp
$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Environment variables for Vercel Serverless
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

// Forward to Laravel entrypoint
require __DIR__ . '/../public/index.php';
