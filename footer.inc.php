<footer>
    <div class="container footer-container">
        <div class="footer-section">
            <h3>🐕 Питомник "Немецкая овчарка"</h3>
            <p>Племенная работа, ветеринарный уход, продажа щенков</p>
            <p>&copy; <?= date('Y') ?> Все права защищены.</p>
        </div>
        <div class="footer-section">
            <h3>Контакты</h3>
            <p>📍 Адрес: г. Москва, ул. Лесная, д. 15</p>
            <p>📞 Телефон: <a href="tel:+74951234567">+7 (495) 123-45-67</a></p>
            <p>✉️ Email: <a href="mailto:info@breeder.ru">info@breeder.ru</a></p>
        </div>
        <div class="footer-section">
            <h3>Социальные сети</h3>
            <div class="social-links">
                <a href="https://vk.com/yourpage" target="_blank" class="social-icon"><i class="fab fa-vk"></i> ВКонтакте</a>
                <a href="https://t.me/yourchannel" target="_blank" class="social-icon"><i class="fab fa-telegram"></i> Telegram</a>
                <a href="https://www.instagram.com/yourpage" target="_blank" class="social-icon"><i class="fab fa-instagram"></i> Instagram</a>
                <a href="https://www.youtube.com/yourchannel" target="_blank" class="social-icon"><i class="fab fa-youtube"></i> YouTube</a>
            </div>
        </div>
        <div class="footer-section">
            <h3>Разделы сайта</h3>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="catalog.php">Каталог</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="feedback.php">Контакты</a></li>
                <?php if(isLoggedIn() && isBuyer()): ?>
                    <li><a href="subscribe.php">Подписка</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</footer>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">