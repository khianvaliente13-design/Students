<?php
require_once 'auth.php';
require_once 'connection.php';
require_once 'form_options.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Student</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>
<script src="theme.js"></script>
<script>
  ValienteTheme.boot();
</script>
<div class="toggle" onclick="ValienteTheme.toggle(this)" title="Toggle light / dark mode" role="button" tabindex="0" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();ValienteTheme.toggle(this);}">🌙</div>

<div class="container">
  <div class="brand">
    <h1>Add Student</h1>
    <p>Negros Oriental · Region VII — quick enrollment-style capture</p>
  </div>

  <form class="form-stack" method="post" action="receiver.php" autocomplete="off">
    <div class="form-field">
      <label for="fullname">Full name</label>
      <input id="fullname" type="text" name="fullname" placeholder="Juan Gabriel Dela Cruz" required autocomplete="name" />
    </div>

    <div class="form-field">
      <label for="age">Age</label>
      <select id="age" name="age" required>
        <option value="" disabled selected>Choose your age</option>
        <?php for ($a = 16; $a <= 45; $a++): ?>
        <option value="<?= $a ?>"><?= $a ?> years old</option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="municipality">City / municipality</label>
      <select id="municipality" name="municipality" required>
        <option value="" disabled selected>Select city or municipality</option>
        <?php foreach (array_keys($barangays_by_municipality) as $mun): ?>
        <option value="<?= htmlspecialchars($mun) ?>"><?= htmlspecialchars($mun) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="barangay">Barangay</label>
      <select id="barangay" name="barangay" required>
        <option value="" disabled selected>Choose municipality first, then barangay</option>
      </select>
      <p class="field-hint" id="address-preview" hidden></p>
    </div>

    <div class="form-field">
      <label for="course">Course</label>
      <select id="course" name="course" required>
        <option value="" disabled selected>Select your program</option>
        <?php foreach ($courses as $c): ?>
        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-field">
      <label for="school">School in Dumaguete &amp; Negros Oriental</label>
      <select id="school" name="school" required>
        <option value="" disabled selected>Where are you enrolled?</option>
        <?php foreach ($schools as $s): ?>
        <option value="<?= htmlspecialchars($s['value']) ?>"><?= htmlspecialchars($s['label']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit">Add student</button>
  </form>

  <div class="links">
    <a href="dashboard.php">Dashboard</a>
    <span aria-hidden="true">·</span>
    <a href="read.php">View all (simple)</a>
    <span aria-hidden="true">·</span>
    <a href="readtable.php">View table</a>
    <span aria-hidden="true">·</span>
    <a href="logout.php">Logout</a>
  </div>
</div>

<script>
  ValienteTheme.syncToggle(document.querySelector('.toggle'));
</script>
<script src="address_cascade.js"></script>
<script>
ValienteAddressInit(<?= json_encode([
    'municipalityId' => 'municipality',
    'barangayId' => 'barangay',
    'data' => $barangays_by_municipality,
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);

(function () {
  var ADDRESS_SUFFIX = <?= json_encode(VALIENTE_ADDRESS_SUFFIX, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var mun = document.getElementById('municipality');
  var bar = document.getElementById('barangay');
  var prev = document.getElementById('address-preview');
  function refreshPreview() {
    var m = mun && mun.value;
    var b = bar && bar.value;
    if (!m || !b) {
      prev.hidden = true;
      prev.textContent = '';
      return;
    }
    prev.hidden = false;
    prev.textContent = 'Complete address: Barangay ' + b + ', ' + m + ADDRESS_SUFFIX;
  }
  if (mun) mun.addEventListener('change', refreshPreview);
  if (bar) bar.addEventListener('change', refreshPreview);
})();
</script>
</body>
</html>
