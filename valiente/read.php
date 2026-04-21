<?php
require_once 'auth.php';
require_once 'connection.php';
$query = $connection->query('SELECT * FROM data ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>All Students (Simple)</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="page-flow">
<script src="theme.js"></script>
<script>
  ValienteTheme.boot();
</script>
<div class="toggle" onclick="ValienteTheme.toggle(this)" title="Toggle light / dark mode" role="button" tabindex="0" aria-label="Toggle theme" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();ValienteTheme.toggle(this);}">🌙</div>

<div class="page-shell">
  <header class="site-top">
    <h1>All students</h1>
    <nav class="site-nav" aria-label="Page">
      <a class="btn btn-ghost" href="dashboard.php">Dashboard</a>
      <a class="btn btn-primary" href="readtable.php">View table</a>
      <a class="btn btn-ghost" href="index.php">Add student</a>
      <a class="btn btn-logout" href="logout.php">Logout</a>
    </nav>
  </header>

  <?php if ($query->num_rows > 0): ?>
  <div class="student-cards">
    <?php while ($row = $query->fetch_assoc()): ?>
    <article class="student-card">
      <div><strong>Full name:</strong> <?= htmlspecialchars($row['name']) ?></div>
      <div><strong>Age:</strong> <?= htmlspecialchars((string) $row['age']) ?></div>
      <div><strong>Address:</strong> <?= htmlspecialchars($row['address']) ?></div>
      <div><strong>Course:</strong> <?= htmlspecialchars($row['course']) ?></div>
      <div><strong>School:</strong> <?= htmlspecialchars($row['school']) ?></div>
    </article>
    <?php endwhile; ?>
  </div>
  <?php else: ?>
  <p class="empty-state">No data yet. Add a student or open the table view.</p>
  <?php endif; ?>

  <footer class="footer-nav">
    <a href="dashboard.php">Dashboard</a>
    <span aria-hidden="true">·</span>
    <a href="readtable.php">View table</a>
    <span aria-hidden="true">·</span>
    <a href="index.php">Add student</a>
    <span aria-hidden="true">·</span>
    <a href="logout.php">Logout</a>
  </footer>
</div>

<script>
  ValienteTheme.syncToggle(document.querySelector('.toggle'));
</script>
</body>
</html>
