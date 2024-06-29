<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

if (isset($_POST['database'], $_POST['table'])) {
    $database = $_POST['database'];
    $table = $_POST['table'];

    try {
        $dbh->query("USE `$database`");

        // Fetch all rows from the table
        $stmt = $dbh->prepare("SELECT * FROM `$table`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt->errorCode() !== '00000') {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['error' => $errorInfo[2]]);
            exit();
        }

        // Prepare response data
        $columns = array_keys($rows[0]);
        $results = $rows;

        echo json_encode(['columns' => $columns, 'results' => $results]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Required parameters are missing']);
}
?>
