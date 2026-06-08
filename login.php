<?php 
require_once 'config/db.php';

$error = '';
$success = '';

if (isset($_GET['registered'])) {
    $success = "Регистрация прошла успешно! Теперь вы можете войти.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Заполните все поля.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            logAudit("LOGIN", "users", $user['id']);
            redirect('index.php');
        } else {
            $error = "Неверный email или пароль.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход - Племенной питомник</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 64px;">🐕</div>
        <h1 style="color: #2c5f2d; margin: 10px 0 5px;">Питомник "Немецкая овчарка"</h1>
        <p style="color: #4a6e4a;">Племенная работа, ветеринарный уход, продажа щенков</p>
    </div>
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <h2>Авторизация</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" required placeholder="example@mail.ru" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label>Пароль:</label>
            <input type="password" name="password" required placeholder="Введите пароль">

            <button type="submit">Войти</button>
        </form>
        <p style="margin-top: 15px;">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
    </div>
</div>
</body>
</html>