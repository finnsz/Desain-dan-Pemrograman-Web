<?php
// auth/login.php
require_once __DIR__ . '/../includes/session.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$base = '../';
$title = 'SIMKOS | Login';
include __DIR__ . '/../includes/header.php';
?>

<section class="card-section">
    <h2>Login Petugas</h2>

    <form method="POST" action="proses_login.php" id="form-login">
        <p>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Masuk</button>
        </p>
    </form>
    <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
