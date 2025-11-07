<?php
require __DIR__.'/lib/db.php';
require __DIR__.'/lib/auth.php';
$user = current_user();
$posts = db()->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html><html><head>
<meta charset="utf-8"><title>Blog – Home</title>
<link rel="stylesheet" href="assets/styles.css">
</head><body>
<header>
  <h1>My Blog</h1>
  <nav>
    <?php if($user): ?>
      <span>Hi, <?= h($user['username']) ?></span>
      <a href="post_create.php">New Post</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>
<main>
<?php if (!$posts): ?>
  <p>No posts yet.</p>
<?php else: foreach($posts as $p): ?>
  <article class="card">
    <h3><?= h($p['title']) ?></h3>
    <p><?= nl2br(h($p['content'])) ?></p>
    <small><?= h($p['created_at']) ?></small>
    <?php if($user): ?>
      <div class="actions">
        <a href="post_edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
        <a href="post_delete.php?id=<?= (int)$p['id'] ?>">Delete</a>
      </div>
    <?php endif; ?>
  </article>
<?php endforeach; endif; ?>
</main>
</body></html>
