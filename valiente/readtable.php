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
<title>Students table</title>
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
    <h1>Students table</h1>
    <nav class="site-nav" aria-label="Page">
      <a class="btn btn-ghost" href="dashboard.php">Dashboard</a>
      <a class="btn btn-primary" href="read.php">Simple list</a>
      <a class="btn btn-ghost" href="index.php">Add student</a>
      <a class="btn btn-logout" href="logout.php">Logout</a>
    </nav>
  </header>

  <?php if ($query->num_rows > 0): ?>
  <div class="data-table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>Full name</th>
          <th>Age</th>
          <th>Address</th>
          <th>Course</th>
          <th>School</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $query->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars((string) $row['age']) ?></td>
          <td><?= htmlspecialchars($row['address']) ?></td>
          <td><?= htmlspecialchars($row['course']) ?></td>
          <td><?= htmlspecialchars($row['school']) ?></td>
          <td class="cell-actions">
            <a href="edit.php?id=<?= (int) $row['id'] ?>"><button type="button">Edit</button></a>
            <a class="btn-del" href="delete.php?id=<?= (int) $row['id'] ?>" onclick="return confirm('Delete this record?');"><button type="button">Delete</button></a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
  <p class="empty-state">No data in the table yet.</p>
  <?php endif; ?>

  <footer class="footer-nav">
    <a href="dashboard.php">Dashboard</a>
    <span aria-hidden="true">·</span>
    <a href="read.php">Simple list</a>
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
