<?php
// auth/proses_login.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); // cegah session fixation
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];
    if (!empty($_POST['remember'])) {
        require_once __DIR__ . '/../includes/remember.php';
        remember_issue($pdo, (int)$user['id']);
    }
    header('Location: ../index.php');
    exit;
}

$_SESSION['flash_message'] = 'Username atau password salah.';
$_SESSION['flash_type']    = 'danger';
header('Location: login.php');
exit;
