<?php
include '../config/db_connect.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM players WHERE id = ?");
$stmt->execute([$id]);
header("Location: list.php");
exit;
?>