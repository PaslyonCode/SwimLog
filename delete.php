<?php
header('Content-Type: application/json');

$pdo = new PDO('mysql:host=localhost;dbname=your_db_name;charset=utf8mb4', 'your_user', 'your_pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$data = json_decode(file_get_contents("php://input"), true);

$id = (int)$data['id'];

$stmt = $pdo->prepare("DELETE FROM runs WHERE id = ?");
$ok = $stmt->execute([$id]);

echo json_encode(['success'=>$ok]);
