<?php
require_once 'config/db.php';
if (!isLoggedIn()) redirect('login.php');
$user_id = getCurrentUserId();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM animals WHERE owner_id = ?");
$stmt->execute([$user_id]); $totalAnimals = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM litters WHERE mother_id IN (SELECT id FROM animals WHERE owner_id = ?)");
$stmt->execute([$user_id]); $totalLitters = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM sales WHERE animal_id IN (SELECT id FROM animals WHERE owner_id = ?)");
$stmt->execute([$user_id]); $totalSales = $stmt->fetchColumn();
$healthStats = [];
if (canViewMedical()) {
    $stmt = $pdo->prepare("SELECT record_type, COUNT(*) as cnt FROM veterinary_records WHERE animal_id IN (SELECT id FROM animals WHERE owner_id = ?) GROUP BY record_type");
    $stmt->execute([$user_id]); $healthStats = $stmt->fetchAll();
}
// Обработка экспорта
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_type'])) {
    $type = $_POST['export_type'];
    if ($type === 'animals') {
        $stmt = $pdo->prepare("SELECT name, species, breed, gender, birth_date, price, status FROM animals WHERE owner_id = ?");
        $stmt->execute([$user_id]); $data = $stmt->fetchAll();
        exportToExcel($data, 'animals_report', ['Кличка','Вид','Порода','Пол','Дата рождения','Цена','Статус']);
    } elseif ($type === 'sales') {
        $stmt = $pdo->prepare("SELECT a.name, s.buyer_name, s.sale_date, s.price, s.destination_info FROM sales s JOIN animals a ON s.animal_id = a.id WHERE a.owner_id = ?");
        $stmt->execute([$user_id]); $data = $stmt->fetchAll();
        exportToExcel($data, 'sales_report', ['Животное','Покупатель','Дата продажи','Цена','Куда продан']);
    } elseif ($type === 'breedings') {
        $stmt = $pdo->prepare("SELECT f.name as female, m.name as male, b.planned_date, b.status FROM breedings b JOIN animals f ON b.female_id = f.id JOIN animals m ON b.male_id = m.id WHERE f.owner_id = ? OR m.owner_id = ?");
        $stmt->execute([$user_id, $user_id]); $data = $stmt->fetchAll();
        exportToExcel($data, 'breedings_report', ['Самка','Самец','Плановая дата','Статус']);
    }
}
?>
<!DOCTYPE html>
<html><head><title>Дашборд</title><script src="https://cdn.jsdelivr.net/npm/chart.js"></script><link rel="stylesheet" href="assets/css/style.css"></head>
<body><?php include 'nav.inc.php'; ?>
<div class="container">
    <h1>Дашборд</h1>
    <div class="card"><canvas id="mainChart" width="400" height="200"></canvas></div>
    <?php if (canViewMedical() && $healthStats): ?><div class="card"><canvas id="healthChart" width="400" height="200"></canvas></div><?php endif; ?>
    <div class="card">
        <h2>Экспорт отчётов в Excel (CSV)</h2>
        <div class="alert alert-info" style="background:#e0f0e0; border-left-color:#2c5f2d;">
            📊 Отчёты экспортируются в формате CSV, совместимом с Microsoft Excel.<br>
            После скачивания откройте файл через меню <strong>Файл → Открыть</strong> или импортируйте данные.
        </div>
        <form method="post"><button type="submit" name="export_type" value="animals">🐕 Экспорт животных</button> <button type="submit" name="export_type" value="sales">💰 Экспорт продаж</button> <button type="submit" name="export_type" value="breedings">❤️ Экспорт вязок</button></form>
    </div>
</div>
<?php include 'footer.inc.php'; ?>
<script>
new Chart(document.getElementById('mainChart'), { type:'bar', data:{ labels:['Животные','Помёты','Продажи'], datasets:[{ label:'Количество', data:[<?=$totalAnimals?>,<?=$totalLitters?>,<?=$totalSales?>], backgroundColor:'#3a7e3b' }] } });
<?php if($healthStats): ?>
new Chart(document.getElementById('healthChart'), { type:'pie', data:{ labels:[<?php foreach($healthStats as $h) echo "'".addslashes($h['record_type'])."',"; ?>], datasets:[{ data:[<?php foreach($healthStats as $h) echo $h['cnt'].","; ?>] }] } });
<?php endif; ?>
</script>
</body></html>