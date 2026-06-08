<?php require_once 'config/db.php';
if(!canViewMedical()) die("Доступ запрещён");
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file_path = uploadFile($_FILES['document']);
    $stmt = $pdo->prepare("INSERT INTO medical_documents (vet_record_id, file_name, file_path) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['vet_record_id'], $_FILES['document']['name'], $file_path]);
    logAudit("ADD_MED_DOC", "medical_documents", $pdo->lastInsertId());
}
$stmt = $pdo->query("SELECT vr.*, a.name as animal_name FROM veterinary_records vr JOIN animals a ON vr.animal_id = a.id ORDER BY vr.visit_date DESC");
$records = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Ветеринария</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
    <?php include 'nav.inc.php'; ?>
    <div class="container">
        <h1>Ветеринарный журнал</h1>
        <div class="card">
            <table><tr>
                <th>Дата</th>
                <th>Животное</th>
                <th>Тип</th>
                <th>Описание</th>
                <th>Ветеринар</th>
                <th>Документы</th>
            </tr>
            <?php foreach($records as $r): ?>
            <tr>
                <td><?= $r['visit_date'] ?></td>
                <td><?= escape($r['animal_name']) ?></td>
                <td><?= escape($r['record_type']) ?></td>
                <td><?= nl2br(escape($r['description'])) ?></td>
                <td><?= escape($r['vet_name']) ?></td>
                <td>
                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="vet_record_id" value="<?= $r['id'] ?>">
                        <input type="file" name="document" required>
                        <button type="submit">Загрузить</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include 'footer.inc.php'; ?>
</body>
</html>