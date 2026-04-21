<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'connection.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim((string) ($_POST['username']  ?? ''));
    $email     = trim((string) ($_POST['email']     ?? ''));
    $password  = (string) ($_POST['password']       ?? '');
    $password2 = (string) ($_POST['password2']      ?? '');

    if ($username === '' || $email === '' || $password === '' || $password2 === '') {
        $error = 'Please fill out all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $password2) {
        $error = 'Passwords do not match.';
    } else {
        // Check for duplicate username or email
        $chk = $connection->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $chk->bind_param('ss', $username, $email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $error = 'Username or email is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $connection->prepare('INSERT INTO users (username, email, password) VALUES (?, ?, ?)');
            $ins->bind_param('sss', $username, $email, $hash);
            if ($ins->execute()) {
                $success = 'Account created! You can now <a href="login.php">sign in</a>.';
            } else {
                $error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Sign up — Valiente</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="page-centered">
<script src="theme.js"></script>
<script>ValienteTheme.boot();</script>
<div class="toggle" onclick="ValienteTheme.toggle(this)" title="Toggle light / dark mode" role="button" tabindex="0" aria-label="Toggle theme" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();ValienteTheme.toggle(this);}">🌙</div>

<div class="container">
  <div class="brand">
    <h1>Create account</h1>
    <p>Negros Oriental · Region VII — quick signup</p>
  </div>

  <?php if ($error !== ''): ?>
  <div class="auth-error" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if ($success !== ''): ?>
  <div class="auth-success" role="status"><?= $success /* trusted HTML: only set by us */ ?></div>
  <?php endif; ?>

  <?php if ($success === ''): ?>
  <form class="form-stack" method="post" action="" autocomplete="off">
    <div class="form-field">
      <label for="username">Username</label>
      <input id="username" type="text" name="username" placeholder="juan.delacruz" required
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username" />
    </div>

    <div class="form-field">
      <label for="email">Email address</label>
      <input id="email" type="email" name="email" placeholder="juan@example.com" required
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="email" />
    </div>

    <div class="form-field">
      <label for="password">Password <span style="font-weight:400;font-size:.8em;text-transform:none">(min. 8 characters)</span></label>
      <div class="pw-wrap">
        <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="new-password" />
        <button type="button" class="pw-eye" onclick="togglePw('password', this)" aria-label="Show password">👁</button>
      </div>
    </div>

    <div class="form-field">
      <label for="password2">Confirm password</label>
      <div class="pw-wrap">
        <input id="password2" type="password" name="password2" placeholder="••••••••" required autocomplete="new-password" />
        <button type="button" class="pw-eye" onclick="togglePw('password2', this)" aria-label="Show confirm password">👁</button>
      </div>
    </div>

    <button type="submit">Create account</button>
  </form>
  <?php endif; ?>

  <div class="links">
    <span>Already have an account?</span>
    <a href="login.php">Sign in</a>
  </div>
</div>

<script>ValienteTheme.syncToggle(document.querySelector('.toggle'));</script>
<script>
function togglePw(id, btn) {
  var inp = document.getElementById(id);
  if (inp.type === 'password') { inp.type = 'text'; btn.textContent = '🙈'; }
  else { inp.type = 'password'; btn.textContent = '👁'; }
}
</script>
</body>
</html>
