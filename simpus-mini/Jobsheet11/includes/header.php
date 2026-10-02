<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';
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
    <script>
        // Apply sidebar state sebelum render (cegah FOUC)
        (function() {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>
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
                        <details class="nav-accordion">
                            <summary class="nav-group-title" onclick="return false;">
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
                        <details class="nav-accordion">
                            <summary class="nav-group-title" onclick="return false;">
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

                    <!-- Grup Keuangan -->
                    <li>
                        <details class="nav-accordion">
                            <summary class="nav-group-title" onclick="return false;">
                                <div>
                                    <i class="fas fa-money-bill-wave"></i>
                                    <span class="nav-text">KEUANGAN</span>
                                </div>
                            </summary>
                            <ul>
                                <li><a href="<?= $base ?>pembayaran/list.php"><i class="fas fa-receipt"></i><span class="nav-text">Pembayaran Sewa</span></a></li>
                                <li><a href="<?= $base ?>pembayaran/generate.php"><i class="fas fa-magic"></i><span class="nav-text">Generate Tagihan</span></a></li>
                                <li><a href="<?= $base ?>pengeluaran/list.php"><i class="fas fa-shopping-cart"></i><span class="nav-text">Pengeluaran</span></a></li>
                                <li><a href="<?= $base ?>laporan/bulanan.php"><i class="fas fa-chart-line"></i><span class="nav-text">Laporan Bulanan</span></a></li>
                            </ul>
                        </details>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= $base ?>../index.php" class="nav-link">
                    <i class="fas fa-arrow-left"></i>
                    <span class="nav-text">Daftar Jobsheet</span>
                </a>
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
                        <span><?php echo e($_SESSION['nama']); ?></span>
                    <?php endif; ?>
                </div>
            </header>

            <main class="content">
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-<?= $_SESSION['flash_type'] ?? 'success' ?>">
                        <?php echo e($_SESSION['flash_message']); ?>
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

    // Restore sidebar state from localStorage
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        appMain.classList.add('sidebar-collapsed');
        document.documentElement.classList.add('sidebar-is-collapsed');
        const icon = toggleBtn.querySelector('i');
        icon.classList.remove('fa-chevron-left');
        icon.classList.add('fa-chevron-right');
    }

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        appMain.classList.toggle('sidebar-collapsed');
        document.documentElement.classList.toggle('sidebar-is-collapsed');

        const icon = toggleBtn.querySelector('i');
        if (sidebar.classList.contains('collapsed')) {
            icon.classList.remove('fa-chevron-left');
            icon.classList.add('fa-chevron-right');
            localStorage.setItem('sidebarCollapsed', 'true');
        } else {
            icon.classList.remove('fa-chevron-right');
            icon.classList.add('fa-chevron-left');
            localStorage.setItem('sidebarCollapsed', 'false');

            // Close all popups when expanding
            document.querySelectorAll('.sidebar-popup-menu').forEach(menu => {
                menu.remove();
            });
        }
    });

    // Auto-expand accordion berdasarkan halaman saat ini
    function detectAndExpandAccordion() {
        const currentUrl = window.location.pathname;
        const accordions = document.querySelectorAll('.nav-accordion');

        accordions.forEach(accordion => {
            const links = accordion.querySelectorAll('a');
            let shouldOpen = false;

            links.forEach(link => {
                const href = link.getAttribute('href');
                // Cek apakah link cocok dengan halaman saat ini
                if (href && currentUrl.includes(href.replace(/^\.\.\//g, '').replace(/^\.\//g, ''))) {
                    shouldOpen = true;
                }
            });

            if (shouldOpen) {
                accordion.setAttribute('open', '');
                localStorage.setItem('accordionOpen_' + accordion.className, 'true');
            } else {
                accordion.removeAttribute('open');
                localStorage.removeItem('accordionOpen_' + accordion.className);
            }
        });
    }

    // Restore accordion state dari localStorage
    function restoreAccordionState() {
        const accordions = document.querySelectorAll('.nav-accordion');
        accordions.forEach(accordion => {
            const key = 'accordionOpen_' + accordion.className;
            if (localStorage.getItem(key) === 'true') {
                accordion.setAttribute('open', '');
            } else {
                accordion.removeAttribute('open');
            }
        });
    }

    // Jalankan deteksi accordion
    restoreAccordionState();
    detectAndExpandAccordion();

    // Handle accordion clicks
    sidebar.addEventListener('click', function(e) {
        const summary = e.target.closest('.nav-accordion summary');
        if (!summary) return;

        const accordion = summary.parentElement;

        if (sidebar.classList.contains('collapsed')) {
            // Collapsed state - show popup
            e.preventDefault();
            e.stopPropagation();

            // Close other popups
            document.querySelectorAll('.sidebar-popup-menu').forEach(menu => {
                menu.remove();
            });

            const ul = accordion.querySelector('ul');
            if (!ul) return;

            // Create popup dan append ke body
            const popup = document.createElement('div');
            popup.className = 'sidebar-popup-menu show';
            popup.dataset.accordionId = accordion.className;

            const newUl = ul.cloneNode(true);
            popup.appendChild(newUl);
            document.body.appendChild(popup);

            const rect = summary.getBoundingClientRect();
            popup.style.top = rect.top + 'px';
        } else {
            // Expanded state - toggle details normally
            if (accordion.hasAttribute('open')) {
                accordion.removeAttribute('open');
                localStorage.removeItem('accordionOpen_' + accordion.className);
            } else {
                accordion.setAttribute('open', '');
                localStorage.setItem('accordionOpen_' + accordion.className, 'true');
            }
        }
    });

    // Close popup when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.app-sidebar') && !e.target.closest('.sidebar-popup-menu')) {
            document.querySelectorAll('.sidebar-popup-menu').forEach(menu => {
                menu.remove();
            });
        }
    });
});
</script>
