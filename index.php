<?php
require __DIR__.'/lib/db.php';
require __DIR__.'/lib/auth.php';

$user = current_user();

// --------- Inputs ----------
$q     = trim($_GET['q'] ?? '');              // search query
$page  = max(1, (int)($_GET['page'] ?? 1));   // current page
$perPage = 5;                                  // posts per page

// --------- WHERE clause + params ----------
$where  = '';
$params = [];
if ($q !== '') {
  $where = 'WHERE title LIKE :q OR content LIKE :q';
  $params[':q'] = "%{$q}%";
}

// --------- Count total ----------
$count = db()->prepare("SELECT COUNT(*) AS c FROM posts $where");
$count->execute($params);
$total  = (int)($count->fetch()['c'] ?? 0);
$pages  = max(1, (int)ceil($total / $perPage));
if ($page > $pages) $page = $pages; // clamp if someone types a huge page

// --------- Fetch current page ----------
$sql = "SELECT * FROM posts $where ORDER BY created_at DESC LIMIT :lim OFFSET :off";
$stmt = db()->prepare($sql);
if ($q !== '') $stmt->bindValue(':q', "%{$q}%", PDO::PARAM_STR);
$stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':off', ($page - 1) * $perPage, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();

// helper for safely echoing
function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Blog – Home</title>
  <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<header>
  <h1>My Blog</h1>
  <nav>
    <?php if ($user): ?>
      <span>Hi, <?= h($user['username']) ?></span>
      <a class="btn" href="post_create.php">New Post</a>
      <a class="btn" href="logout.php">Logout</a>
    <?php else: ?>
      <a class="btn" href="login.php">Login</a>
      <a class="btn" href="register.php">Register</a>
    <?php endif; ?>
  </nav>
</header>

<!-- Search Bar -->
<form method="get" class="searchbar">
  <input name="q" value="<?= h($q) ?>" placeholder="Search posts (title or content)">
  <?php if ($q !== ''): ?>
    <a class="btn-link" href="index.php">Reset</a>
  <?php endif; ?>
  <button class="btn">Search</button>
</form>

<main>
  <?php if ($total === 0): ?>
    <p class="muted">
      <?php if ($q === ''): ?>
        No posts yet.
      <?php else: ?>
        No results for “<?= h($q) ?>”.
      <?php endif; ?>
    </p>
  <?php else: ?>
    <?php foreach ($posts as $p): ?>
      <article class="card">
        <h3><?= h($p['title']) ?></h3>
        <p><?= nl2br(h($p['content'])) ?></p>
        <small class="muted"><?= h($p['created_at']) ?></small>
        <?php if ($user && is_admin()): ?>
  <div class="actions">
    <a href="post_edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
    <a href="post_delete.php?id=<?= (int)$p['id'] ?>">Delete</a>
  </div>
<?php endif; ?>
      </article>
    <?php endforeach; ?>

    <!-- Pagination -->
    <?php
      $qs = $q !== '' ? '&q='.urlencode($q) : '';
      $window = 2; // show 2 pages before/after current
      $start = max(1, $page - $window);
      $end   = min($pages, $page + $window);
    ?>
    <nav class="pagination">
      <?php if ($page > 1): ?>
        <a href="index.php?page=1<?= $qs ?>">« First</a>
        <a href="index.php?page=<?= $page-1 ?><?= $qs ?>">‹ Prev</a>
      <?php endif; ?>

      <?php for ($i = $start; $i <= $end; $i++): ?>
        <?php if ($i === $page): ?>
          <span class="current"><?= $i ?></span>
        <?php else: ?>
          <a href="index.php?page=<?= $i ?><?= $qs ?>"><?= $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>

      <?php if ($page < $pages): ?>
        <a href="index.php?page=<?= $page+1 ?><?= $qs ?>">Next ›</a>
        <a href="index.php?page=<?= $pages ?><?= $qs ?>">Last »</a>
      <?php endif; ?>
    </nav>
  <?php endif; ?>
</main>
</body>
</html>
