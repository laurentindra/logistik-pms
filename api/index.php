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
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// Override environment variables for Vercel Serverless
putenv('DB_CONNECTION=sqlite');
putenv('DB_DATABASE=' . $dbPath);
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $dbPath;
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// Forward to Laravel entrypoint
require __DIR__ . '/../public/index.php';
