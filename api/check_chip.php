<?php
header('Content-Type: application/json');
require_once '../config/db.php';
$chip = $_GET['chip'] ?? '';
if(!$chip) { echo json_encode(['error'=>'no chip']); exit; }
$result = checkChipWithHorriot($chip);
echo json_encode($result);
?>
