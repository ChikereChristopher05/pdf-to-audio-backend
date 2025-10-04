<?php
// Absolute path to your project root
define('APP_ROOT', __DIR__ . '/../');

// Uploads & Outputs
define('UPLOAD_DIR', APP_ROOT . 'uploads/');
define('OUTPUT_DIR', APP_ROOT . 'outputs/');

// Path to Python inside Docker (Linux)
define('PYTHON_BIN', '/usr/bin/python3');

// Path to converter script
define('CONVERTER_SCRIPT', APP_ROOT . 'convert.py');

// --- Optional MySQL logging ---
define('ENABLE_DB', false); // You disabled DB logging, so ignore DB configs below.

define('DB_HOST', 'localhost');
define('DB_NAME', 'pdf_audio');
define('DB_USER', 'root');
define('DB_PASS', '');

// Helper for DB (only if ENABLE_DB = true)
function db() {
    static $pdo = null;
    if (!ENABLE_DB) return null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }
    return $pdo;
}
