<?php
require_once __DIR__.'/lib/db.php';
require_once __DIR__.'/lib/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$find = db()->prepare("SELECT * FROM posts WHERE id=?");
$find->execute([$id]);
$post = $find->fetch();
if (!$post) { http_response_code(404); exit('Post not found'); }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  verify_csrf_post();
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content'] ?? '');
  if ($title === '' || $content === '') $errors[] = 'Title and content are required.';
  if (strlen($title) > 200) $errors[] = 'Title must be ≤ 200 characters.';
  if (!$errors) {
    $upd = db()->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
    $upd->execute([$title, $content, $id]);
    header('Location: index.php'); exit;
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit Post</title>
<link rel="stylesheet" href="assets/styles.css"></head><body>
<h2>Edit Post</h2>
<?php foreach($errors as $e) echo "<p class='error'>".h($e)."</p>"; ?>
<form method="post" novalidate>
  <?php csrf_field(); ?>
  <label>Title <input name="title" value="<?= h($post['title']) ?>" maxlength="200" required></label>
  <label>Content <textarea name="content" rows="8" required><?= h($post['content']) ?></textarea></label>
  <button>Save</button> <a href="index.php">Cancel</a>
</form>
</body></html>
