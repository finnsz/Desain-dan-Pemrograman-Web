<?php
require __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/role.php';

// Hanya admin
require_admin('generate.php');

$periode_bulan = isset($_POST['periode_bulan']) ? (int)$_POST['periode_bulan'] : 0;
$periode_tahun = isset($_POST['periode_tahun']) ? (int)$_POST['periode_tahun'] : 0;
$tgl_jatuh_tempo = isset($_POST['tgl_jatuh_tempo']) ? $_POST['tgl_jatuh_tempo'] : '';
$filter_status = isset($_POST['filter_status']) ? $_POST['filter_status'] : 'TERISI';

$errors = [];

if (!$periode_bulan || $periode_bulan < 1 || $periode_bulan > 12) $errors[] = 'Bulan tidak valid';
if (!$periode_tahun || $periode_tahun < 2020) $errors[] = 'Tahun tidak valid';
if (!$tgl_jatuh_tempo) $errors[] = 'Tanggal jatuh tempo harus diisi';

if (!empty($errors)) {
    $_SESSION['flash_message'] = implode('; ', $errors);
    $_SESSION['flash_type'] = 'danger';
    header('Location: generate.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // Ambil semua penghuni aktif beserta kamar + harga mereka
    $where = '';
    if ($filter_status === 'TERISI') {
        $where = "AND k.status = 'TERISI'";
    }

    $query = "
        SELECT p.id, p.nama_lengkap, k.harga
        FROM penghuni p
        LEFT JOIN kamar k ON p.kamar_id = k.id
        WHERE p.kamar_id IS NOT NULL $where
        ORDER BY p.id
    ";

    $penghuni_list = $pdo->query($query)->fetchAll(PDO::FETCH_ASSOC);

    $generated_count = 0;
    $skipped_count = 0;

    foreach ($penghuni_list as $p) {
        // Cek apakah tagihan sudah ada dengan FOR UPDATE - mencegah race condition
        // Skenario: 2 generate job berjalan paralel untuk periode sama
        $check = $pdo->prepare("
            SELECT id FROM pembayaran
            WHERE penghuni_id = :penghuni_id AND periode_bulan = :bulan AND periode_tahun = :tahun
            FOR UPDATE
        ");
        $check->execute([
            ':penghuni_id' => $p['id'],
            ':bulan' => $periode_bulan,
            ':tahun' => $periode_tahun
        ]);

        if ($check->rowCount() > 0) {
            $skipped_count++;
            continue; // Skip, sudah ada
        }

        // Insert tagihan baru
        $stmt = $pdo->prepare("
            INSERT INTO pembayaran
            (penghuni_id, periode_bulan, periode_tahun, nominal, tgl_jatuh_tempo, status)
            VALUES
            (:penghuni_id, :bulan, :tahun, :nominal, :tgl_jatuh_tempo, 'BELUM BAYAR')
        ");

        $stmt->execute([
            ':penghuni_id' => $p['id'],
            ':bulan' => $periode_bulan,
            ':tahun' => $periode_tahun,
            ':nominal' => $p['harga'] ?: 0,
            ':tgl_jatuh_tempo' => $tgl_jatuh_tempo
        ]);

        $generated_count++;
    }

    $pdo->commit();

    $msg = "Generate berhasil! Dibuat: $generated_count tagihan baru";
    if ($skipped_count > 0) {
        $msg .= ", $skipped_count periode sudah ada (di-skip)";
    }

    $_SESSION['flash_message'] = $msg . '.';
    $_SESSION['flash_type'] = 'success';
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['flash_message'] = 'Error: ' . $e->getMessage();
    $_SESSION['flash_type'] = 'danger';
}

header('Location: list.php');
exit;
?>
