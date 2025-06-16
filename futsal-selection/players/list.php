<?php
include '../config/db_connect.php';
include '../includes/header.php';
include '../includes/navbar.php';

$stmt = $pdo->query("SELECT * FROM players");
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2 class="my-4">Daftar Pemain</h2>
    <a href="add.php" class="btn btn-primary mb-3">Tambah Pemain</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode Pemain</th>
                <th>Nama Pemain</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td><?php echo htmlspecialchars($player['kode_pemain']); ?></td>
                    <td><?php echo htmlspecialchars($player['nama_pemain']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $player['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete.php?id=<?php echo $player['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include '../includes/footer.php'; ?>