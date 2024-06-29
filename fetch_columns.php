<?php
session_start();
error_reporting(0);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

if (isset($_POST['database']) && isset($_POST['table'])) {
    $database = $_POST['database'];
    $table = $_POST['table'];

    try {
        $stmt = $dbh->query("SHOW COLUMNS FROM `$database`.`$table`");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo json_encode(['columns' => $columns]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Database or table not selected']);
}
?>
