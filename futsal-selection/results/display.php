<?php
include '../config/db_connect.php';
include '../includes/header.php';
include '../includes/navbar.php';
$results = include 'calculate.php';

// Fetch sub-criteria for display
$sub_criteria_stmt = $pdo->query("SELECT sc.id, sc.kode_subkriteria, sc.nama_subkriteria, sc.factor_type, c.kode_kriteria 
                                  FROM sub_criteria sc 
                                  JOIN criteria c ON sc.kriteria_id = c.id 
                                  ORDER BY c.kode_kriteria, sc.kode_subkriteria");
$sub_criteria = $sub_criteria_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2 class="my-4">Peringkat Pemain</h2>
    <div class="card">
        <div class="card-body">
            <button id="refresh-btn" class="btn btn-primary mb-3">Refresh Peringkat</button>
            <table class="table table-bordered table-hover" id="ranking-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Pemain</th>
                        <th>Nama Pemain</th>
                        <th>NCF Taktikal</th>
                        <th>NSF Taktikal</th>
                        <th>NT Taktikal</th>
                        <th>NCF Individu</th>
                        <th>NSF Individu</th>
                        <th>NT Individu</th>
                        <th>Skor Akhir</th>
                        <th>Peringkat</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Isi awal dari PHP -->
                    <?php foreach ($results as $index => $result): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($result['kode_pemain']); ?></td>
                            <td><?php echo htmlspecialchars($result['nama_pemain']); ?></td>
                            <td><?php echo number_format($result['criteria']['KT']['ncf'], 2); ?></td>
                            <td><?php echo number_format($result['criteria']['KT']['nsf'], 2); ?></td>
                            <td><?php echo number_format($result['criteria']['KT']['nt'], 2); ?></td>
                            <td><?php echo number_format($result['criteria']['KI']['ncf'], 2); ?></td>
                            <td><?php echo number_format($result['criteria']['KI']['nsf'], 2); ?></td>
                            <td><?php echo number_format($result['criteria']['KI']['nt'], 2); ?></td>
                            <td><?php echo number_format($result['final_score'], 2); ?></td>
                            <td><?php echo $result['rank']; ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#details<?php echo $index; ?>" aria-expanded="false" aria-controls="details<?php echo $index; ?>">
                                    Lihat
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="details<?php echo $index; ?>">
                            <td colspan="12">
                                <div class="p-3">
                                    <h5>Detail Sub-Kriteria</h5>
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Kriteria</th>
                                                <th>Kode Sub-Kriteria</th>
                                                <th>Nama Sub-Kriteria</th>
                                                <th>Factor Type</th>
                                                <th>Nilai</th>
                                                <th>Nilai Ideal</th>
                                                <th>Gap</th>
                                                <th>Weight</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($sub_criteria as $sub): ?>
                                                <?php
                                                $criterion_code = $sub['kode_kriteria'];
                                                $kode_subkriteria = $sub['kode_subkriteria'];
                                                $gap = $result['criteria'][$criterion_code]['gaps'][$kode_subkriteria] ?? null;
                                                if ($gap !== null):
                                                    $score_stmt = $pdo->prepare("SELECT s.nilai, sc.nilai_ideal 
                                                                                 FROM scores s 
                                                                                 JOIN sub_criteria sc ON s.subkriteria_id = sc.id 
                                                                                 WHERE s.player_id = (SELECT id FROM players WHERE kode_pemain = ?) 
                                                                                 AND sc.kode_subkriteria = ?");
                                                    $score_stmt->execute([$result['kode_pemain'], $kode_subkriteria]);
                                                    $score_data = $score_stmt->fetch(PDO::FETCH_ASSOC);
                                                    $nilai = $score_data['nilai'] ?? '-';
                                                    $nilai_ideal = $score_data['nilai_ideal'] ?? '-';
                                                ?>
                                                    <tr>
                                                        <td><?php echo htmlspecialchars($criterion_code); ?></td>
                                                        <td><?php echo htmlspecialchars($kode_subkriteria); ?></td>
                                                        <td><?php echo htmlspecialchars($sub['nama_subkriteria']); ?></td>
                                                        <td><?php echo htmlspecialchars($sub['factor_type']); ?></td>
                                                        <td><?php echo $nilai; ?></td>
                                                        <td><?php echo $nilai_ideal; ?></td>
                                                        <td><?php echo $gap; ?></td>
                                                        <td><?php echo number_format($result['criteria'][$criterion_code]['weights'][$kode_subkriteria], 2); ?></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Fungsi untuk memperbarui tabel
    function updateRanking() {
        $.ajax({
            url: 'api.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var tbody = $('#ranking-table tbody');
                tbody.empty();
                $.each(data, function(index, result) {
                    var row = `<tr>
                        <td>${index + 1}</td>
                        <td>${result.kode_pemain}</td>
                        <td>${result.nama_pemain}</td>
                        <td>${parseFloat(result.criteria.KT.ncf).toFixed(2)}</td>
                        <td>${parseFloat(result.criteria.KT.nsf).toFixed(2)}</td>
                        <td>${parseFloat(result.criteria.KT.nt).toFixed(2)}</td>
                        <td>${parseFloat(result.criteria.KI.ncf).toFixed(2)}</td>
                        <td>${parseFloat(result.criteria.KI.nsf).toFixed(2)}</td>
                        <td>${parseFloat(result.criteria.KI.nt).toFixed(2)}</td>
                        <td>${parseFloat(result.final_score).toFixed(2)}</td>
                        <td>${result.rank}</td>
                        <td><button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#details${index}" aria-expanded="false" aria-controls="details${index}">Lihat</button></td>
                    </tr>`;
                    // Tambahkan detail collapse (diperlukan logika tambahan untuk subkriteria)
                    tbody.append(row);
                });
            },
            error: function() {
                alert('Gagal memperbarui peringkat.');
            }
        });
    }

    // Refresh saat tombol diklik
    $('#refresh-btn').click(function() {
        updateRanking();
    });

    // Optional: Refresh otomatis setiap 5 detik
    // setInterval(updateRanking, 5000);
});
</script>
<?php include '../includes/footer.php'; ?>