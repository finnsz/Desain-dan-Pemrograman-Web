<?php
require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nominal = isset($_POST['nominal']) ? (float)$_POST['nominal'] : 0;
$tgl_jatuh_tempo = isset($_POST['tgl_jatuh_tempo']) ? $_POST['tgl_jatuh_tempo'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'BELUM BAYAR';
$tgl_bayar = isset($_POST['tgl_bayar']) && $_POST['tgl_bayar'] !== '' ? $_POST['tgl_bayar'] : null;
$metode_bayar = isset($_POST['metode_bayar']) && $_POST['metode_bayar'] !== '' ? $_POST['metode_bayar'] : null;
$denda = isset($_POST['denda']) ? (float)$_POST['denda'] : 0;
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : null;

if (!$id) {
    $_SESSION['flash_message'] = 'ID tidak valid.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

$errors = [];
if (!$nominal || $nominal <= 0) $errors[] = 'Nominal harus lebih besar dari 0';
if (!$tgl_jatuh_tempo) $errors[] = 'Tanggal jatuh tempo harus diisi';
if ($status === 'LUNAS') {
    if (!$tgl_bayar) $errors[] = 'Tanggal pembayaran harus diisi';
    if (!$metode_bayar) $errors[] = 'Metode pembayaran harus dipilih';
}

if (!empty($errors)) {
    $_SESSION['flash_message'] = implode('; ', $errors);
    $_SESSION['flash_type'] = 'danger';
    header("Location: edit.php?id=$id");
    exit;
}

// Ambil bukti lama
$old = $pdo->prepare("SELECT bukti_path FROM pembayaran WHERE id = :id");
$old->execute([':id' => $id]);
$old_bukti = $old->fetchColumn();

// Handle upload bukti baru (optional)
$bukti_path = $old_bukti;
if (isset($_FILES['bukti_path']) && $_FILES['bukti_path']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../assets/bukti/';
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

    $file_new_name = 'bukti_' . $id . '_' . time() . '.' . $file_ext;
    $file_path = $upload_dir . $file_new_name;

    if (move_uploaded_file($_FILES['bukti_path']['tmp_name'], $file_path)) {
        // Hapus bukti lama
        if ($old_bukti && file_exists(__DIR__ . '/../' . $old_bukti)) {
            unlink(__DIR__ . '/../' . $old_bukti);
        }
        $bukti_path = 'assets/bukti/' . $file_new_name;
    }
}

try {
    $stmt = $pdo->prepare("
        UPDATE pembayaran SET
            nominal = :nominal,
            tgl_jatuh_tempo = :tgl_jatuh_tempo,
            status = :status,
            tgl_bayar = :tgl_bayar,
            metode_bayar = :metode_bayar,
            denda = :denda,
            bukti_path = :bukti_path,
            keterangan = :keterangan,
            updated_at = now()
        WHERE id = :id
    ");

    $stmt->execute([
        ':nominal' => $nominal,
        ':tgl_jatuh_tempo' => $tgl_jatuh_tempo,
        ':status' => $status,
        ':tgl_bayar' => $tgl_bayar,
        ':metode_bayar' => $metode_bayar,
        ':denda' => $denda,
        ':bukti_path' => $bukti_path,
        ':keterangan' => $keterangan,
        ':id' => $id
    ]);

    $_SESSION['flash_message'] = 'Pembayaran berhasil diupdate!';
    $_SESSION['flash_type'] = 'success';
} catch (Exception $e) {
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'danger';
}

header('Location: list.php');
exit;
?>