<?php require_once 'config/db.php';
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $msg = $_POST['message'];
    $stmt = $pdo->prepare("INSERT INTO feedback (name, email, message) VALUES (?,?,?)");
    $stmt->execute([$name, $email, $msg]);
    echo "<div class='alert alert-success'>Сообщение отправлено!</div>";
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Обратная связь</title>
        <link rel="stylesheet" href="assets/css/style.css">
    </head>
<body>
<?php include 'nav.inc.php'; ?>
    <div class="container">
        <h1>Напишите нам</h1>
        <div class="card">
            <form method="post">
                <label>Имя</label>
                <input name="name" required>
                <label>Email</label>
                <input type="email" name="email" required>
                <label>Сообщение</label>
                <textarea name="message" required>
                </textarea>
                <button>Отправить</button>
            </form>
        </div>
    </div>
<?php include 'footer.inc.php'; ?>
</body>
</html>