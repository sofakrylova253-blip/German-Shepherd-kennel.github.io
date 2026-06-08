<?php require_once 'config/db.php';
if(!isset($_GET['id'])) die("Животное не найдено");
$animal_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT a.*, u.name as owner_name FROM animals a JOIN users u ON a.owner_id = u.id WHERE a.id = ?");
$stmt->execute([$animal_id]);
$animal = $stmt->fetch();
if(!$animal) die("Животное не найдено");
$ancestors = getAncestors($pdo, $animal_id, 1, 3);
$stmt = $pdo->prepare("SELECT * FROM animal_achievements WHERE animal_id = ?");
$stmt->execute([$animal_id]); $achievements = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM veterinary_records WHERE animal_id = ? ORDER BY visit_date DESC");
$stmt->execute([$animal_id]); $vet_records = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM genetic_tests WHERE animal_id = ?");
$stmt->execute([$animal_id]); $gen_tests = $stmt->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM bonitization WHERE animal_id = ?");
$stmt->execute([$animal_id]); $bonit = $stmt->fetch();
function renderTree($node) {
    if(!$node) return;
    echo "<ul><li><strong>{$node['name']}</strong> ({$node['breed']}, рег.{$node['reg_number']})";
    if($node['father'] || $node['mother']) { echo "<ul>"; renderTree($node['father']); renderTree($node['mother']); echo "</ul>"; }
    echo "</li></ul>";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?= escape($animal['name']) ?></title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
<?php include 'nav.inc.php'; ?>
<div class="container">
    <h1><?= escape($animal['name']) ?></h1>
    <div class="card">
        <p><strong>Чип/Тату:</strong> <?= escape($animal['chip_number']) ?></p>
        <p><strong>Рег. номер:</strong> <?= escape($animal['reg_number']) ?></p>
        <p><strong>Порода:</strong> <?= escape($animal['breed']) ?></p>
        <p><strong>Масть:</strong> <?= escape($animal['color']) ?></p>
        <p><strong>Дата рождения:</strong> <?= $animal['birth_date'] ?></p>
        <p><strong>Пол:</strong> <?= $animal['gender']=='male'?'Самец':'Самка' ?></p>
        <p><strong>Цена:</strong> <?= number_format($animal['price'],2) ?> ₽</p>
        <p><strong>Владелец:</strong> <?= escape($animal['owner_name']) ?></p>
        <p><strong>Описание:</strong> <?= nl2br(escape($animal['description'])) ?></p>
    </div>
    <h3>Родословная (3 поколения)</h3>
    <div class="card tree">
        <?php renderTree($ancestors); ?>
    </div>
    <?php if($achievements): ?>
        <h3>Достижения</h3>
        <div class="card">
            <?php foreach($achievements as $ach): ?>
                <b><?= $ach['achievement_type'] ?></b>: <?= escape($ach['title']) ?> (<?= $ach['date'] ?>)
                <br><?php endforeach; ?>
            </div>
            <?php endif; ?>
    <h3>Ветеринарные записи</h3>
    <div class="card">
        <?php foreach($vet_records as $vr): ?><b><?= $vr['visit_date'] ?></b> — <?= $vr['record_type'] ?>: <?= nl2br(escape($vr['description'])) ?>
            <br><?php endforeach; ?>
        </div>
    <?php if($gen_tests): ?>
        <h3>Генетические тесты</h3>
        <div class="card">
            <?php foreach($gen_tests as $gt): ?><b><?= $gt['test_name'] ?></b> (<?= $gt['test_date'] ?>): <?= escape($gt['result']) ?>
                <br><?php endforeach; ?>
            </div>
            <?php endif; ?>
    <?php if($bonit): ?>
        <h3>Бонитировка</h3>
        <div class="card">
            Оценка: <?= $bonit['score'] ?> баллов, эксперт: <?= escape($bonit['expert_name']) ?>, дата: <?= $bonit['assessment_date'] ?>
        </div>
        <?php endif; ?>
</div>
<?php include 'footer.inc.php'; ?>
</body>
</html>