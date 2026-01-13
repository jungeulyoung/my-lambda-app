<?php
// Create database/database.sqlite if not exists
$dbFile = 'database/database.sqlite';
if (!file_exists($dbFile)) {
    touch($dbFile);
    echo "Created database.sqlite.\n";
}

// Update .env
$envFile = '.env';
$content = file_get_contents($envFile);

// Change connection
$content = preg_replace('/^DB_CONNECTION=.*$/m', 'DB_CONNECTION=sqlite', $content);

// Comment out other DB params
$params = ['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
foreach ($params as $param) {
    $content = preg_replace('/^(\s*)' . $param . '/m', '$1#' . $param, $content);
}

// Remove # if already commented to avoid double commenting (simplistic check)
// Actually, just ensuring they are commented is enough. If I run this multiple times, it might add multiple #. 
// Let's reset first to be clean or just be careful. 
// A simpler regex: replace start of line `DB_HOST` with `#DB_HOST`.
// If it starts with `#DB_HOST`, leave it.

file_put_contents($envFile, $content);
echo "Updated .env for SQLite.\n";
