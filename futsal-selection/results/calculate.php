<?php
include_once '../config/db_connect.php';

// Weight mapping for gaps based on journal
function getWeight($gap) {
    $weightMap = [
        0 => 5,
        1 => 4.5,
        -1 => 4,
        2 => 3.5,
        -2 => 3,
        3 => 2.5,
        -3 => 2,
        4 => 1.5,
        -4 => 1
    ];
    return $weightMap[$gap] ?? 1; // Default to 1 for unexpected gaps
}

// Fetch all players (ensure unique)
$players_stmt = $pdo->query("SELECT DISTINCT id, kode_pemain, nama_pemain FROM players ORDER BY kode_pemain");
$players = $players_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all criteria
$criteria_stmt = $pdo->query("SELECT id, kode_kriteria, nama_kriteria FROM criteria");
$criteria = $criteria_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all sub-criteria
$sub_criteria_stmt = $pdo->query("SELECT sc.id, sc.kode_subkriteria, sc.nama_subkriteria, sc.kriteria_id, sc.factor_type, sc.nilai_ideal, c.kode_kriteria 
                                  FROM sub_criteria sc 
                                  JOIN criteria c ON sc.kriteria_id = c.id");
$sub_criteria = $sub_criteria_stmt->fetchAll(PDO::FETCH_ASSOC);

// Organize sub-criteria by criteria_id and kode_kriteria
$sub_criteria_by_criterion = [];
$sub_criteria_by_id = [];
foreach ($sub_criteria as $sub) {
    $sub_criteria_by_criterion[$sub['kriteria_id']][] = $sub;
    $sub_criteria_by_id[$sub['id']] = $sub;
}

// Initialize results array
$results = [];

// Process each player
foreach ($players as $player) {
    $player_id = $player['id'];
    $player_result = [
        'kode_pemain' => $player['kode_pemain'],
        'nama_pemain' => $player['nama_pemain'],
        'criteria' => [],
        'final_score' => 0,
        'rank' => 0
    ];

    // Process each criterion (Taktikal and Individu)
    foreach ($criteria as $criterion) {
        $criterion_id = $criterion['id'];
        $criterion_code = $criterion['kode_kriteria'];
        $core_weights = [];
        $secondary_weights = [];

        // Fetch scores for this player and criterion's sub-criteria
        $scores_stmt = $pdo->prepare("SELECT sc.id AS subkriteria_id, sc.kode_subkriteria, sc.nilai_ideal, s.nilai 
                                      FROM sub_criteria sc 
                                      LEFT JOIN scores s ON s.subkriteria_id = sc.id AND s.player_id = ? 
                                      WHERE sc.kriteria_id = ?");
        $scores_stmt->execute([$player_id, $criterion_id]);
        $scores = $scores_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculate gaps and weights
        $criterion_result = [
            'gaps' => [],
            'weights' => [],
            'ncf' => 0,
            'nsf' => 0,
            'nt' => 0
        ];

        foreach ($scores as $score) {
            $subkriteria_id = $score['subkriteria_id'];
            $kode_subkriteria = $score['kode_subkriteria'];
            $nilai = $score['nilai'] ?? null;
            $nilai_ideal = $score['nilai_ideal'];

            if ($nilai !== null) {
                $gap = $nilai - $nilai_ideal;
                $weight = getWeight($gap);
                $criterion_result['gaps'][$kode_subkriteria] = $gap;
                $criterion_result['weights'][$kode_subkriteria] = $weight;

                // Find factor type
                foreach ($sub_criteria_by_criterion[$criterion_id] as $sub) {
                    if ($sub['id'] == $subkriteria_id) {
                        if ($sub['factor_type'] == 'Core') {
                            $core_weights[] = $weight;
                        } else {
                            $secondary_weights[] = $weight;
                        }
                        break;
                    }
                }
            }
        }

        // Calculate NCF (Core Factor average) and NSF (Secondary Factor average)
        $criterion_result['ncf'] = !empty($core_weights) ? array_sum($core_weights) / count($core_weights) : 0;
        $criterion_result['nsf'] = !empty($secondary_weights) ? array_sum($secondary_weights) / count($secondary_weights) : 0;

        // Calculate NT (60% NCF + 40% NSF)
        $criterion_result['nt'] = (0.6 * $criterion_result['ncf']) + (0.4 * $criterion_result['nsf']);

        $player_result['criteria'][$criterion_code] = $criterion_result;
    }

    // Calculate final score (60% NT Taktikal + 40% NT Individu)
    $nt_taktikal = $player_result['criteria']['KT']['nt'] ?? 0;
    $nt_individu = $player_result['criteria']['KI']['nt'] ?? 0;
    $player_result['final_score'] = (0.6 * $nt_taktikal) + (0.4 * $nt_individu);

    $results[$player_id] = $player_result; // Use player_id as key to prevent duplicates
}

// Convert results to indexed array for sorting
$results = array_values($results);

// Sort players by final score and assign ranks, handling ties
usort($results, function($a, $b) {
    if ($a['final_score'] == $b['final_score']) {
        return strcmp($a['kode_pemain'], $b['kode_pemain']); // Tiebreak by kode_pemain
    }
    return $b['final_score'] <=> $a['final_score'];
});

$last_score = null;
$last_rank = 0;
$current_rank = 0;
foreach ($results as &$result) {
    $current_rank++;
    if ($result['final_score'] !== $last_score) {
        $result['rank'] = $current_rank;
        $last_rank = $current_rank;
    } else {
        $result['rank'] = $last_rank;
    }
    $last_score = $result['final_score'];
}

file_put_contents('debug_results.txt', print_r($results, true));

// Return results for use in display.php
return $results;
?>