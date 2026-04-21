<?php
require_once 'auth.php';
require_once 'connection.php';

// --- Stats ---
$total_students  = $connection->query('SELECT COUNT(*) AS c FROM data')->fetch_assoc()['c'] ?? 0;
$total_users     = $connection->query('SELECT COUNT(*) AS c FROM users')->fetch_assoc()['c'] ?? 0;
$latest_students = $connection->query('SELECT name, course, school FROM data ORDER BY id DESC LIMIT 5');

// Students per school
$by_school = $connection->query(
    'SELECT school, COUNT(*) AS c FROM data GROUP BY school ORDER BY c DESC LIMIT 6'
);
$school_rows = [];
while ($r = $by_school->fetch_assoc()) $school_rows[] = $r;

// Students per course
$by_course = $connection->query(
    'SELECT course, COUNT(*) AS c FROM data GROUP BY course ORDER BY c DESC LIMIT 5'
);
$course_rows = [];
while ($r = $by_course->fetch_assoc()) $course_rows[] = $r;

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<title>Dashboard — Valiente</title>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --gold: #e8b923;
  --gold-dim: rgba(232,185,35,.18);
  --gold-soft: rgba(232,185,35,.08);
  --bg: #080b10;
  --surface: rgba(16,21,30,.85);
  --surface2: rgba(22,28,40,.9);
  --border: rgba(255,204,0,.13);
  --border2: rgba(255,255,255,.06);
  --text: #dde2ec;
  --muted: #6b7585;
  --red: #e05c5c;
  --green: #3ecf8e;
  --radius: 16px;
  --font-display: 'Syne', sans-serif;
  --font-body: 'DM Sans', sans-serif;
}

body {
  font-family: var(--font-body);
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  overflow-x: hidden;
}

/* animated mesh bg */
body::before {
  content: '';
  position: fixed;
  inset: 0;
  background:
    radial-gradient(ellipse 70% 55% at 8% 12%, rgba(232,185,35,.09) 0%, transparent 55%),
    radial-gradient(ellipse 55% 45% at 92% 80%, rgba(59,130,246,.07) 0%, transparent 50%),
    radial-gradient(ellipse 40% 35% at 50% 50%, rgba(139,92,246,.04) 0%, transparent 60%);
  pointer-events: none;
  z-index: 0;
}

/* ── Layout ── */
.shell {
  position: relative;
  z-index: 1;
  display: grid;
  grid-template-columns: 240px 1fr;
  min-height: 100vh;
}

/* ── Sidebar ── */
.sidebar {
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex;
  flex-direction: column;
  padding: 2rem 1.25rem;
  gap: 0.35rem;
  backdrop-filter: blur(20px);
  position: sticky;
  top: 0;
  height: 100vh;
  overflow-y: auto;
}

.sidebar-logo {
  font-family: var(--font-display);
  font-weight: 800;
  font-size: 1.2rem;
  letter-spacing: .06em;
  color: var(--gold);
  text-transform: uppercase;
  padding: 0 0.5rem 1.75rem;
  border-bottom: 1px solid var(--border2);
  margin-bottom: 1rem;
}

.sidebar-logo span {
  display: block;
  font-size: .65rem;
  font-weight: 400;
  letter-spacing: .1em;
  color: var(--muted);
  margin-top: .15rem;
  font-family: var(--font-body);
}

.nav-label {
  font-size: .6rem;
  letter-spacing: .14em;
  text-transform: uppercase;
  color: var(--muted);
  padding: 0 0.5rem;
  margin: 1rem 0 .4rem;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: .7rem;
  padding: .65rem .85rem;
  border-radius: 10px;
  font-size: .875rem;
  font-weight: 500;
  color: var(--muted);
  text-decoration: none;
  transition: all .18s ease;
  letter-spacing: .01em;
}

.nav-link:hover {
  color: var(--text);
  background: rgba(255,255,255,.05);
}

.nav-link.active {
  color: var(--gold);
  background: var(--gold-dim);
  font-weight: 600;
}

.nav-link .icon {
  width: 18px;
  height: 18px;
  opacity: .75;
  flex-shrink: 0;
}

.nav-link.active .icon { opacity: 1; }

.sidebar-footer {
  margin-top: auto;
  padding-top: 1.25rem;
  border-top: 1px solid var(--border2);
}

.user-chip {
  display: flex;
  align-items: center;
  gap: .75rem;
  padding: .65rem .85rem;
  border-radius: 10px;
  background: var(--gold-soft);
  margin-bottom: .6rem;
}

.user-avatar {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: linear-gradient(135deg, #f0d060, var(--gold));
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-display);
  font-weight: 800;
  font-size: .85rem;
  color: #0c0f14;
  flex-shrink: 0;
}

.user-info { overflow: hidden; }

.user-name {
  font-size: .8rem;
  font-weight: 600;
  color: var(--text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  font-size: .65rem;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .08em;
}

.btn-logout-side {
  display: flex;
  align-items: center;
  gap: .6rem;
  width: 100%;
  padding: .6rem .85rem;
  border-radius: 10px;
  background: none;
  border: 1px solid rgba(224,92,92,.3);
  color: #f5a5a5;
  font-size: .8rem;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  transition: background .15s ease;
  font-family: var(--font-body);
}

.btn-logout-side:hover { background: rgba(224,92,92,.1); }

/* ── Main ── */
.main {
  display: flex;
  flex-direction: column;
  padding: 2.25rem 2rem;
  gap: 1.75rem;
  overflow-y: auto;
}

/* Page header */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
}

.page-title {
  font-family: var(--font-display);
  font-size: 1.75rem;
  font-weight: 800;
  color: var(--text);
  letter-spacing: -.01em;
  line-height: 1.1;
}

.page-title span {
  color: var(--gold);
}

.page-sub {
  font-size: .8rem;
  color: var(--muted);
  margin-top: .3rem;
}

.header-actions {
  display: flex;
  gap: .6rem;
  align-items: center;
}

.btn-add {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  padding: .65rem 1.15rem;
  border-radius: 10px;
  background: linear-gradient(135deg, #f0d060, var(--gold));
  color: #0c0f14;
  font-weight: 700;
  font-size: .875rem;
  text-decoration: none;
  box-shadow: 0 4px 18px rgba(232,185,35,.3);
  transition: transform .15s ease, box-shadow .15s ease;
  font-family: var(--font-body);
}

.btn-add:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 24px rgba(232,185,35,.4);
}

/* ── Stat cards ── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
}

.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.4rem 1.5rem;
  backdrop-filter: blur(12px);
  position: relative;
  overflow: hidden;
  animation: fadeUp .4s ease both;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, var(--gold), transparent);
}

.stat-label {
  font-size: .68rem;
  text-transform: uppercase;
  letter-spacing: .1em;
  color: var(--muted);
  margin-bottom: .6rem;
}

.stat-value {
  font-family: var(--font-display);
  font-size: 2.4rem;
  font-weight: 800;
  color: var(--gold);
  line-height: 1;
}

.stat-desc {
  font-size: .75rem;
  color: var(--muted);
  margin-top: .4rem;
}

.stat-icon {
  position: absolute;
  right: 1.25rem;
  top: 1.25rem;
  font-size: 1.5rem;
  opacity: .18;
}

/* ── Section titles ── */
.section-title {
  font-family: var(--font-display);
  font-size: .75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .12em;
  color: var(--muted);
  margin-bottom: 1rem;
}

/* ── Two-col grid ── */
.two-col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}

/* ── Card shell ── */
.card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 1.5rem;
  backdrop-filter: blur(12px);
  animation: fadeUp .45s ease both;
}

/* ── Bar chart (CSS only) ── */
.bar-list {
  display: flex;
  flex-direction: column;
  gap: .9rem;
}

.bar-row { display: flex; flex-direction: column; gap: .3rem; }

.bar-meta {
  display: flex;
  justify-content: space-between;
  font-size: .78rem;
}

.bar-name {
  color: var(--text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 75%;
}

.bar-count { color: var(--gold); font-weight: 600; }

.bar-track {
  height: 6px;
  background: rgba(255,255,255,.06);
  border-radius: 99px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  border-radius: 99px;
  background: linear-gradient(90deg, #f0d060, var(--gold));
  transition: width .6s cubic-bezier(.4,0,.2,1);
}

/* ── Recent students table ── */
.recent-table {
  width: 100%;
  border-collapse: collapse;
  font-size: .85rem;
}

.recent-table th {
  text-align: left;
  font-size: .62rem;
  text-transform: uppercase;
  letter-spacing: .1em;
  color: var(--muted);
  padding: 0 .75rem .75rem;
  border-bottom: 1px solid var(--border2);
}

.recent-table td {
  padding: .75rem;
  border-bottom: 1px solid var(--border2);
  color: var(--text);
  vertical-align: middle;
}

.recent-table tbody tr:last-child td { border-bottom: none; }

.recent-table tbody tr {
  transition: background .15s ease;
}

.recent-table tbody tr:hover {
  background: rgba(255,255,255,.03);
}

.pill {
  display: inline-block;
  padding: .2rem .65rem;
  border-radius: 99px;
  font-size: .7rem;
  font-weight: 600;
  background: var(--gold-dim);
  color: var(--gold);
  white-space: nowrap;
}

.empty-note {
  color: var(--muted);
  font-size: .85rem;
  padding: 1.5rem 0;
  text-align: center;
}

/* ── Quick actions ── */
.action-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: .85rem;
}

.action-btn {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: .5rem;
  padding: 1.2rem 1.25rem;
  border-radius: 12px;
  border: 1px solid var(--border2);
  background: var(--surface2);
  text-decoration: none;
  color: var(--text);
  font-size: .8rem;
  font-weight: 500;
  transition: border-color .18s ease, background .18s ease, transform .18s ease;
}

.action-btn:hover {
  border-color: var(--gold);
  background: var(--gold-soft);
  transform: translateY(-2px);
}

.action-btn .a-icon { font-size: 1.35rem; }
.action-btn .a-label { font-size: .75rem; font-weight: 600; color: var(--text); }
.action-btn .a-sub { font-size: .65rem; color: var(--muted); }

/* ── Animations ── */
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}
@keyframes fadeIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}
@keyframes countUp {
  from { opacity: 0; transform: scale(.7); }
  to   { opacity: 1; transform: scale(1); }
}
@keyframes barGrow {
  from { width: 0 !important; }
}
@keyframes shimmer {
  0%   { box-shadow: 0 0 0 0 rgba(232,185,35,0); }
  50%  { box-shadow: 0 0 18px 4px rgba(232,185,35,.18); }
  100% { box-shadow: 0 0 0 0 rgba(232,185,35,0); }
}
@keyframes sidebarSlide {
  from { opacity: 0; transform: translateX(-18px); }
  to   { opacity: 1; transform: translateX(0); }
}

/* Page entrance */
.sidebar { animation: sidebarSlide .45s cubic-bezier(.22,1,.36,1) both; }
.main    { animation: fadeIn .4s ease .1s both; }

/* Stat cards stagger */
.stat-card {
  animation: fadeUp .5s cubic-bezier(.22,1,.36,1) both;
  opacity: 0;
}
.stat-card:nth-child(1) { animation-delay: .12s; }
.stat-card:nth-child(2) { animation-delay: .2s; }
.stat-card:nth-child(3) { animation-delay: .28s; }
.stat-card:nth-child(4) { animation-delay: .36s; }

/* Shimmer pulse on stat-value after load */
.stat-card:nth-child(1) .stat-value { animation: countUp .45s cubic-bezier(.22,1,.36,1) .18s both; }
.stat-card:nth-child(2) .stat-value { animation: countUp .45s cubic-bezier(.22,1,.36,1) .26s both; }
.stat-card:nth-child(3) .stat-value { animation: countUp .45s cubic-bezier(.22,1,.36,1) .34s both; }
.stat-card:nth-child(4) .stat-value { animation: countUp .45s cubic-bezier(.22,1,.36,1) .42s both; }

/* Shimmer on active stat card hover */
.stat-card:hover { animation: shimmer .6s ease; }

/* Cards row stagger */
.two-col > .card:nth-child(1) { animation: fadeUp .5s cubic-bezier(.22,1,.36,1) .3s both; opacity: 0; }
.two-col > .card:nth-child(2) { animation: fadeUp .5s cubic-bezier(.22,1,.36,1) .38s both; opacity: 0; }

/* Recent students card */
.main > .card { animation: fadeUp .5s cubic-bezier(.22,1,.36,1) .45s both; opacity: 0; }

/* Quick actions */
.main > div:last-child { animation: fadeUp .5s cubic-bezier(.22,1,.36,1) .52s both; opacity: 0; }

/* Bar fill animation */
.bar-fill {
  animation: barGrow .8s cubic-bezier(.22,1,.36,1) .5s both;
}

/* Action buttons pop in */
.action-btn {
  animation: fadeUp .45s cubic-bezier(.22,1,.36,1) both;
  opacity: 0;
}
.action-btn:nth-child(1) { animation-delay: .55s; }
.action-btn:nth-child(2) { animation-delay: .62s; }
.action-btn:nth-child(3) { animation-delay: .69s; }
.action-btn:nth-child(4) { animation-delay: .76s; }

/* Nav links slide in */
.nav-link {
  animation: fadeUp .35s cubic-bezier(.22,1,.36,1) both;
  opacity: 0;
}
.nav-link:nth-child(2) { animation-delay: .08s; }
.nav-link:nth-child(3) { animation-delay: .13s; }
.nav-link:nth-child(4) { animation-delay: .18s; }
.nav-link:nth-child(5) { animation-delay: .23s; }

/* Page title */
.page-header { animation: fadeUp .5s cubic-bezier(.22,1,.36,1) .08s both; opacity: 0; }

/* Recent table rows */
.recent-table tbody tr {
  animation: fadeUp .35s cubic-bezier(.22,1,.36,1) both;
  opacity: 0;
}
.recent-table tbody tr:nth-child(1) { animation-delay: .5s; }
.recent-table tbody tr:nth-child(2) { animation-delay: .56s; }
.recent-table tbody tr:nth-child(3) { animation-delay: .62s; }
.recent-table tbody tr:nth-child(4) { animation-delay: .68s; }
.recent-table tbody tr:nth-child(5) { animation-delay: .74s; }

/* ── Responsive ── */
@media (max-width: 768px) {
  .shell { grid-template-columns: 1fr; }
  .sidebar {
    position: static;
    height: auto;
    flex-direction: row;
    flex-wrap: wrap;
    padding: 1rem;
    gap: .5rem;
  }
  .sidebar-logo { padding-bottom: 0; border-bottom: none; margin-bottom: 0; }
  .nav-label { display: none; }
  .sidebar-footer { margin-top: 0; padding-top: 0; border-top: none; }
  .two-col { grid-template-columns: 1fr; }
  .main { padding: 1.25rem 1rem; }
}
</style>
</head>
<body>

<div class="shell">
  <!-- ── Sidebar ── -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      Valiente
      <span>Negros Oriental · Region VII</span>
    </div>

    <span class="nav-label">Main</span>
    <a href="dashboard.php" class="nav-link active">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
    <a href="index.php" class="nav-link">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
      Add Student
    </a>
    <a href="readtable.php" class="nav-link">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 10h18M10 10v9"/></svg>
      View Table
    </a>
    <a href="read.php" class="nav-link">
      <svg class="icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 10h16M4 14h10"/></svg>
      Simple List
    </a>

    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="user-avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?></div>
        <div class="user-info">
          <div class="user-name"><?= $username ?></div>
          <div class="user-role">Administrator</div>
        </div>
      </div>
      <a href="logout.php" class="btn-logout-side">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Log out
      </a>
    </div>
  </aside>

  <!-- ── Main ── -->
  <main class="main">

    <!-- Header -->
    <div class="page-header">
      <div>
        <div class="page-title">Good day, <span><?= $username ?></span> 👋</div>
        <div class="page-sub">Here's what's happening in your student registry.</div>
      </div>
      <div class="header-actions">
        <a href="index.php" class="btn-add">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
          Add student
        </a>
      </div>
    </div>

    <!-- Stat cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">🎓</div>
        <div class="stat-label">Total students</div>
        <div class="stat-value"><?= number_format($total_students) ?></div>
        <div class="stat-desc">Enrolled in registry</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🏫</div>
        <div class="stat-label">Schools</div>
        <div class="stat-value"><?= count($school_rows) ?></div>
        <div class="stat-desc">Represented institutions</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">📚</div>
        <div class="stat-label">Courses</div>
        <div class="stat-value"><?= count($course_rows) ?></div>
        <div class="stat-desc">Active programs</div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">👤</div>
        <div class="stat-label">Admin accounts</div>
        <div class="stat-value"><?= number_format($total_users) ?></div>
        <div class="stat-desc">Registered users</div>
      </div>
    </div>

    <!-- Charts row -->
    <div class="two-col">
      <!-- By school -->
      <div class="card">
        <div class="section-title">Students by school</div>
        <?php if (empty($school_rows)): ?>
          <div class="empty-note">No data yet.</div>
        <?php else:
          $max = max(array_column($school_rows, 'c'));
          ?>
          <div class="bar-list">
            <?php foreach ($school_rows as $r): ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-name"><?= htmlspecialchars($r['school']) ?></span>
                <span class="bar-count"><?= $r['c'] ?></span>
              </div>
              <div class="bar-track">
                <div class="bar-fill" style="width:<?= round(($r['c'] / $max) * 100) ?>%"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <!-- By course -->
      <div class="card">
        <div class="section-title">Top courses</div>
        <?php if (empty($course_rows)): ?>
          <div class="empty-note">No data yet.</div>
        <?php else:
          $max2 = max(array_column($course_rows, 'c'));
          ?>
          <div class="bar-list">
            <?php foreach ($course_rows as $r): ?>
            <div class="bar-row">
              <div class="bar-meta">
                <span class="bar-name"><?= htmlspecialchars($r['course']) ?></span>
                <span class="bar-count"><?= $r['c'] ?></span>
              </div>
              <div class="bar-track">
                <div class="bar-fill" style="width:<?= round(($r['c'] / $max2) * 100) ?>%"></div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Recent students -->
    <div class="card">
      <div class="section-title">Recently added students</div>
      <?php if ($latest_students->num_rows === 0): ?>
        <div class="empty-note">No students yet. <a href="index.php" style="color:var(--gold);">Add one →</a></div>
      <?php else: ?>
      <table class="recent-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Course</th>
            <th>School</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($r = $latest_students->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['course']) ?></td>
            <td><span class="pill"><?= htmlspecialchars($r['school']) ?></span></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

    <!-- Quick actions -->
    <div>
      <div class="section-title">Quick actions</div>
      <div class="action-grid">
        <a href="index.php" class="action-btn">
          <span class="a-icon">➕</span>
          <span class="a-label">Add Student</span>
          <span class="a-sub">Enroll new record</span>
        </a>
        <a href="readtable.php" class="action-btn">
          <span class="a-icon">📋</span>
          <span class="a-label">View Table</span>
          <span class="a-sub">Edit / delete records</span>
        </a>
        <a href="read.php" class="action-btn">
          <span class="a-icon">📄</span>
          <span class="a-label">Simple List</span>
          <span class="a-sub">Card view of students</span>
        </a>
        <a href="logout.php" class="action-btn" style="border-color:rgba(224,92,92,.25);">
          <span class="a-icon">🚪</span>
          <span class="a-label" style="color:#f5a5a5;">Log out</span>
          <span class="a-sub">End session</span>
        </a>
      </div>
    </div>

  </main>
</div>

</body>
</html>
