<?php
// includes/remember.php
// Fitur "Ingat Saya" memakai pola selector:validator.
// - Cookie berisi "selector:validator" (acak), BUKAN password / user_id.
// - Database hanya menyimpan HASH dari validator, jadi kalau DB bocor
//   token tidak bisa langsung dipakai.
// - Token diganti baru (rotasi) setiap kali dipakai.

const REMEMBER_COOKIE = 'remember_me';
const REMEMBER_DAYS   = 30;

function remember_ensure_table(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS remember_tokens (
        id SERIAL PRIMARY KEY,
        user_id INT NOT NULL,
        selector VARCHAR(32) NOT NULL UNIQUE,
        token_hash VARCHAR(64) NOT NULL,
        expires_at TIMESTAMP NOT NULL
    )");
}

function remember_cookie_options(int $expires): array
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return [
        'expires'  => $expires,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,   // tidak bisa dibaca JavaScript (mengurangi risiko XSS)
        'samesite' => 'Lax',
    ];
}

function remember_clear_cookie(): void
{
    setcookie(REMEMBER_COOKIE, '', remember_cookie_options(time() - 3600));
    unset($_COOKIE[REMEMBER_COOKIE]);
}

// Dipanggil setelah login sukses + checkbox dicentang
function remember_issue(PDO $pdo, int $userId): void
{
    remember_ensure_table($pdo);
    $selector  = bin2hex(random_bytes(9));
    $validator = bin2hex(random_bytes(32));

    $stmt = $pdo->prepare(
        "INSERT INTO remember_tokens (user_id, selector, token_hash, expires_at)
         VALUES (?, ?, ?, NOW() + (? || ' days')::interval)"
    );
    $stmt->execute([$userId, $selector, hash('sha256', $validator), REMEMBER_DAYS]);

    setcookie(
        REMEMBER_COOKIE,
        $selector . ':' . $validator,
        remember_cookie_options(time() + REMEMBER_DAYS * 86400)
    );
}

// Dipanggil otomatis di session.php: login ulang lewat cookie jika session kosong
function remember_check(PDO $pdo): void
{
    if (!empty($_SESSION['user_id']) || empty($_COOKIE[REMEMBER_COOKIE])) {
        return;
    }

    $parts = explode(':', $_COOKIE[REMEMBER_COOKIE], 2);
    if (count($parts) !== 2) {
        remember_clear_cookie();
        return;
    }
    [$selector, $validator] = $parts;

    remember_ensure_table($pdo);
    $stmt = $pdo->prepare(
        "SELECT rt.id, rt.user_id, rt.token_hash, u.nama, u.role
         FROM remember_tokens rt
         JOIN users u ON u.id = rt.user_id
         WHERE rt.selector = ? AND rt.expires_at > NOW()"
    );
    $stmt->execute([$selector]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        remember_clear_cookie();
        return;
    }

    if (!hash_equals($row['token_hash'], hash('sha256', $validator))) {
        // Selector cocok tapi validator salah: kemungkinan token dicuri.
        // Hapus semua token milik user ini.
        $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = ?")->execute([$row['user_id']]);
        remember_clear_cookie();
        return;
    }

    // Valid -> login-kan user, ganti session id, dan rotasi token
    session_regenerate_id(true);
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['nama']    = $row['nama'];
    $_SESSION['role']    = $row['role'];

    $pdo->prepare("DELETE FROM remember_tokens WHERE id = ?")->execute([$row['id']]);
    remember_issue($pdo, (int)$row['user_id']);
}

// Dipanggil saat logout
function remember_forget(PDO $pdo): void
{
    if (!empty($_COOKIE[REMEMBER_COOKIE])) {
        $selector = explode(':', $_COOKIE[REMEMBER_COOKIE], 2)[0];
        remember_ensure_table($pdo);
        $pdo->prepare("DELETE FROM remember_tokens WHERE selector = ?")->execute([$selector]);
        remember_clear_cookie();
    }
}
