<?php
require __DIR__.'/lib/db.php';
require __DIR__.'/lib/auth.php';
require_login();

$errors=[];
if($_SERVER['REQUEST_METHOD']==='POST'){
  $title=trim($_POST['title']??'');
  $content=trim($_POST['content']??'');
  if($title===''||$content===''){ $errors[]='Title and content are required.'; }
  if(!$errors){
    $ins=db()->prepare("INSERT INTO posts(title,content) VALUES(?,?)");
    $ins->execute([$title,$content]);
    header('Location:index.php'); exit;
  }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>New Post</title><link rel="stylesheet" href="assets/styles.css"></head>
<body>
<h2>New Post</h2>
<?php foreach($errors as $e) echo "<p class='error'>".h($e)."</p>"; ?>
<form method="post">
  <label>Title <input name="title" required></label>
  <label>Content <textarea name="content" rows="8" required></textarea></label>
  <button>Create</button> <a href="index.php">Cancel</a>
</form>
</body></html>
