-- Создание базы данных
CREATE DATABASE IF NOT EXISTS breeding_db;
USE breeding_db;

-- Таблица пользователей
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('breeder', 'vet', 'buyer', 'admin') DEFAULT 'buyer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблица животных
CREATE TABLE animals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    species VARCHAR(50) NOT NULL,
    breed VARCHAR(100),
    gender ENUM('male','female') NOT NULL,
    color VARCHAR(50),
    birth_date DATE,
    chip_number VARCHAR(50) UNIQUE,
    reg_number VARCHAR(50),
    father_id INT DEFAULT NULL,
    mother_id INT DEFAULT NULL,
    status ENUM('available','sold','dead','reserved') DEFAULT 'available',
    description TEXT,
    price DECIMAL(10,2),
    delivery_terms TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (father_id) REFERENCES animals(id) ON DELETE SET NULL,
    FOREIGN KEY (mother_id) REFERENCES animals(id) ON DELETE SET NULL
);

-- Достижения (выставки, тесты)
CREATE TABLE animal_achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    achievement_type VARCHAR(100),
    title VARCHAR(200),
    date DATE,
    place VARCHAR(200),
    result VARCHAR(100),
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

-- Ветеринарные записи
CREATE TABLE veterinary_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    visit_date DATE NOT NULL,
    record_type VARCHAR(100),
    description TEXT,
    vet_name VARCHAR(100),
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

-- Медицинские документы
CREATE TABLE medical_documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vet_record_id INT NOT NULL,
    file_name VARCHAR(255),
    file_path VARCHAR(500),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vet_record_id) REFERENCES veterinary_records(id) ON DELETE CASCADE
);

-- Генетические тесты
CREATE TABLE genetic_tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    test_name VARCHAR(150),
    test_date DATE,
    result TEXT,
    laboratory VARCHAR(100),
    document_path VARCHAR(500),
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

-- Бонитировка
CREATE TABLE bonitization (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    assessment_date DATE,
    score INT,
    expert_name VARCHAR(100),
    notes TEXT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

-- Планирование вязок
CREATE TABLE breedings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    female_id INT NOT NULL,
    male_id INT NOT NULL,
    planned_date DATE,
    actual_date DATE,
    status ENUM('planned','completed','cancelled') DEFAULT 'planned',
    notes TEXT,
    created_by INT,
    FOREIGN KEY (female_id) REFERENCES animals(id),
    FOREIGN KEY (male_id) REFERENCES animals(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Помёты
CREATE TABLE litters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    breeding_id INT,
    mother_id INT NOT NULL,
    father_id INT NOT NULL,
    birth_date DATE,
    puppies_count INT,
    notes TEXT,
    FOREIGN KEY (breeding_id) REFERENCES breedings(id),
    FOREIGN KEY (mother_id) REFERENCES animals(id),
    FOREIGN KEY (father_id) REFERENCES animals(id)
);

-- Продажи
CREATE TABLE sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    buyer_id INT,
    buyer_name VARCHAR(150),
    buyer_contact VARCHAR(100),
    sale_date DATE,
    price DECIMAL(10,2),
    contract_number VARCHAR(50),
    destination_info TEXT,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Фотографии
CREATE TABLE animal_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    animal_id INT NOT NULL,
    photo_path VARCHAR(500),
    is_main BOOLEAN DEFAULT FALSE,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (animal_id) REFERENCES animals(id) ON DELETE CASCADE
);

-- Подписки
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    animal_species VARCHAR(50),
    breed VARCHAR(100),
    active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Блог
CREATE TABLE blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    content TEXT,
    author_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- FAQ
CREATE TABLE faq (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    answer TEXT,
    category VARCHAR(100),
    sort_order INT DEFAULT 0
);

-- Обратная связь
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100),
    email VARCHAR(100),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new','read','replied') DEFAULT 'new',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Аудит
CREATE TABLE audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    table_name VARCHAR(100),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Интеграции API
CREATE TABLE api_integrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    api_name VARCHAR(100),
    request_data TEXT,
    response_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ========== ТЕСТОВЫЕ ДАННЫЕ ==========
INSERT INTO users (name, email, password, phone, role) VALUES 
('Иван Петров', 'ivan@breeder.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+79991234567', 'breeder'),
('Анна Вет', 'anna@vet.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+79997654321', 'vet'),
('Покупатель', 'buyer@mail.ru', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'buyer');
-- Пароли: 123456

-- Животные 1-е поколение (бабушки/дедушки)
INSERT INTO animals (owner_id, name, species, breed, gender, color, birth_date, chip_number, reg_number, father_id, mother_id, status, price) VALUES
(1, 'Барон', 'Собака', 'Немецкая овчарка', 'male', 'чёрно-подпалый', '2010-03-10', '100001', 'RKF-001', NULL, NULL, 'dead', 0),
(1, 'Лада', 'Собака', 'Немецкая овчарка', 'female', 'чепрачный', '2011-07-22', '100002', 'RKF-002', NULL, NULL, 'dead', 0);

-- Животные 2-е поколение (родители)
INSERT INTO animals (owner_id, name, species, breed, gender, color, birth_date, chip_number, reg_number, father_id, mother_id, status, price) VALUES
(1, 'Рекс', 'Собака', 'Немецкая овчарка', 'male', 'чёрный', '2015-05-10', '200001', 'RKF-010', 1, 2, 'available', 50000),
(1, 'Грета', 'Собака', 'Немецкая овчарка', 'female', 'серый', '2016-02-14', '200002', 'RKF-011', 1, 2, 'available', 45000);

-- Животные 3-е поколение (текущие)
INSERT INTO animals (owner_id, name, species, breed, gender, color, birth_date, chip_number, reg_number, father_id, mother_id, status, price, description) VALUES
(1, 'Альфа', 'Собака', 'Немецкая овчарка', 'male', 'чёрный', '2020-03-15', '300001', 'RKF-100', 3, 4, 'available', 70000, 'Отличный экстерьер, чемпион области'),
(1, 'Бетти', 'Собака', 'Немецкая овчарка', 'female', 'чепрачный', '2020-03-15', '300002', 'RKF-101', 3, 4, 'sold', 75000, 'Добрая, контактная');

-- Фотографии
INSERT INTO animal_photos (animal_id, photo_path, is_main) VALUES (5, 'assets/uploads/alfa.jpg', 1), (6, 'assets/uploads/betty.jpg', 1);

-- Ветеринарные записи
INSERT INTO veterinary_records (animal_id, visit_date, record_type, description, vet_name) VALUES
(5, '2020-05-10', 'Вакцинация', 'DHPPI + бешенство', 'Д-р Смирнов'),
(5, '2021-02-20', 'Генетический тест', 'HD - свободен', 'Лаборатория Биотест');

-- Генетические тесты
INSERT INTO genetic_tests (animal_id, test_name, test_date, result) VALUES (5, 'Дисплазия ТБС', '2021-02-20', 'Свободен');

-- Бонитировка
INSERT INTO bonitization (animal_id, assessment_date, score, expert_name, notes) VALUES (5, '2022-05-01', 92, 'Эксперт Иванов', 'Отличная голова, крепкий костяк');

-- Вязка и помёт
INSERT INTO breedings (female_id, male_id, planned_date, actual_date, status, notes) VALUES (4, 3, '2020-01-10', '2020-01-12', 'completed', 'Удачная вязка');
INSERT INTO litters (breeding_id, mother_id, father_id, birth_date, puppies_count, notes) VALUES (1, 4, 3, '2020-03-15', 4, 'Все щенки здоровы');
INSERT INTO sales (animal_id, buyer_name, sale_date, price, destination_info) VALUES (6, 'ООО Кинолог', '2020-08-10', 75000, 'Москва');

-- FAQ
INSERT INTO faq (question, answer, category) VALUES
('Как зарегистрировать помёт?', 'Зайдите в раздел "Планирование вязок" и создайте запись о вязке, затем добавьте помёт.', 'Племенная работа'),
('Какие прививки обязательны?', 'DHPPI и бешенство. Рекомендуем также бордетеллёз.', 'Ветеринария');

-- Блог
INSERT INTO blog_posts (title, content, author_id) VALUES
('Как выбрать щенка немецкой овчарки', 'Советы по выбору здорового и перспективного щенка...', 1);
