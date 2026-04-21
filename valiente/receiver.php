<?php
require_once 'auth.php';
require_once 'connection.php';
require_once 'form_options.php';

$name = $_POST['fullname'] ?? '';
$age = $_POST['age'] ?? '';
$municipality = trim((string) ($_POST['municipality'] ?? ''));
$barangay = trim((string) ($_POST['barangay'] ?? ''));
$address = valiente_build_address_from_post($barangays_by_municipality, $municipality, $barangay);
$course = $_POST['course'] ?? '';
$school = $_POST['school'] ?? '';

if ($name && $age && $address && $course && $school) {
    $stmt = $connection->prepare('INSERT INTO data (name, age, address, course, school) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sisss', $name, $age, $address, $course, $school);
    $stmt->execute();
    $success = true;
} else {
    $success = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Student added</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="page-centered">
<script src="theme.js"></script>
<script>
  ValienteTheme.boot();
</script>
<div class="toggle" onclick="ValienteTheme.toggle(this)" title="Toggle light / dark mode" role="button" tabindex="0" aria-label="Toggle theme" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();ValienteTheme.toggle(this);}">🌙</div>

<div class="panel">
<?php if ($success): ?>
    <h2>Student added</h2>
    <p><strong>Full name:</strong> <?= htmlspecialchars($name) ?></p>
    <p><strong>Age:</strong> <?= htmlspecialchars((string) $age) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($address) ?></p>
    <p><strong>Course:</strong> <?= htmlspecialchars($course) ?></p>
    <p><strong>School:</strong> <?= htmlspecialchars($school) ?></p>
    <div class="panel-actions">
      <a href="dashboard.php">Dashboard</a>
      ·
      <a href="readtable.php">View table</a>
      ·
      <a href="read.php">Simple list</a>
      ·
      <a href="index.php">Add another</a>
    </div>
<?php else: ?>
    <h2>Missing data</h2>
    <p>Please fill out all fields on the form and try again.</p>
    <div class="panel-actions">
      <a href="index.php">Back to form</a>
    </div>
<?php endif; ?>
</div>

<script>
  ValienteTheme.syncToggle(document.querySelector('.toggle'));
</script>
</body>
</html>
