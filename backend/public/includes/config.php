<?php
define('APP_ROOT', dirname(__DIR__) . '/');
define('UPLOAD_DIR', APP_ROOT . 'uploads/');
define('OUTPUT_DIR', APP_ROOT . 'outputs/');

// Python path on Render
define('PYTHON_BIN', '/usr/bin/python3');

// Path to Python converter script
define('CONVERTER_SCRIPT', APP_ROOT . 'convert.py');

// Ensure directories exist
if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
if (!file_exists(OUTPUT_DIR)) mkdir(OUTPUT_DIR, 0777, true);
