<?php
require_once 'auth.php';
require_once 'connection.php';
require_once 'form_options.php';

$id = $_GET['id'] ?? '';
if (!$id) {
    header('Location: readtable.php');
    exit;
}

$stmt = $connection->prepare('SELECT * FROM data WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    header('Location: readtable.php');
    exit;
}

$parsed_addr = valiente_parse_address($row['address']);
$addr_m = $parsed_addr['municipality'];
$addr_b = $parsed_addr['barangay'];
$use_legacy_address = !(
    $addr_m !== ''
    && isset($barangays_by_municipality[$addr_m])
    && in_array($addr_b, $barangays_by_municipality[$addr_m], true)
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['fullname'] ?? '';
    $age = $_POST['age'] ?? '';
    $address_legacy = trim((string) ($_POST['address_legacy'] ?? ''));
    if ($address_legacy !== '') {
        $address = $address_legacy;
    } else {
        $municipality = trim((string) ($_POST['municipality'] ?? ''));
        $barangay = trim((string) ($_POST['barangay'] ?? ''));
        $address = valiente_build_address_from_post($barangays_by_municipality, $municipality, $barangay);
        if ($address === '' && $municipality !== '' && $barangay !== '') {
            $prev = valiente_parse_address($row['address']);
            if ($prev['municipality'] === $municipality && $prev['barangay'] === $barangay) {
                $address = (string) $row['address'];
            }
        }
    }
    $course = $_POST['course'] ?? '';
    $school = $_POST['school'] ?? '';

    if ($name && $age && $address && $course && $school) {
        $update = $connection->prepare('UPDATE data SET name=?, age=?, address=?, course=?, school=? WHERE id=?');
        $update->bind_param('sisssi', $name, $age, $address, $course, $school, $id);
        $update->execute();
        header('Location: readtable.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit student</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="page-centered">
<script src="theme.js"></script>
<script>
  ValienteTheme.boot();
</script>
<div class="toggle" onclick="ValienteTheme.toggle(this)" title="Toggle light / dark mode" role="button" tabindex="0" aria-label="Toggle theme" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();ValienteTheme.toggle(this);}">🌙</div>

<div class="edit-shell">
  <h2>Edit student</h2>
  <form method="post" action="">
    <label class="field-label" for="fullname">Full name</label>
    <input id="fullname" type="text" name="fullname" value="<?= htmlspecialchars($row['name']) ?>" required autocomplete="name" />

    <label class="field-label" for="age_edit">Age</label>
    <select id="age_edit" name="age" required>
      <option value="" disabled>Select age</option>
      <?php
      $age_row = (int) $row['age'];
      for ($a = 16; $a <= 45; $a++) {
          $selected = ($age_row === $a) ? 'selected' : '';
          echo '<option value="' . $a . '" ' . $selected . '>' . $a . ' years old</option>';
      }
      if ($age_row && ($age_row < 16 || $age_row > 45)) {
          echo '<option value="' . $age_row . '" selected>' . $age_row . ' years old (saved)</option>';
      }
      ?>
    </select>

    <?php if ($use_legacy_address): ?>
    <label class="field-label" for="address_legacy">Full address (edit as text)</label>
    <textarea id="address_legacy" name="address_legacy" rows="3" required><?= htmlspecialchars($row['address']) ?></textarea>
    <?php else: ?>
    <label class="field-label" for="municipality">City / municipality</label>
    <select id="municipality" name="municipality" required>
      <option value="" disabled>Select city or municipality</option>
      <?php foreach (array_keys($barangays_by_municipality) as $mun): ?>
      <?php $sel = ($mun === $addr_m) ? ' selected' : ''; ?>
      <option value="<?= htmlspecialchars($mun) ?>"<?= $sel ?>><?= htmlspecialchars($mun) ?></option>
      <?php endforeach; ?>
    </select>
    <label class="field-label" for="barangay">Barangay</label>
    <select id="barangay" name="barangay" required>
      <option value="" disabled>Select barangay</option>
    </select>
    <?php endif; ?>

    <label class="field-label" for="course_edit">Course</label>
    <select id="course_edit" name="course" required>
      <option value="" disabled>Select course</option>
      <?php
      $cur_course = (string) $row['course'];
      $course_known = $cur_course !== '' && in_array($cur_course, $courses, true);
      if ($cur_course !== '' && !$course_known) {
          echo '<option value="' . htmlspecialchars($cur_course) . '" selected>' . htmlspecialchars($cur_course) . ' (saved)</option>';
      }
      foreach ($courses as $c) {
          $sel = ($cur_course === $c) ? ' selected' : '';
          echo '<option value="' . htmlspecialchars($c) . '"' . $sel . '>' . htmlspecialchars($c) . '</option>';
      }
      ?>
    </select>

    <label class="field-label" for="school_edit">School</label>
    <select id="school_edit" name="school" required>
      <option value="" disabled>Select school</option>
      <?php
      $cur_school = (string) $row['school'];
      $school_vals = array_column($schools, 'value');
      $school_known = $cur_school !== '' && in_array($cur_school, $school_vals, true);
      if ($cur_school !== '' && !$school_known) {
          echo '<option value="' . htmlspecialchars($cur_school) . '" selected>' . htmlspecialchars($cur_school) . ' (saved)</option>';
      }
      foreach ($schools as $s) {
          $sel = ($cur_school === $s['value']) ? ' selected' : '';
          echo '<option value="' . htmlspecialchars($s['value']) . '"' . $sel . '>' . htmlspecialchars($s['label']) . '</option>';
      }
      ?>
    </select>

    <button type="submit">Update student</button>
  </form>
  <a class="back-link" href="readtable.php">← Back to table</a>
</div>

<script>
  ValienteTheme.syncToggle(document.querySelector('.toggle'));
</script>
<?php if (!$use_legacy_address): ?>
<script src="address_cascade.js"></script>
<script>
ValienteAddressInit(<?= json_encode([
    'municipalityId' => 'municipality',
    'barangayId' => 'barangay',
    'data' => $barangays_by_municipality,
    'initialMunicipality' => $addr_m,
    'initialBarangay' => $addr_b,
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
</script>
<?php endif; ?>
</body>
</html>
