<?php
/**
 * includes/csrf.php
 * Fungsi untuk CSRF token generation dan verification.
 *
 * Pastikan session_start() sudah dipanggil sebelum include file ini.
 */

/**
 * Generate atau return existing CSRF token.
 * Token dibuat sekali per sesi dan disimpan di $_SESSION['csrf_token'].
 *
 * @return string CSRF token (64 karakter heksadesimal)
 */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Generate HTML untuk hidden input CSRF token.
 * Digunakan di dalam form POST.
 *
 * @return string HTML <input type="hidden" name="csrf_token" value="...">
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verify CSRF token dari $_POST.
 * Jika token tidak valid atau tidak ada, exit dengan HTTP 403.
 *
 * @return void
 */
function csrf_verify()
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa.');
    }
}
