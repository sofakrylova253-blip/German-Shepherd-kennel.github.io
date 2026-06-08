<?php require_once 'config/db.php';
if(!isBreeder() && !isAdmin()) die("Доступ только для заводчиков");
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_breeding'])) {
    $stmt = $pdo->prepare("INSERT INTO breedings (female_id, male_id, planned_date, notes, created_by) VALUES (?,?,?,?,?)");
    $stmt->execute([$_POST['female_id'], $_POST['male_id'], $_POST['planned_date'], $_POST['notes'], getCurrentUserId()]);
    logAudit("ADD_BREEDING", "breedings", $pdo->lastInsertId());
    redirect('breeding_calendar.php');
}
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_litter'])) {
    $stmt = $pdo->prepare("INSERT INTO litters (breeding_id, mother_id, father_id, birth_date, puppies_count, notes) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$_POST['breeding_id'], $_POST['mother_id'], $_POST['father_id'], $_POST['birth_date'], $_POST['puppies_count'], $_POST['notes']]);
    notifySubscribers('Собака', 'Немецкая овчарка', "Новый помёт от ".$_POST['mother_id']." родился ".$_POST['birth_date']);
    redirect('breeding_calendar.php');
}
$breedings = $pdo->query("SELECT b.*, f.name as female_name, m.name as male_name FROM breedings b JOIN animals f ON b.female_id=f.id JOIN animals m ON b.male_id=m.id ORDER BY b.planned_date DESC")->fetchAll();
$litters = $pdo->query("SELECT l.*, f.name as mother_name, m.name as father_name FROM litters l JOIN animals f ON l.mother_id=f.id JOIN animals m ON l.father_id=m.id ORDER BY l.birth_date DESC")->fetchAll();
$females = $pdo->query("SELECT id,name FROM animals WHERE gender='female' AND status='available'")->fetchAll();
$males = $pdo->query("SELECT id,name FROM animals WHERE gender='male' AND status='available'")->fetchAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Вязки и помёты</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
    <?php include 'nav.inc.php'; ?>
    <div class="container">
    <h1>Планирование вязок</h1>
    <div class="card">
        <form method="post">
            <input type="hidden" name="add_breeding" value="1">
            <label>Самка</label>
            <select name="female_id" required>
                <?php foreach($females as $f) echo "<option value='{$f['id']}'>{$f['name']}</option>"; ?>
            </select>
            <label>Самец</label>
            <select name="male_id" required>
                <?php foreach($males as $m) echo "<option value='{$m['id']}'>{$m['name']}</option>"; ?>
            </select>
            <label>Плановая дата</label>
            <input type="date" name="planned_date" required>
            <label>Примечания</label>
            <textarea name="notes"></textarea>
            <button type="submit">Запланировать</button>
        </form>
    </div>
    <h2>Список вязок</h2>
    <div class="card">
        <table>
            <tr>
                <th>Самка</th>
                <th>Самец</th>
                <th>Плановая дата</th>
                <th>Статус</th>
            </tr>
            <?php foreach($breedings as $b): ?>
                <tr>
                    <td><?= $b['female_name'] ?></td>
                    <td><?= $b['male_name'] ?></td>
                    <td><?= $b['planned_date'] ?></td>
                    <td><?= $b['status'] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <h2>Зарегистрированные помёты</h2>
    <div class="card">
        <form method="post">
            <input type="hidden" name="add_litter" value="1">
            <label>Вязка (опц.)</label>
            <select name="breeding_id">
                <option value="">—</option>
                <?php foreach($breedings as $b) echo "<option value='{$b['id']}'>{$b['female_name']} x {$b['male_name']}</option>"; ?>
            </select>
            <label>Мать</label>
            <select name="mother_id" required><?php foreach($females as $f) echo "<option value='{$f['id']}'>{$f['name']}</option>"; ?></select>
                <label>Отец</label>
                <select name="father_id" required>
                    <?php foreach($males as $m) echo "<option value='{$m['id']}'>{$m['name']}</option>"; ?>
                </select>
                <label>Дата рождения</label>
                <input type="date" name="birth_date" required>
                <label>Кол-во детёнышей</label>
                <input type="number" name="puppies_count" required>
                <label>Примечания</label>
                <textarea name="notes">
                </textarea>
                <button type="submit">Зарегистрировать</button>
            </form>
            <hr>
            <?php foreach($litters as $l): ?>
                <p><b><?= $l['mother_name'] ?> + <?= $l['father_name'] ?></b> — <?= $l['birth_date'] ?>, детёнышей: <?= $l['puppies_count'] ?></p>
                <?php endforeach; ?>
            </div>
        </div>
        <?php include 'footer.inc.php'; ?>
    </body>
</html>