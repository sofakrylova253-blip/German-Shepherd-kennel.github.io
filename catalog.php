<?php require_once 'config/db.php';
$species = $_GET['species'] ?? '';
$breed = $_GET['breed'] ?? '';
$gender = $_GET['gender'] ?? '';
$sql = "SELECT a.*, (SELECT photo_path FROM animal_photos WHERE animal_id = a.id AND is_main = 1 LIMIT 1) as photo FROM animals a WHERE a.status = 'available'";
$params = [];
if($species) { $sql .= " AND a.species = ?"; $params[] = $species; }
if($breed) { $sql .= " AND a.breed = ?"; $params[] = $breed; }
if($gender) { $sql .= " AND a.gender = ?"; $params[] = $gender; }
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$animals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Каталог</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
<?php include 'nav.inc.php'; ?>
<div class="container">
    <h1>Доступные животные</h1>
    <div class="card">
        <form method="get">
            <label>Вид:</label>
            <input type="text" name="species" value="<?= escape($species) ?>">
            <label>Порода:</label>
            <input type="text" name="breed" value="<?= escape($breed) ?>">
            <label>Пол:</label>
            <select name="gender">
                <option value="">Любой</option><option value="male">Самец</option><option value="female">Самка</option>
            </select>
            <button type="submit">Фильтр</button>
        </form>
    </div>
    <div class="catalog-grid">
        <?php foreach($animals as $a): ?>
        <div class="animal-card">
            <img src="<?= $a['photo']?:'assets/img/no-image.png' ?>">
            <div style="padding:10px">
                <h3><?= escape($a['name']) ?></h3>
                <p><?= escape($a['breed']) ?>, <?= $a['gender']=='male'?'Самец':'Самка' ?></p>
                <p>Цена: <?= number_format($a['price'],2) ?> ₽</p>
                <a href="animal_card.php?id=<?= $a['id'] ?>" class="btn">Подробнее</a>
                <?php if(isLoggedIn() && isBuyer()): ?>
                    <button onclick="bookAnimal(<?= $a['id'] ?>)">Забронировать</button>
                    <?php endif; ?>
                </div>
            </div>
    <?php endforeach; ?>
</div>
</div>
<?php include 'footer.inc.php'; ?>
<script src="assets/js/script.js"></script>
</body>
</html>