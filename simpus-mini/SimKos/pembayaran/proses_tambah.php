<?php
require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Validasi input
$penghuni_id = isset($_POST['penghuni_id']) ? (int)$_POST['penghuni_id'] : 0;
$periode_bulan = isset($_POST['periode_bulan']) ? (int)$_POST['periode_bulan'] : 0;
$periode_tahun = isset($_POST['periode_tahun']) ? (int)$_POST['periode_tahun'] : 0;
$nominal = isset($_POST['nominal']) ? (float)$_POST['nominal'] : 0;
$tgl_jatuh_tempo = isset($_POST['tgl_jatuh_tempo']) ? $_POST['tgl_jatuh_tempo'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : 'BELUM BAYAR';
$tgl_bayar = isset($_POST['tgl_bayar']) ? $_POST['tgl_bayar'] : null;
$metode_bayar = isset($_POST['metode_bayar']) ? $_POST['metode_bayar'] : null;
$denda = isset($_POST['denda']) ? (float)$_POST['denda'] : 0;
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : null;

$errors = [];

if (!$penghuni_id) $errors[] = 'Penghuni harus dipilih';
if (!$periode_bulan || $periode_bulan < 1 || $periode_bulan > 12) $errors[] = 'Bulan tidak valid';
if (!$periode_tahun || $periode_tahun < 2020) $errors[] = 'Tahun tidak valid';
if (!$nominal || $nominal <= 0) $errors[] = 'Nominal harus lebih besar dari 0';
if (!$tgl_jatuh_tempo) $errors[] = 'Tanggal jatuh tempo harus diisi';

if ($status === 'LUNAS') {
    if (!$tgl_bayar) $errors[] = 'Tanggal pembayaran harus diisi untuk status Lunas';
    if (!$metode_bayar) $errors[] = 'Metode pembayaran harus dipilih untuk status Lunas';
}

if (!empty($errors)) {
    $_SESSION['flash_message'] = implode('; ', $errors);
    $_SESSION['flash_type'] = 'danger';
    header('Location: tambah.php');
    exit;
}

// Handle upload bukti (optional) - validasi dulu sebelum transaction
$bukti_path = null;
$file_to_move = null;
$file_destination = null;

if (isset($_FILES['bukti_path']) && $_FILES['bukti_path']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = __DIR__ . '/../assets/bukti/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $file_tmp = $_FILES['bukti_path']['tmp_name'];
    $file_name = $_FILES['bukti_path']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];

    if (!in_array($file_ext, $allowed_ext)) {
        $_SESSION['flash_message'] = 'Format file tidak diizinkan. Gunakan: PDF, JPG, PNG';
        $_SESSION['flash_type'] = 'danger';
        header('Location: tambah.php');
        exit;
    }

    if ($_FILES['bukti_path']['size'] > 2 * 1024 * 1024) { // 2MB
        $_SESSION['flash_message'] = 'Ukuran file terlalu besar (max 2MB)';
        $_SESSION['flash_type'] = 'danger';
        header('Location: tambah.php');
        exit;
    }

    $file_new_name = 'bukti_' . $penghuni_id . '_' . time() . '.' . $file_ext;
    $file_destination = $upload_dir . $file_new_name;
    $file_to_move = $file_tmp;
    $bukti_path = 'assets/bukti/' . $file_new_name;
}

// Insert pembayaran dengan transaction
try {
    $pdo->beginTransaction();

    // Cek duplikat periode dengan FOR UPDATE - mencegah race condition
    // Skenario: 2 petugas generate tagihan periode sama secara bersamaan
    $check = $pdo->prepare("SELECT id FROM pembayaran WHERE penghuni_id = :penghuni_id AND periode_bulan = :bulan AND periode_tahun = :tahun FOR UPDATE");
    $check->execute([':penghuni_id' => $penghuni_id, ':bulan' => $periode_bulan, ':tahun' => $periode_tahun]);
    if ($check->rowCount() > 0) {
        throw new Exception('Tagihan untuk periode ini sudah ada. Gunakan Edit jika ingin mengubah.');
    }

    // Move file setelah validasi duplikat lolos
    if ($file_to_move && $file_destination) {
        if (!move_uploaded_file($file_to_move, $file_destination)) {
            throw new Exception('Gagal menyimpan file bukti.');
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO pembayaran
        (penghuni_id, periode_bulan, periode_tahun, nominal, tgl_jatuh_tempo, tgl_bayar, status, metode_bayar, denda, bukti_path, keterangan)
        VALUES
        (:penghuni_id, :bulan, :tahun, :nominal, :tgl_jatuh_tempo, :tgl_bayar, :status, :metode_bayar, :denda, :bukti_path, :keterangan)
    ");

    $stmt->execute([
        ':penghuni_id' => $penghuni_id,
        ':bulan' => $periode_bulan,
        ':tahun' => $periode_tahun,
        ':nominal' => $nominal,
        ':tgl_jatuh_tempo' => $tgl_jatuh_tempo,
        ':tgl_bayar' => $tgl_bayar ?: null,
        ':status' => $status,
        ':metode_bayar' => $metode_bayar,
        ':denda' => $denda,
        ':bukti_path' => $bukti_path,
        ':keterangan' => $keterangan
    ]);

    $pdo->commit();

    $_SESSION['flash_message'] = 'Pembayaran berhasil dicatat!';
    $_SESSION['flash_type'] = 'success';
} catch (Exception $e) {
    $pdo->rollBack();

    // Hapus file jika sudah di-upload tapi transaction gagal
    if ($file_destination && file_exists($file_destination)) {
        unlink($file_destination);
    }

    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'danger';
}

header('Location: list.php');
exit;
?>
