<?php
require __DIR__ . '/includes/config.php';

if (!isset($_FILES['pdf'])) {
    http_response_code(400);
    echo "No file uploaded";
    exit;
}

$pdfFile = $_FILES['pdf'];
$start = $_POST['start'] ?? 1;
$end   = $_POST['end'] ?? 0;
$lang  = $_POST['lang'] ?? "en";

// Save uploaded file
$pdfPath = UPLOAD_DIR . uniqid("pdf_") . ".pdf";
move_uploaded_file($pdfFile['tmp_name'], $pdfPath);

// Output file
$mp3Path = OUTPUT_DIR . uniqid("audio_") . ".mp3";

// Run Python converter
$cmd = escapeshellcmd(PYTHON_BIN) . " " . CONVERTER_SCRIPT . " " .
    escapeshellarg($pdfPath) . " " . escapeshellarg($mp3Path) . " " .
    escapeshellarg($start) . " " . escapeshellarg($end) . " " . escapeshellarg($lang);

exec($cmd . " 2>&1", $output, $return);

if ($return !== 0) {
    http_response_code(500);
    echo "Conversion failed: " . implode("\n", $output);
    exit;
}

// Serve MP3 file
header('Content-Type: audio/mpeg');
header('Content-Disposition: attachment; filename="output.mp3"');
readfile($mp3Path);
exit;
