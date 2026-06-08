<?php
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/config/db.php';
}
?>
<nav>
    <div class="container nav-links">
        <!-- Название и логотип -->
        <div style="display: flex; align-items: center; gap: 15px;">
            <span style="font-size: 1.3rem; font-weight: bold; color: #f0f7e6;">🐾 Питомник "Немецкая овчарка"</span>
        </div>
        <div style="display: flex; flex-wrap: wrap;">
            <a href="index.php">Главная</a>
            <a href="catalog.php">Каталог</a>
            <a href="faq.php">FAQ</a>
            <a href="feedback.php">Контакты</a>
            <?php if (isLoggedIn()): ?>
                <?php $role = getCurrentUserRole(); $userName = $_SESSION['user_name']; ?>
                <?php if ($role === 'admin'): ?>
                    <a href="breeding_calendar.php">Вязки и помёты</a>
                    <a href="vet_module.php">Ветеринария</a>
                    <a href="dashboard.php">Дашборд</a>
                    <a href="admin/backup.php">Резервное копирование</a>
                    <a href="admin/audit_log.php">Журнал аудита</a>
                <?php elseif ($role === 'breeder'): ?>
                    <a href="breeding_calendar.php">Вязки и помёты</a>
                    <a href="vet_module.php">Ветеринария</a>
                    <a href="dashboard.php">Дашборд</a>
                    <a href="export_report.php?type=excel">Экспорт отчётов</a>
                <?php elseif ($role === 'vet'): ?>
                    <a href="vet_module.php">Ветеринария</a>
                    <a href="dashboard.php">Статистика здоровья</a>
                <?php elseif ($role === 'buyer'): ?>
                    <a href="subscribe.php">Подписка на новости</a>
                <?php endif; ?>
                <span>Привет, <?= escape($userName) ?> (<?= $role ?>)</span>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Вход</a>
                <a href="register.php">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</nav>