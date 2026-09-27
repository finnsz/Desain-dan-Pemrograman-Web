<?php
require_once __DIR__ . '/../config/database.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? $_GET['q'] : '';

$whereClause = "";
$params = [];
if ($search) {
    $whereClause = "WHERE nomor_kamar ILIKE :search OR tipe ILIKE :search";
    $params[':search'] = "%$search%";
}

// Hitung total
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM kamar $whereClause");
$stmtTotal->execute($params);
$totalData = $stmtTotal->fetchColumn();
$totalPages = ceil($totalData / $limit);

// Ambil data
$query = "SELECT * FROM kamar $whereClause ORDER BY id DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$kamar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form method="GET">
    <input type="text" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="Cari kamar...">
    <button type="submit">Cari</button>
</form>

<table border="1">
    <tr>
        <th>ID</th><th>Nomor Kamar</th><th>Tipe</th><th>Harga</th><th>Status</th><th>Aksi</th>
    </tr>
    <?php foreach ($kamar as $k): ?>
    <tr>
        <td><?= $k['id'] ?></td>
        <td><?= htmlspecialchars($k['nomor_kamar']) ?></td>
        <td><?= htmlspecialchars($k['tipe']) ?></td>
        <td><?= $k['harga'] ?></td>
        <td><?= $k['status'] ?></td>
        <td>
            <a href="edit.php?id=<?= $k['id'] ?>">Edit</a>
            <form action="hapus.php" method="POST" class="form-hapus" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
                <input type="hidden" name="id" value="<?= $k['id'] ?>">
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