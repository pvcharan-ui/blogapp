<?php
require_once __DIR__.'/lib/db.php';
require_once __DIR__.'/lib/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_post();

  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';
  $c = $_POST['confirm'] ?? '';

  // server-side validation
  if ($u === '' || $p === '' || $c === '') $errors[] = 'All fields are required.';
  if (strlen($u) < 3 || strlen($u) > 50) $errors[] = 'Username must be 3–50 characters.';
  if (strlen($p) < 6) $errors[] = 'Password must be at least 6 characters.';
  if ($p !== $c) $errors[] = 'Passwords do not match.';

  if (!$errors) {
    $q = db()->prepare("SELECT id FROM users WHERE username=?");
    $q->execute([$u]);
    if ($q->fetch()) $errors[] = 'Username already taken.';
  }

  if (!$errors) {
    $hash = password_hash($p, PASSWORD_DEFAULT);
    $ins = db()->prepare("INSERT INTO users(username,password) VALUES(?,?)");
    $ins->execute([$u,$hash]);
    header('Location: login.php?registered=1'); exit;
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Register</title>
<link rel="stylesheet" href="assets/styles.css"></head><body>
<h2>Create account</h2>
<?php foreach($errors as $e) echo "<p class='error'>".h($e)."</p>"; ?>
<form method="post" novalidate>
  <?php csrf_field(); ?>
  <label>Username <input name="username" required minlength="3" maxlength="50"></label>
  <label>Password <input type="password" name="password" required minlength="6"></label>
  <label>Confirm  <input type="password" name="confirm" required minlength="6"></label>
  <button>Sign up</button>
</form>
<p>Have an account? <a href="login.php">Login</a></p>
</body></html>
