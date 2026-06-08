<?php 
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'];
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        $error = "Заполните все обязательные поля.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный email адрес.";
    } elseif (strlen($password) < 6) {
        $error = "Пароль должен содержать минимум 6 символов.";
    } elseif ($password !== $confirm_password) {
        $error = "Пароли не совпадают.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Пользователь с таким email уже зарегистрирован.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, phone) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashedPassword, $role, $phone])) {
                logAudit("REGISTER", "users", $pdo->lastInsertId());
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Ошибка при регистрации. Попробуйте позже.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация - Племенной питомник</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="register-page">
<div class="container">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 64px;">🐕</div>
        <h1 style="color: #2c5f2d; margin: 10px 0 5px;">Питомник "Немецкая овчарка"</h1>
        <p style="color: #4a6e4a;">Племенная работа, ветеринарный уход, продажа щенков</p>
    </div>
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <h2>Регистрация</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Имя <span style="color:red;">*</span></label>
            <input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

            <label>Email <span style="color:red;">*</span></label>
            <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

            <label>Пароль <span style="color:red;">*</span> (минимум 6 символов)</label>
            <input type="password" name="password" required>

            <label>Подтверждение пароля <span style="color:red;">*</span></label>
            <input type="password" name="confirm_password" required>

            <label>Телефон</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">

            <label>Роль</label>
            <select name="role">
                <option value="buyer" <?= ($_POST['role'] ?? '') == 'buyer' ? 'selected' : '' ?>>Покупатель</option>
                <option value="breeder" <?= ($_POST['role'] ?? '') == 'breeder' ? 'selected' : '' ?>>Заводчик</option>
                <option value="vet" <?= ($_POST['role'] ?? '') == 'vet' ? 'selected' : '' ?>>Ветеринар</option>
            </select>

            <button type="submit">Зарегистрироваться</button>
        </form>
        <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
    </div>
</div>
</body>
</html>