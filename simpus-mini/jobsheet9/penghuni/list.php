<?php
require_once __DIR__ . '/../config/database.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';

$whereClause = "";
$params = [];
if ($search) {
    $whereClause = "WHERE p.nama_lengkap ILIKE :search OR p.no_hp ILIKE :search";
    $params[':search'] = "%$search%";
}

// Hitung total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM penghuni p $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data
$query = "SELECT p.*, k.nomor_kamar FROM penghuni p LEFT JOIN kamar k ON p.kamar_id = k.id $whereClause ORDER BY p.id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$penghuni = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="GET">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama penghuni...">
    <button type="submit">Cari</button>
</form>

<table border="1">
    <tr>
        <th>ID</th><th>Nama</th><th>No HP</th><th>Nomor Kamar</th><th>Tgl Masuk</th><th>Aksi</th>
    </tr>
    <?php foreach ($penghuni as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
        <td><?= htmlspecialchars($p['no_hp']) ?></td>
        <td><?= htmlspecialchars($p['nomor_kamar'] ?? '-') ?></td>
        <td><?= $p['tgl_masuk'] ?></td>
        <td>
            <a href="edit.php?id=<?= $p['id'] ?>">Edit</a>
            <form action="hapus.php" method="POST" class="form-hapus" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                <button type="submit">Hapus</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<div>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a>
    <?php endfor; ?>
</div>