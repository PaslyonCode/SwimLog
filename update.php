<?php
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=your_db_name;charset=utf8mb4', 'your_user', 'your_pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$data = json_decode(file_get_contents("php://input"), true);

$id = (int)$data['id'];
$field = $data['field'];
$value = $data['value'];

$allowed = ['date','distance','duration','description','gear','location'];

if (!in_array($field, $allowed)) {
    echo json_encode(['success'=>false,'error'=>'Недопустимое поле']);
    exit;
}

$stmt = $pdo->prepare("UPDATE runs SET `$field` = ? WHERE id = ?");
$ok = $stmt->execute([$value, $id]);

echo json_encode(['success'=>$ok]);
