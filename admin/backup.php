<?php
require_once '../config/db.php';
if(!isAdmin()) die("Доступ только администратору");
$backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
$command = "mysqldump --user={$username} --password={$password} --host={$host} {$dbname} > {$backup_file}";
system($command, $output);
if(file_exists($backup_file)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$backup_file);
    readfile($backup_file);
    unlink($backup_file);
} else {
    echo "Ошибка создания резервной копии";
}
?>