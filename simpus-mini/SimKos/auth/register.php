<?php
// auth/register.php
require_once __DIR__ . '/../includes/session.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

$base = '../';
$title = 'SIMKOS | Registrasi';
include __DIR__ . '/../includes/header.php';
?>

<section class="card-section">
    <h2>Registrasi Petugas</h2>

    <form method="POST" action="proses_register.php" id="form-register">
        <?php echo csrf_field(); ?>
        <p>
            <label for="nama">Nama</label>
            <input type="text" id="nama" name="nama" required>
        </p>
        <p>
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="6">
        </p>
        <p style="margin-top: 1.5rem;">
            <button type="submit" class="btn-primary">Daftar</button>
        </p>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
