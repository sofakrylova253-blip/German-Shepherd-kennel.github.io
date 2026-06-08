<?php require_once 'config/db.php';
$faqs = $pdo->query("SELECT * FROM faq ORDER BY sort_order")->fetchAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>FAQ</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
<?php include 'nav.inc.php'; ?>
    <div class="container">
        <h1>Часто задаваемые вопросы</h1>
<?php foreach($faqs as $faq): ?>
    <div class="card">
        <h3><?= escape($faq['question']) ?></h3>
        <p><?= nl2br(escape($faq['answer'])) ?></p>
    </div>
<?php endforeach; ?>
    </div>
<?php include 'footer.inc.php'; ?>
</body>
</html>