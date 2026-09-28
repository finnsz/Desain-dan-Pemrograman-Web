<?php
// auth/logout.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/remember.php';
remember_forget($pdo);
session_destroy();
header('Location: login.php');
exit;
