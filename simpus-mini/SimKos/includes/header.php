<?php
require_once __DIR__ . '/session.php';
$base = $base ?? './';
$sudahLogin = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'SIMKOS' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?>assets/css/style.css">
</head>
<body>
<!-- Sidebar Navigation -->
        <aside class="app-sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="brand">
                    <i class="fas fa-building"></i>
                    <span class="brand-text">SIMKOS</span>
                </div>
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-chevron-left"></i>
                </button>
            </div>

            <nav>
                <ul>
                    <li>
                        <a href="<?= $base ?>index.php" class="nav-link">
                            <i class="fas fa-th-large"></i>
                            <span class="nav-text">Dashboard</span>
                        </a>
                    </li>

                    <!-- Grup Kamar -->
                    <li>
                        <details open class="nav-accordion">
                            <summary class="nav-group-title">
                                <div>
                                    <i class="fas fa-door-open"></i>
                                    <span class="nav-text">KAMAR</span>
                                </div>
                            </summary>
                            <ul>
                                <li><a href="<?= $base ?>kamar/list.php"><i class="fas fa-list"></i><span class="nav-text">Daftar Kamar</span></a></li>
                                <?php if ($sudahLogin): ?>
                                <li><a href="<?= $base ?>kamar/tambah.php"><i class="fas fa-plus"></i><span class="nav-text">Tambah Kamar</span></a></li>
                                <?php endif; ?>
                            </ul>
                        </details>
                    </li>

                    <!-- Grup Penghuni -->
                    <?php if ($sudahLogin): ?>
                    <li>
                        <details open class="nav-accordion">
                            <summary class="nav-group-title">
                                <div>
                                    <i class="fas fa-users"></i>
                                    <span class="nav-text">PENGHUNI</span>
                                </div>
                            </summary>
                            <ul>
                                <li><a href="<?= $base ?>penghuni/list.php"><i class="fas fa-list"></i><span class="nav-text">Daftar Penghuni</span></a></li>
                                <li><a href="<?= $base ?>penghuni/tambah.php"><i class="fas fa-user-plus"></i><span class="nav-text">Tambah Penghuni</span></a></li>
                            </ul>
                        </details>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= $base ?>auth/<?= $sudahLogin ? 'logout.php' : 'login.php' ?>" class="nav-link">
                    <i class="fas fa-<?= $sudahLogin ? 'sign-out-alt' : 'sign-in-alt' ?>"></i>
                    <span class="nav-text"><?= $sudahLogin ? 'Logout' : 'Login' ?></span>
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="app-main">
            <header class="topbar">
                <div class="page-title"><?= $title ?? 'Dashboard' ?></div>
                <div class="user-info">
                    <?php if ($sudahLogin): ?>
                        <i class="fas fa-user-circle" style="font-size: 1.1rem;"></i>
                        <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    const appMain = document.querySelector('.app-main');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        appMain.classList.toggle('sidebar-collapsed');

        // Rotate icon
        const icon = toggleBtn.querySelector('i');
        if (sidebar.classList.contains('collapsed')) {
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
        } else {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
        }
    });
});
</script>
