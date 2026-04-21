<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Already logged in → go to dashboard
if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please fill out all fields.';
    } else {
        $stmt = $connection->prepare('SELECT id, password FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username / email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login — Valiente</title>
<link rel="stylesheet" href="style.css" />
</head>
<body class="page-centered">
<script src="theme.js"></script>
<script>ValienteTheme.boot();</script>
<div class="toggle" onclick="ValienteTheme.toggle(this)" title="Toggle light / dark mode" role="button" tabindex="0" aria-label="Toggle theme" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();ValienteTheme.toggle(this);}">🌙</div>

<div class="container">
  <div class="brand">
    <h1>Welcome back</h1>
    <p>Negros Oriental · Region VII — sign in to continue</p>
  </div>

  <?php if ($error !== ''): ?>
  <div class="auth-error" role="alert"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form class="form-stack" method="post" action="" autocomplete="off">
    <div class="form-field">
      <label for="username">Username or email</label>
      <input id="username" type="text" name="username" placeholder="juan.delacruz" required
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" autocomplete="username" />
    </div>

    <div class="form-field">
      <label for="password">Password</label>
      <div class="pw-wrap">
        <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password" />
        <button type="button" class="pw-eye" onclick="togglePw('password', this)" aria-label="Show password">👁</button>
      </div>
    </div>

    <button type="submit">Sign in</button>
  </form>

  <div class="links">
    <span>No account yet?</span>
    <a href="signup.php">Create one</a>
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
