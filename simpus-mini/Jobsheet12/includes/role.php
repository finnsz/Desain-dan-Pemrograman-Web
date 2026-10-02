<?php
// includes/role.php
// Helper kontrol akses berbasis role. Di-include SETELAH includes/auth.php
// (yang sudah memastikan user login & session aktif).

function is_admin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}

// Hentikan halaman jika user bukan admin, lalu kembalikan ke halaman lain.
function require_admin(string $redirect = 'list.php'): void
{
    if (!is_admin()) {
        $_SESSION['flash_message'] = 'Akses ditolak: hanya admin yang boleh menghapus data.';
        $_SESSION['flash_type']    = 'danger';
        header('Location: ' . $redirect);
        exit;
    }
}
