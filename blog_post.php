<?php require_once 'config/db.php';
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();
if(!$post) die("Статья не найдена");
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= escape($post['title']) ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
<?php include 'nav.inc.php'; ?>
    <div class="container">
        <div class="card">
            <h1><?= escape($post['title']) ?></h1>
            <small><?= $post['created_at'] ?></small>
            <p><?= nl2br(escape($post['content'])) ?></p>
        </div>
    </div>
<?php include 'footer.inc.php'; ?>
</body>
</html>