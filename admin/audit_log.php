<?php require_once '../config/db.php';
if(!isAdmin()) die("Доступ только администратору");
$logs = $pdo->query("SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 100")->fetchAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Аудит</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
<body>
    <div class="container">
        <h1>Журнал аудита</h1>
        <div class="card">
            <table border=1>
            <tr>
                <th>Дата</th>
                <th>Пользователь</th>
                <th>Действие</th>
                <th>Таблица</th>
                <th>ID записи</th>
                <th>IP</th>
            </tr>
<?php foreach($logs as $log): ?>
    <tr>
        <td><?= $log['created_at'] ?></td>
        <td><?= $log['user_id'] ?></td>
        <td><?= escape($log['action']) ?></td>
        <td><?= $log['table_name'] ?></td>
        <td><?= $log['record_id'] ?></td>
        <td><?= $log['ip_address'] ?></td>
    </tr>
    <?php endforeach; ?>
            </table>
</div>
</div>
<?php include 'footer.inc.php'; ?>
</body>
</html>
