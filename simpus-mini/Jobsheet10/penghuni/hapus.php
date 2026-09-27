<?php
require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM penghuni WHERE id = :id");
    $stmt->execute(['id' => $_POST['id']]);
    header('Location: list.php');
}
?>