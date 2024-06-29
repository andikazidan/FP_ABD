<?php
session_start();
error_reporting(0);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

if (isset($_POST['database'])) {
    $database = $_POST['database'];

    try {
        $stmt = $dbh->query("SHOW TABLES FROM `$database`");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode(['tables' => $tables]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'No database selected']);
}
?>
