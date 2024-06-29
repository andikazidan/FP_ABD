<?php
session_start();
error_reporting(0);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

try {
    $stmt = $dbh->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['databases' => $databases]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
