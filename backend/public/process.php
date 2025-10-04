<?php
ini_set('max_execution_time', 600); // 600 seconds = 5 minutes
require __DIR__ . '/includes/config.php';

function fail($msg) {
    http_response_code(400);
    echo "<!doctype html><meta charset='utf-8'><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'><div class='container py-5'><div class='alert alert-danger'>$msg</div><a class='btn btn-secondary' href='index.php'>&larr; Back</a></div>";
    exit;
}

if (!isset($_FILES['pdf'])) fail('No file uploaded.');

$file = $_FILES['pdf'];
if ($file['error'] !== UPLOAD_ERR_OK) fail('Upload failed. Code: '.$file['error']);

if ($file['size'] > 10 * 1024 * 1024) fail('File too large. Max 10 MB.');
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);
if ($mime !== 'application/pdf') fail('Only PDF files are allowed.');

if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
if (!is_dir(OUTPUT_DIR)) mkdir(OUTPUT_DIR, 0775, true);

// Safe filename
$baseName = pathinfo($file['name'], PATHINFO_FILENAME);
$slug = preg_replace('/[^a-zA-Z0-9-_]+/', '_', $baseName);
$uid = bin2hex(random_bytes(4));
$pdfName = $slug . '_' . $uid . '.pdf';
$mp3Name = $slug . '_' . $uid . '.mp3';

$pdfPath = UPLOAD_DIR . $pdfName;
$mp3Path = OUTPUT_DIR . $mp3Name;

if (!move_uploaded_file($file['tmp_name'], $pdfPath)) fail('Could not move uploaded file.');

$start = isset($_POST['start']) && $_POST['start'] !== '' ? (int)$_POST['start'] : 1;
$end   = isset($_POST['end']) && $_POST['end'] !== '' ? (int)$_POST['end'] : 0;
$lang  = isset($_POST['lang']) ? $_POST['lang'] : 'en';

// Build Python command (no "rate" anymore!)
$cmd = escapeshellcmd(PYTHON_BIN) . ' ' .
       escapeshellarg(CONVERTER_SCRIPT) . ' ' .
       escapeshellarg($pdfPath) . ' ' .
       escapeshellarg($mp3Path) . ' ' .
       escapeshellarg($start) . ' ' .
       escapeshellarg($end) . ' ' .
       escapeshellarg($lang) . ' 2>&1';

// Run Python and capture output
$descriptorspec = [
    1 => ["pipe", "w"], // stdout
    2 => ["pipe", "w"], // stderr
];

$process = proc_open($cmd, $descriptorspec, $pipes);
$output = '';

if (is_resource($process)) {
    $output .= stream_get_contents($pipes[1]); // stdout
    fclose($pipes[1]);

    $output .= stream_get_contents($pipes[2]); // stderr
    fclose($pipes[2]);

    proc_close($process);
}

$ok = is_file($mp3Path) && filesize($mp3Path) > 0;

// (Optional) Database logging
if (ENABLE_DB && $ok) {
    try {
        $pdo = db();
        if ($pdo) {
            $stmt = $pdo->prepare("INSERT INTO conversions (ip, pdf_name, mp3_name, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$_SERVER['REMOTE_ADDR'] ?? null, $pdfName, $mp3Name]);
        }
    } catch (Throwable $e) {
        // logging optional
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Conversion Result</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PDF to Audio Converter</a>
        </div>
    </nav>
  </div>
  
<div class="container py-5">
  <?php if ($ok): ?>
    <div class="card p-4">
      <h2 class="h4 mb-3">Conversion successful</h2>
      <p class="text-muted mb-3">Your file has been converted. You can play it below or download it.</p>

      <audio controls style="width:100%;">
        <source src="<?= 'outputs/' . htmlspecialchars($mp3Name, ENT_QUOTES) ?>" type="audio/mpeg">
        Your browser does not support the audio element.
      </audio>

      <div class="mt-3 d-flex gap-2">
        <a class="btn btn-primary" href="<?= 'outputs/' . htmlspecialchars($mp3Name, ENT_QUOTES) ?>" download>Download MP3</a>
        <a class="btn btn-outline-secondary" href="index.php">Convert another PDF</a>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger">
      <h4 class="alert-heading">Conversion failed</h4>
      <p>We couldn’t generate the MP3. Details:</p>
      <pre class="small bg-light p-3 border rounded"><?= htmlspecialchars($output ?? 'No output', ENT_QUOTES) ?></pre>
      <a class="btn btn-secondary mt-3" href="index.php">&larr; Try again</a>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
