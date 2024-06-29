<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

// Ensure all required parameters are present
if (isset($_POST['database'], $_POST['table'], $_POST['pivotColumn'], $_POST['valueColumn'])) {
    $database = $_POST['database'];
    $table = $_POST['table'];
    $pivotColumn = $_POST['pivotColumn'];
    $valueColumn = $_POST['valueColumn'];

    try {
        $dbh->exec("USE `$database`");

        // Check if the table exists
        $stmt = $dbh->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            throw new Exception("Table '$table' does not exist in database '$database'");
        }

        // Get all columns except the pivot and value columns
        $stmt = $dbh->query("DESCRIBE `$table`");
        $allColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $otherColumns = array_diff($allColumns, [$pivotColumn, $valueColumn]);

        // Construct the UNPIVOT query
        $selectColumns = implode(', ', $otherColumns);
        $query = "
            SELECT $selectColumns, `$pivotColumn` AS PivotColumn, `$valueColumn` AS PivotValue
            FROM `$table`
        ";

        // Execute the unpivot query
        $stmt = $dbh->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['columns' => array_keys(current($rows)), 'results' => $rows]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Required parameters are missing']);
}
?>
