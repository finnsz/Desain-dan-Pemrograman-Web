<?php
require __DIR__ . '/../includes/auth.php';
$base = '../';

require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    $_SESSION['flash_message'] = 'ID pembayaran tidak valid.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM v_pembayaran_detail WHERE id = :id");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p || $p['status'] !== 'LUNAS') {
    $_SESSION['flash_message'] = 'Kwitansi hanya bisa dicetak untuk pembayaran dengan status LUNAS.';
    $_SESSION['flash_type'] = 'danger';
    header('Location: list.php');
    exit;
}

$bulan_nama = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

function terbilang($angka) {
    $angka = abs($angka);
    $bilangan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');

    if ($angka < 12) return $bilangan[$angka];
    elseif ($angka < 20) return $bilangan[$angka - 10] . ' Belas';
    elseif ($angka < 100) return $bilangan[(int)($angka / 10)] . ' Puluh ' . $bilangan[$angka % 10];
    elseif ($angka < 200) return 'Seratus ' . terbilang($angka - 100);
    elseif ($angka < 1000) return $bilangan[(int)($angka / 100)] . ' Ratus ' . terbilang($angka % 100);
    elseif ($angka < 2000) return 'Seribu ' . terbilang($angka - 1000);
    elseif ($angka < 1000000) return terbilang((int)($angka / 1000)) . ' Ribu ' . terbilang($angka % 1000);
    elseif ($angka < 1000000000) return terbilang((int)($angka / 1000000)) . ' Juta ' . terbilang($angka % 1000000);
    else return 'Angka terlalu besar';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran #<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Arial', sans-serif;
            padding: 2rem;
            background: #f5f5f5;
        }
        .kwitansi {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border: 2px solid #333;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px double #333;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }
        .header h1 {
            font-size: 1.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0.5rem;
        }
        .header p {
            font-size: 0.9rem;
            color: #666;
        }
        .nomor {
            text-align: right;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .content {
            margin-bottom: 2rem;
        }
        .row {
            display: flex;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }
        .row .label {
            width: 200px;
            font-weight: bold;
        }
        .row .value {
            flex: 1;
        }
        .row .separator {
            margin: 0 0.5rem;
        }
        .total {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f9f9f9;
            border: 2px solid #333;
            text-align: center;
        }
        .total .amount {
            font-size: 2rem;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 0.5rem;
        }
        .total .terbilang {
            font-style: italic;
            color: #666;
        }
        .footer {
            margin-top: 3rem;
            display: flex;
            justify-content: space-between;
        }
        .footer .box {
            width: 45%;
            text-align: center;
        }
        .footer .box .label {
            font-size: 0.9rem;
            margin-bottom: 4rem;
        }
        .footer .box .name {
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 0.5rem;
            display: inline-block;
            min-width: 150px;
        }
        .print-btn {
            position: fixed;
            top: 1rem;
            right: 1rem;
            padding: 0.75rem 1.5rem;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(59,130,246,0.3);
        }
        .print-btn:hover {
            background: #2563eb;
        }
        @media print {
            body { background: white; padding: 0; }
            .kwitansi { border: none; box-shadow: none; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Print / PDF
    </button>

    <div class="kwitansi">
        <div class="header">
            <h1>KWITANSI</h1>
            <p>SIMKOS - Sistem Informasi Manajemen Kost</p>
        </div>

        <div class="nomor">
            No: <?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?>/KWT/<?= date('m/Y', strtotime($p['tgl_bayar'])) ?>
        </div>

        <div class="content">
            <div class="row">
                <div class="label">Sudah terima dari</div>
                <div class="separator">:</div>
                <div class="value"><strong><?= htmlspecialchars($p['nama_lengkap']) ?></strong></div>
            </div>

            <div class="row">
                <div class="label">Uang sejumlah</div>
                <div class="separator">:</div>
                <div class="value">
                    <strong><?= ucwords(trim(terbilang($p['total_bayar']))) ?> Rupiah</strong>
                </div>
            </div>

            <div class="row">
                <div class="label">Untuk pembayaran</div>
                <div class="separator">:</div>
                <div class="value">
                    Sewa Kamar <?= htmlspecialchars($p['nomor_kamar']) ?> (<?= htmlspecialchars($p['tipe']) ?>)
                    <br>Periode: <?= $bulan_nama[$p['periode_bulan']] ?> <?= $p['periode_tahun'] ?>
                </div>
            </div>

            <div class="row">
                <div class="label">Tanggal Pembayaran</div>
                <div class="separator">:</div>
                <div class="value"><?= date('d F Y', strtotime($p['tgl_bayar'])) ?></div>
            </div>

            <div class="row">
                <div class="label">Metode Pembayaran</div>
                <div class="separator">:</div>
                <div class="value"><?= htmlspecialchars($p['metode_bayar']) ?></div>
            </div>
        </div>

        <div class="total">
            <div class="amount">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></div>
            <div class="terbilang">
                <?php if ($p['denda'] > 0): ?>
                    (Nominal Sewa: Rp <?= number_format($p['nominal'], 0, ',', '.') ?> + Denda: Rp <?= number_format($p['denda'], 0, ',', '.') ?>)
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($p['keterangan'])): ?>
        <div style="margin-top: 1.5rem; padding: 1rem; background: #f9fafb; border-left: 4px solid #3b82f6; font-size: 0.9rem;">
            <strong>Keterangan:</strong> <?= nl2br(htmlspecialchars($p['keterangan'])) ?>
        </div>
        <?php endif; ?>

        <div class="footer">
            <div class="box">
                <div class="label">Penyewa,</div>
                <div class="name"><?= htmlspecialchars($p['nama_lengkap']) ?></div>
            </div>
            <div class="box">
                <div class="label">Pengelola Kost,</div>
                <div class="name">__________________</div>
            </div>
        </div>

        <div style="margin-top: 2rem; text-align: center; font-size: 0.75rem; color: #999;">
            Dicetak pada: <?= date('d F Y H:i') ?> WIB
        </div>
    </div>
</body>
</html>
