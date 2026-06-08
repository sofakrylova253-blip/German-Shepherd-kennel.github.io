<?php
session_start();

$host = 'localhost';
$dbname = 'breeding_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}

// === РОЛЕВЫЕ ФУНКЦИИ ===
function isLoggedIn() { return isset($_SESSION['user_id']); }
function getCurrentUserId() { return $_SESSION['user_id'] ?? 0; }
function getCurrentUserRole() { return $_SESSION['role'] ?? 'guest'; }
function isAdmin() { return getCurrentUserRole() === 'admin'; }
function isBreeder() { return in_array(getCurrentUserRole(), ['breeder','admin']); }
function isVet() { return in_array(getCurrentUserRole(), ['vet','admin']); }
function isBuyer() { return in_array(getCurrentUserRole(), ['buyer','admin']); }
function canEditAnimal($animal_owner_id) {
    return isAdmin() || (isBreeder() && $animal_owner_id == getCurrentUserId());
}
function canViewMedical() { return isVet() || isAdmin() || isBreeder(); }

// === АУДИТ С ПОДДЕРЖКОЙ NULL ===
function logAudit($action, $table_name, $record_id, $old_values = null, $new_values = null) {
    global $pdo;
    $user_id = getCurrentUserId();
    if ($user_id == 0) $user_id = null;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $stmt = $pdo->prepare("INSERT INTO audit_log (user_id, action, table_name, record_id, old_values, new_values, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $action, $table_name, $record_id, $old_values, $new_values, $ip]);
}

// === ЗАГРУЗКА ФАЙЛОВ ===
function uploadFile($file, $target_dir = "assets/uploads/") {
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    $filename = time() . '_' . basename($file['name']);
    $target = $target_dir . $filename;
    if (move_uploaded_file($file['tmp_name'], $target)) return $target;
    return null;
}

// === API ХОРРИОТ (симуляция) ===
function checkChipWithHorriot($chip_number) {
    if (preg_match('/^999/', $chip_number)) {
        return ['valid' => false, 'message' => 'Чип уже зарегистрирован'];
    }
    return ['valid' => true, 'message' => 'Чип доступен'];
}

// === ОПОВЕЩЕНИЕ ПОДПИСЧИКОВ ===
function notifySubscribers($species, $breed, $message) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.email FROM subscriptions s JOIN users u ON s.user_id = u.id WHERE s.active = 1 AND (s.animal_species = ? OR s.breed = ?)");
    $stmt->execute([$species, $breed]);
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($emails as $email) {
        mail($email, "Новое потомство!", $message);
    }
}

// === РОДОСЛОВНАЯ (рекурсивная, 3 поколения) ===
function getAncestors($pdo, $animal_id, $generation = 1, $max_gen = 3) {
    if ($generation > $max_gen) return null;
    $stmt = $pdo->prepare("SELECT id, name, breed, reg_number, father_id, mother_id FROM animals WHERE id = ?");
    $stmt->execute([$animal_id]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$animal) return null;
    $result = [
        'id' => $animal['id'],
        'name' => $animal['name'],
        'breed' => $animal['breed'],
        'reg_number' => $animal['reg_number'],
        'generation' => $generation
    ];
    $result['father'] = $animal['father_id'] ? getAncestors($pdo, $animal['father_id'], $generation+1, $max_gen) : null;
    $result['mother'] = $animal['mother_id'] ? getAncestors($pdo, $animal['mother_id'], $generation+1, $max_gen) : null;
    return $result;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function escape($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// === ЭКСПОРТ В EXCEL (CSV с BOM и разделителем ;) ===
function exportToExcel($data, $filename, $headers) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '_' . date('Y-m-d') . '.csv"');
    $output = fopen('php://output', 'w');
    // BOM для корректной работы кириллицы в Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    // Разделитель ; (точка с запятой)
    fputcsv($output, $headers, ';');
    foreach ($data as $row) {
        // Преобразуем HTML-сущности в обычные символы
        $row = array_map(function($v) {
            return html_entity_decode($v, ENT_QUOTES, 'UTF-8');
        }, $row);
        fputcsv($output, $row, ';');
    }
    fclose($output);
    exit;
}
?>
