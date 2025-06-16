<?php
include '../config/db_connect.php';
include '../includes/header.php';
include '../includes/navbar.php';

// Fetch criteria
$criteria_stmt = $pdo->query("SELECT * FROM criteria");
$criteria = $criteria_stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add/Edit Criteria
    if (isset($_POST['save_criteria'])) {
        $kode_kriteria = trim($_POST['kode_kriteria']);
        $nama_kriteria = trim($_POST['nama_kriteria']);
        $tipe = $_POST['tipe'];
        $criteria_id = $_POST['criteria_id'] ?? null;

        if (empty($kode_kriteria) || empty($nama_kriteria) || !in_array($tipe, ['Taktikal', 'Individu'])) {
            echo '<div class="alert alert-danger">Data input tidak valid.</div>';
        } else {
            try {
                if ($criteria_id) {
                    $stmt = $pdo->prepare("UPDATE criteria SET kode_kriteria = ?, nama_kriteria = ?, tipe = ? WHERE id = ?");
                    $stmt->execute([$kode_kriteria, $nama_kriteria, $tipe, $criteria_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO criteria (kode_kriteria, nama_kriteria, tipe) VALUES (?, ?, ?)");
                    $stmt->execute([$kode_kriteria, $nama_kriteria, $tipe]);
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
    }

    // Delete Criteria
    if (isset($_POST['delete_criteria'])) {
        $criteria_id = $_POST['criteria_id'];
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM scores s JOIN sub_criteria sc ON s.subkriteria_id = sc.id WHERE sc.kriteria_id = ?");
        $check_stmt->execute([$criteria_id]);
        if ($check_stmt->fetchColumn() == 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM sub_criteria WHERE kriteria_id = ?");
                $stmt->execute([$criteria_id]);
                $stmt = $pdo->prepare("DELETE FROM criteria WHERE id = ?");
                $stmt->execute([$criteria_id]);
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error menghapus kriteria: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Tidak dapat menghapus kriteria yang memiliki skor terkait.</div>';
        }
    }

    // Add/Edit Sub-Criteria
    if (isset($_POST['save_sub_criteria'])) {
        $kode_subkriteria = trim($_POST['kode_subkriteria']);
        $nama_subkriteria = trim($_POST['nama_subkriteria']);
        $kriteria_id = $_POST['kriteria_id'];
        $factor_type = $_POST['factor_type'];
        $nilai_ideal = (int)$_POST['nilai_ideal'];
        $subkriteria_id = $_POST['subkriteria_id'] ?? null;

        if (empty($kode_subkriteria) || empty($nama_subkriteria) || !in_array($factor_type, ['Core', 'Secondary']) || $nilai_ideal < 1 || $nilai_ideal > 5) {
            echo '<div class="alert alert-danger">Data input subkriteria tidak valid.</div>';
        } else {
            try {
                if ($subkriteria_id) {
                    $stmt = $pdo->prepare("UPDATE sub_criteria SET kode_subkriteria = ?, nama_subkriteria = ?, factor_type = ?, nilai_ideal = ? WHERE id = ?");
                    $stmt->execute([$kode_subkriteria, $nama_subkriteria, $factor_type, $nilai_ideal, $subkriteria_id]);
                } else {
                    $stmt = $pdo->prepare("INSERT INTO sub_criteria (kode_subkriteria, nama_subkriteria, kriteria_id, factor_type, nilai_ideal) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$kode_subkriteria, $nama_subkriteria, $kriteria_id, $factor_type, $nilai_ideal]);
                }
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        }
    }

    // Delete Sub-Criteria
    if (isset($_POST['delete_sub_criteria'])) {
        $subkriteria_id = $_POST['subkriteria_id'];
        $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM scores WHERE subkriteria_id = ?");
        $check_stmt->execute([$subkriteria_id]);
        if ($check_stmt->fetchColumn() == 0) {
            try {
                $stmt = $pdo->prepare("DELETE FROM sub_criteria WHERE id = ?");
                $stmt->execute([$subkriteria_id]);
            } catch (PDOException $e) {
                echo '<div class="alert alert-danger">Error menghapus subkriteria: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger">Tidak dapat menghapus subkriteria yang memiliki skor terkait.</div>';
        }
    }

    // Add/Edit Scores
    if (isset($_POST['submit_scores'])) {
        $player_id = $_POST['player_id'];
        foreach ($_POST['scores'] as $subkriteria_id => $nilai) {
            $nilai = (int)$nilai;
            if ($nilai >= 1 && $nilai <= 5) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO scores (player_id, subkriteria_id, nilai) VALUES (?, ?, ?) 
                                           ON DUPLICATE KEY UPDATE nilai = ?");
                    $stmt->execute([$player_id, $subkriteria_id, $nilai, $nilai]);
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">Error menyimpan skor: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
        }
        // Redirect to results page
        header("Location: ../results/display.php");
        exit;
    }

    // Redirect to manage.php for other actions to avoid form resubmission
    if (!isset($_POST['submit_scores'])) {
        header("Location: manage.php");
        exit;
    }
}

// Fetch players for dropdown
$players_stmt = $pdo->query("SELECT * FROM players");
$players = $players_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2 class="my-4">Kelola Kriteria dan Skor</h2>

    <!-- Add Criteria Button -->
    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addCriteriaModal">Tambah Kriteria</button>

    <!-- Criteria List -->
    <?php foreach ($criteria as $criterion): ?>
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <h4><?php echo htmlspecialchars($criterion['nama_kriteria']); ?> (<?php echo htmlspecialchars($criterion['tipe']); ?>)</h4>
                    <div>
                        <button type="button" class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editCriteriaModal<?php echo $criterion['id']; ?>">Edit</button>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?');">
                            <input type="hidden" name="criteria_id" value="<?php echo $criterion['id']; ?>">
                            <button type="submit" name="delete_criteria" class="btn btn-danger btn-sm me-1">Hapus</button>
                        </form>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSubCriteriaModal<?php echo $criterion['id']; ?>">Tambah Sub-Kriteria</button>
                    </div>
                </div>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Kode Sub-Kriteria</th>
                            <th>Nama Sub-Kriteria</th>
                            <th>Factor Type</th>
                            <th>Nilai Ideal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sub_stmt = $pdo->prepare("SELECT * FROM sub_criteria WHERE kriteria_id = ?");
                        $sub_stmt->execute([$criterion['id']]);
                        $sub_criteria = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($sub_criteria as $sub): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sub['kode_subkriteria']); ?></td>
                                <td><?php echo htmlspecialchars($sub['nama_subkriteria']); ?></td>
                                <td><?php echo htmlspecialchars($sub['factor_type']); ?></td>
                                <td><?php echo htmlspecialchars($sub['nilai_ideal']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editSubCriteriaModal<?php echo $sub['id']; ?>">Edit</button>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus subkriteria ini?');">
                                        <input type="hidden" name="subkriteria_id" value="<?php echo $sub['id']; ?>">
                                        <button type="submit" name="delete_sub_criteria" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Criteria Modal -->
        <div class="modal fade" id="editCriteriaModal<?php echo $criterion['id']; ?>" tabindex="-1" aria-labelledby="editCriteriaModalLabel<?php echo $criterion['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCriteriaModalLabel<?php echo $criterion['id']; ?>">Edit Kriteria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="criteria_id" value="<?php echo $criterion['id']; ?>">
                            <div class="mb-3">
                                <label for="kode_kriteria_<?php echo $criterion['id']; ?>" class="form-label">Kode Kriteria</label>
                                <input type="text" class="form-control" id="kode_kriteria_<?php echo $criterion['id']; ?>" name="kode_kriteria" value="<?php echo htmlspecialchars($criterion['kode_kriteria']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama_kriteria_<?php echo $criterion['id']; ?>" class="form-label">Nama Kriteria</label>
                                <input type="text" class="form-control" id="nama_kriteria_<?php echo $criterion['id']; ?>" name="nama_kriteria" value="<?php echo htmlspecialchars($criterion['nama_kriteria']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipe_<?php echo $criterion['id']; ?>" class="form-label">Tipe</label>
                                <select class="form-control" id="tipe_<?php echo $criterion['id']; ?>" name="tipe" required>
                                    <option value="Taktikal" <?php if ($criterion['tipe'] == 'Taktikal') echo 'selected'; ?>>Taktikal</option>
                                    <option value="Individu" <?php if ($criterion['tipe'] == 'Individu') echo 'selected'; ?>>Individu</option>
                                </select>
                            </div>
                            <button type="submit" name="save_criteria" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Sub-Criteria Modal -->
        <div class="modal fade" id="addSubCriteriaModal<?php echo $criterion['id']; ?>" tabindex="-1" aria-labelledby="addSubCriteriaModalLabel<?php echo $criterion['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addSubCriteriaModalLabel<?php echo $criterion['id']; ?>">Tambah Sub-Kriteria untuk <?php echo htmlspecialchars($criterion['nama_kriteria']); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST">
                            <input type="hidden" name="kriteria_id" value="<?php echo $criterion['id']; ?>">
                            <div class="mb-3">
                                <label for="kode_subkriteria_add_<?php echo $criterion['id']; ?>" class="form-label">Kode Sub-Kriteria</label>
                                <input type="text" class="form-control" id="kode_subkriteria_add_<?php echo $criterion['id']; ?>" name="kode_subkriteria" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama_subkriteria_add_<?php echo $criterion['id']; ?>" class="form-label">Nama Sub-Kriteria</label>
                                <input type="text" class="form-control" id="nama_subkriteria_add_<?php echo $criterion['id']; ?>" name="nama_subkriteria" required>
                            </div>
                            <div class="mb-3">
                                <label for="factor_type_add_<?php echo $criterion['id']; ?>" class="form-label">Factor Type</label>
                                <select class="form-control" id="factor_type_add_<?php echo $criterion['id']; ?>" name="factor_type" required>
                                    <option value="Core">Core</option>
                                    <option value="Secondary">Secondary</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="nilai_ideal_add_<?php echo $criterion['id']; ?>" class="form-label">Nilai Ideal (1-5)</label>
                                <input type="number" class="form-control" id="nilai_ideal_add_<?php echo $criterion['id']; ?>" name="nilai_ideal" min="1" max="5" required>
                            </div>
                            <button type="submit" name="save_sub_criteria" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Sub-Criteria Modals -->
        <?php foreach ($sub_criteria as $sub): ?>
            <div class="modal fade" id="editSubCriteriaModal<?php echo $sub['id']; ?>" tabindex="-1" aria-labelledby="editSubCriteriaModalLabel<?php echo $sub['id']; ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editSubCriteriaModalLabel<?php echo $sub['id']; ?>">Edit Sub-Kriteria</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <input type="hidden" name="subkriteria_id" value="<?php echo $sub['id']; ?>">
                                <input type="hidden" name="kriteria_id" value="<?php echo $criterion['id']; ?>">
                                <div class="mb-3">
                                    <label for="kode_subkriteria_<?php echo $sub['id']; ?>" class="form-label">Kode Sub-Kriteria</label>
                                    <input type="text" class="form-control" id="kode_subkriteria_<?php echo $sub['id']; ?>" name="kode_subkriteria" value="<?php echo htmlspecialchars($sub['kode_subkriteria']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="nama_subkriteria_<?php echo $sub['id']; ?>" class="form-label">Nama Sub-Kriteria</label>
                                    <input type="text" class="form-control" id="nama_subkriteria_<?php echo $sub['id']; ?>" name="nama_subkriteria" value="<?php echo htmlspecialchars($sub['nama_subkriteria']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="factor_type_<?php echo $sub['id']; ?>" class="form-label">Factor Type</label>
                                    <select class="form-control" id="factor_type_<?php echo $sub['id']; ?>" name="factor_type" required>
                                        <option value="Core" <?php if ($sub['factor_type'] == 'Core') echo 'selected'; ?>>Core</option>
                                        <option value="Secondary" <?php if ($sub['factor_type'] == 'Secondary') echo 'selected'; ?>>Secondary</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="nilai_ideal_<?php echo $sub['id']; ?>" class="form-label">Nilai Ideal (1-5)</label>
                                    <input type="number" class="form-control" id="nilai_ideal_<?php echo $sub['id']; ?>" name="nilai_ideal" min="1" max="5" value="<?php echo htmlspecialchars($sub['nilai_ideal']); ?>" required>
                                </div>
                                <button type="submit" name="save_sub_criteria" class="btn btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <!-- Add Criteria Modal -->
    <div class="modal fade" id="addCriteriaModal" tabindex="-1" aria-labelledby="addCriteriaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCriteriaModalLabel">Tambah Kriteria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="kode_kriteria_add" class="form-label">Kode Kriteria</label>
                            <input type="text" class="form-control" id="kode_kriteria_add" name="kode_kriteria" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama_kriteria_add" class="form-label">Nama Kriteria</label>
                            <input type="text" class="form-control" id="nama_kriteria_add" name="nama_kriteria" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipe_add" class="form-label">Tipe</label>
                            <select class="form-control" id="tipe_add" name="tipe" required>
                                <option value="Taktikal">Taktikal</option>
                                <option value="Individu">Individu</option>
                            </select>
                        </div>
                        <button type="submit" name="save_criteria" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Scores Section -->
    <div class="card">
        <div class="card-body">
            <h3>Tambah Skor untuk Pemain</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="player_id" class="form-label">Pilih Pemain</label>
                    <select class="form-control" id="player_id" name="player_id" required>
                        <?php foreach ($players as $player): ?>
                            <option value="<?php echo $player['id']; ?>"><?php echo htmlspecialchars($player['kode_pemain'] . ' - ' . $player['nama_pemain']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php foreach ($criteria as $criterion): ?>
                    <h4><?php echo htmlspecialchars($criterion['nama_kriteria']); ?></h4>
                    <?php
                    $sub_stmt = $pdo->prepare("SELECT * FROM sub_criteria WHERE kriteria_id = ?");
                    $sub_stmt->execute([$criterion['id']]);
                    $sub_criteria = $sub_stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($sub_criteria as $sub): ?>
                        <div class="mb-3">
                            <label for="score_<?php echo $sub['id']; ?>" class="form-label"><?php echo htmlspecialchars($sub['nama_subkriteria']); ?></label>
                            <input type="number" class="form-control" id="score_<?php echo $sub['id']; ?>" name="scores[<?php echo $sub['id']; ?>]" min="1" max="5" required>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                <button type="submit" name="submit_scores" class="btn btn-primary">Simpan Skor</button>
            </form>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>