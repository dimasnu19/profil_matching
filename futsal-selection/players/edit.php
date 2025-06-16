<?php
include '../config/db_connect.php';
include '../includes/header.php';
include '../includes/navbar.php';

$player_id = $_GET['id'] ?? null;
if (!$player_id) {
    header("Location: list.php");
    exit;
}

// Fetch player
$stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
$stmt->execute([$player_id]);
$player = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$player) {
    header("Location: list.php");
    exit;
}

// Fetch sub-criteria
$sub_criteria_stmt = $pdo->query("SELECT sc.id, sc.kode_subkriteria, sc.nama_subkriteria, c.nama_kriteria 
                                  FROM sub_criteria sc 
                                  JOIN criteria c ON sc.kriteria_id = c.id 
                                  ORDER BY c.nama_kriteria, sc.kode_subkriteria");
$sub_criteria = $sub_criteria_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch existing scores
$scores_stmt = $pdo->prepare("SELECT subkriteria_id, nilai FROM scores WHERE player_id = ?");
$scores_stmt->execute([$player_id]);
$existing_scores = [];
while ($row = $scores_stmt->fetch(PDO::FETCH_ASSOC)) {
    $existing_scores[$row['subkriteria_id']] = $row['nilai'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_pemain = trim($_POST['kode_pemain']);
    $nama_pemain = trim($_POST['nama_pemain']);
    $scores = $_POST['scores'] ?? [];

    // Basic validation
    if (empty($kode_pemain) || empty($nama_pemain)) {
        echo '<div class="alert alert-danger">Kode dan nama pemain harus diisi.</div>';
    } else {
        try {
            // Update player
            $stmt = $pdo->prepare("UPDATE players SET kode_pemain = ?, nama_pemain = ? WHERE id = ?");
            $stmt->execute([$kode_pemain, $nama_pemain, $player_id]);

            // Update scores
            foreach ($scores as $subkriteria_id => $nilai) {
                $nilai = (int)$nilai;
                if ($nilai >= 1 && $nilai <= 5) {
                    $stmt = $pdo->prepare("INSERT INTO scores (player_id, subkriteria_id, nilai) VALUES (?, ?, ?) 
                                           ON DUPLICATE KEY UPDATE nilai = ?");
                    $stmt->execute([$player_id, $subkriteria_id, $nilai, $nilai]);
                }
            }

            // Redirect to results page
            header("Location: ../results/display.php");
            exit;
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }
}
?>

<div class="container">
    <h2 class="my-4">Edit Pemain</h2>
    <div class="card">
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="kode_pemain" class="form-label">Kode Pemain</label>
                    <input type="text" class="form-control" id="kode_pemain" name="kode_pemain" value="<?php echo htmlspecialchars($player['kode_pemain']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="nama_pemain" class="form-label">Nama Pemain</label>
                    <input type="text" class="form-control" id="nama_pemain" name="nama_pemain" value="<?php echo htmlspecialchars($player['nama_pemain']); ?>" required>
                </div>
                <h4>Skor Sub-Kriteria</h4>
                <?php foreach ($sub_criteria as $sub): ?>
                    <div class="mb-3">
                        <label for="score_<?php echo $sub['id']; ?>" class="form-label"><?php echo htmlspecialchars($sub['nama_kriteria'] . ' - ' . $sub['nama_subkriteria']); ?></label>
                        <input type="number" class="form-control" id="score_<?php echo $sub['id']; ?>" name="scores[<?php echo $sub['id']; ?>]" min="1" max="5" value="<?php echo isset($existing_scores[$sub['id']]) ? $existing_scores[$sub['id']] : ''; ?>" required>
                    </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>