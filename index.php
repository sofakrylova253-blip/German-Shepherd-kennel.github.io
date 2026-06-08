<?php require_once 'config/db.php';
$stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC LIMIT 5");
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Питомник "Немецкая овчарка" – племенная работа</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'nav.inc.php'; ?>
<div class="container">
    <div class="card">
        <h1>О компании</h1>
        <p>Наш питомник занимается разведением немецких овчарок с 2010 года. Мы гордимся своими питомцами – чемпионами выставок, обладателями отличных рабочих качеств. Все животные имеют полный пакет документов, чипированы и привиты. Мы предоставляем полную поддержку новым владельцам, помогаем с воспитанием и адаптацией щенка в семье.</p>
        <p>Наша цель – сохранить и улучшить породные качества, получить здоровое, психически устойчивое потомство, радующее своих хозяев.</p>
    </div>
    
    <h2>Последние новости</h2>
    <?php foreach($posts as $post): ?>
        <div class="card">
            <h3><a href="blog_post.php?id=<?= $post['id'] ?>"><?= escape($post['title']) ?></a></h3>
            <small><?= $post['created_at'] ?></small>
            <p><?= nl2br(escape(substr($post['content'],0,300))) ?>...</p>
        </div>
    <?php endforeach; ?>
</div>
<?php include 'footer.inc.php'; ?>
</body>
</html>