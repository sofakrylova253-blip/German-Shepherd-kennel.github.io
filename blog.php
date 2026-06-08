<?php require_once 'config/db.php';
$posts = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Блог</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
<?php include 'nav.inc.php'; ?>
    <div class="container">
        <h1>Новости и статьи</h1>
<?php foreach($posts as $p): ?>
    <div class="card">
        <h2><a href="blog_post.php?id=<?= $p['id'] ?>"><?= escape($p['title']) ?></a></h2>
        <small><?= $p['created_at'] ?></small>
        <p><?= nl2br(escape(substr($p['content'],0,300))) ?>...</p>
    </div><?php endforeach; ?>
</div>
<?php include 'footer.inc.php'; ?>
</body>
</html>