<?php
// config.php — database credentials are NOT stored in the repo.
// Copy config.example.php to config.local.php and fill in your values,
// or set the DB_HOST / DB_USER / DB_PASS / DB_NAME environment variables.
$local = __DIR__ . '/config.local.php';
if (file_exists($local)) {
    require $local;
} else {
    define('DB_CREDENTIALS', [
        'host' => getenv('DB_HOST') ?: 'localhost',
        'user' => getenv('DB_USER') ?: 'root',
        'pass' => getenv('DB_PASS') ?: '',
        'db'   => getenv('DB_NAME') ?: 'Dinamize',
    ]);
}
