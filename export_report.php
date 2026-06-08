<?php
require_once 'config/db.php';
if(!isBreeder() && !isAdmin()) die("Нет прав");

$type = $_GET['type'] ?? 'excel';
$stmt = $pdo->prepare("SELECT a.name, a.breed, a.birth_date, a.price, s.sale_date, s.buyer_name FROM animals a LEFT JOIN sales s ON a.id = s.animal_id WHERE a.owner_id = ?");
$stmt->execute([getCurrentUserId()]);
$data = $stmt->fetchAll();

// Используем улучшенную функцию экспорта
exportToExcel($data, 'full_report', ['Кличка','Порода','Дата рождения','Цена','Дата продажи','Покупатель']);
?>