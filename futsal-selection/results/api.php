<?php
header('Content-Type: application/json');
include 'calculate.php';
echo json_encode($results);
?>