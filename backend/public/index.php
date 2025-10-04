<?php require __DIR__ . '/includes/config.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>PDF to Audio Converter</title>
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <h1>PDF to Audio Converter</h1>
  <form id="uploadForm" action="process.php" method="post" enctype="multipart/form-data">
    <input type="file" name="pdf" accept="application/pdf" required><br><br>
    <label>Start page: <input type="number" name="start" value="1"></label><br><br>
    <label>End page: <input type="number" name="end"></label><br><br>
    <label>Language:
      <select name="lang">
        <option value="en">English</option>
        <option value="fr">French</option>
        <option value="es">Spanish</option>
      </select>
    </label><br><br>
    <button type="submit">Convert to MP3</button>
  </form>
</body>
</html>
