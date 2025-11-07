<?php
require_once __DIR__.'/lib/db.php';
require_once __DIR__.'/lib/auth.php';

$errors = [];
// simple rate limiting (session-based)
$_SESSION['login'] = $_SESSION['login'] ?? ['count'=>0,'start'=>time()];
$win =& $_SESSION['login'];
if (time() - $win['start'] > 600) { $win = ['count'=>0,'start'=>time()]; } // reset every 10 mins

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_post();
  if ($win['count'] >= 5) { $errors[] = 'Too many attempts. Try again later.'; }
  else {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    if ($u === '' || $p === '') $errors[] = 'Enter username and password.';
    if (!$errors) {
      $stmt = db()->prepare("SELECT * FROM users WHERE username=?");
      $stmt->execute([$u]);
      $user = $stmt->fetch();
      if ($user && password_verify($p, $user['password'])) {
        do_login($user);
        $win = ['count'=>0,'start'=>time()];
        header('Location: index.php'); exit;
      } else {
        $win['count']++;
        $errors[] = 'Invalid username or password.';
      }
    }
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Login</title>
<link rel="stylesheet" href="assets/styles.css"></head><body>
<h2>Login</h2>
<?php if(isset($_GET['registered'])) echo "<p class='success'>Registered! Please login.</p>"; ?>
<?php foreach($errors as $e) echo "<p class='error'>".h($e)."</p>"; ?>
<form method="post" novalidate>
  <?php csrf_field(); ?>
  <label>Username <input name="username" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button>Login</button>
</form>
<p>No account? <a href="register.php">Register</a></p>
</body></html>
