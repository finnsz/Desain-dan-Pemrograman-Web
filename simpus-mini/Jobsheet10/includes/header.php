<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$base = $base ?? './';
$sudahLogin = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'SIMKOS' ?></title>
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<!-- Sidebar Navigation -->
        <aside class="app-sidebar">
            <div class="brand">
                <span>SIMKOS</span>
            </div>
            <nav>
                <ul>
                    <li><a href="<?= $base ?>index.php">Beranda</a></li>

                    <!-- Grup Kamar (Collapsible) -->
                    <li>
                        <details open class="nav-accordion">
                            <summary class="nav-group-title">KAMAR</summary>
                            <ul>
                                <li><a href="<?= $base ?>kamar/list.php">Daftar Kamar</a></li>
                                <?php if ($sudahLogin): ?>
                                <li><a href="<?= $base ?>kamar/tambah.php">Tambah Kamar</a></li>
                                <?php endif; ?>
                            </ul>
                        </details>
                    </li>

                    <!-- Grup Penghuni (Collapsible) - seluruhnya terkunci -->
                    <?php if ($sudahLogin): ?>
                    <li>
                        <details open class="nav-accordion">
                            <summary class="nav-group-title">PENGHUNI</summary>
                            <ul>
                                <li><a href="<?= $base ?>penghuni/list.php">Daftar Penghuni</a></li>
                                <li><a href="<?= $base ?>penghuni/tambah.php">Tambah Penghuni</a></li>
                            </ul>
                        </details>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="app-main">
            <header class="topbar">
                <div class="page-title"><?= $title ?? 'Dashboard' ?></div>
                <div class="user-info">
                    <?php if ($sudahLogin): ?>
                        <?= htmlspecialchars($_SESSION['nama']) ?>
                        &nbsp;|&nbsp;
                        <a href="<?= $base ?>auth/logout.php">Logout</a>
                    <?php else: ?>
                        <a href="<?= $base ?>auth/login.php">Login</a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="content">
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'success' ?>">
                        <?= htmlspecialchars($_SESSION['flash_message']); ?>
                    </div>
                    <?php 
                        unset($_SESSION['flash_message']);
                        unset($_SESSION['flash_type']);
                    ?>
                <?php endif; ?>
