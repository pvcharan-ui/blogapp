<?php
require __DIR__.'/lib/db.php';
require __DIR__.'/lib/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $u = trim($_POST['username'] ?? '');
  $p = $_POST['password'] ?? '';

  $stmt = db()->prepare("SELECT * FROM users WHERE username=?");
  $stmt->execute([$u]);
  $user = $stmt->fetch();

  if ($user && password_verify($p, $user['password'])) {
    do_login($user);
    header('Location: index.php'); exit;
  } else {
    $errors[] = 'Invalid username or password.';
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Login</title><link rel="stylesheet" href="assets/styles.css"></head>
<body>
<h2>Login</h2>
<?php if(isset($_GET['registered'])) echo "<p class='success'>Registered! Please login.</p>"; ?>
<?php foreach($errors as $e) echo "<p class='error'>".h($e)."</p>"; ?>
<form method="post">
  <label>Username <input name="username" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button>Login</button>
</form>
<p>No account? <a href="register.php">Register</a></p>
</body></html>
