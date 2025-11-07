<?php
require_once __DIR__.'/lib/db.php';
require_once __DIR__.'/lib/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_post();
  $del = db()->prepare("DELETE FROM posts WHERE id=?");
  $del->execute([$id]);
  header('Location: index.php'); exit;
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Delete Post</title>
<link rel="stylesheet" href="assets/styles.css"></head><body>
<h2>Delete Post</h2>
<p>Are you sure you want to delete this post?</p>
<form method="post">
  <?php csrf_field(); ?>
  <button>Yes, delete</button> <a href="index.php">Cancel</a>
</form>
</body></html>
