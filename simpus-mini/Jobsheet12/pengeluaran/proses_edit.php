<?php
require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$kategori = isset($_POST['kategori']) ? $_POST['kategori'] : '';
$nominal = isset($_POST['nominal']) ? (float)$_POST['nominal'] : 0;
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : '';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

if (!$id) {
    $_SESSION['flash_message'] = 'ID tidak valid.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

$errors = [];
if (!$kategori) $errors[] = 'Kategori harus dipilih';
if (!$nominal || $nominal <= 0) $errors[] = 'Nominal harus lebih besar dari 0';
if (!$tanggal) $errors[] = 'Tanggal harus diisi';
if (!$keterangan) $errors[] = 'Keterangan harus diisi';

if (!empty($errors)) {
    $_SESSION['flash_message'] = implode('; ', $errors);
    $_SESSION['flash_type'] = 'danger';
    header("Location: edit.php?id=$id");
    exit;
}

// Ambil bukti lama
$old = $pdo->prepare("SELECT bukti_path FROM pengeluaran WHERE id = :id");
$old->execute([':id' => $id]);
$old_bukti = $old->fetchColumn();

// Handle upload bukti baru (optional)
$bukti_path = $old_bukti;
if (isset($_FILES['bukti_path']) && $_FILES['bukti_path']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../assets/bukti_pengeluaran/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $file_ext = strtolower(pathinfo($_FILES['bukti_path']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_ext)) {
        $_SESSION['flash_message'] = 'Format file tidak diizinkan.';
        $_SESSION['flash_type'] = 'danger';
        header("Location: edit.php?id=$id");
        exit;
    }

    if ($_FILES['bukti_path']['size'] > 2 * 1024 * 1024) {
        $_SESSION['flash_message'] = 'Ukuran file terlalu besar (max 2MB).';
        $_SESSION['flash_type'] = 'danger';
        header("Location: edit.php?id=$id");
        exit;
    }

    $file_new_name = 'pengeluaran_' . $id . '_' . time() . '.' . $file_ext;
    $file_path = $upload_dir . $file_new_name;

    if (move_uploaded_file($_FILES['bukti_path']['tmp_name'], $file_path)) {
        if ($old_bukti && file_exists(__DIR__ . '/../' . $old_bukti)) {
            unlink(__DIR__ . '/../' . $old_bukti);
        }
        $bukti_path = 'assets/bukti_pengeluaran/' . $file_new_name;
    }
}

try {
    $stmt = $pdo->prepare("
        UPDATE pengeluaran SET
            kategori = :kategori,
            nominal = :nominal,
            tanggal = :tanggal,
            keterangan = :keterangan,
            bukti_path = :bukti_path
        WHERE id = :id
    ");

    $stmt->execute([
        ':kategori' => $kategori,
        ':nominal' => $nominal,
        ':tanggal' => $tanggal,
        ':keterangan' => $keterangan,
        ':bukti_path' => $bukti_path,
        ':id' => $id
    ]);

    $_SESSION['flash_message'] = 'Pengeluaran berhasil diupdate!';
    $_SESSION['flash_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'danger';
}

header('Location: list.php');
exit;
?>
