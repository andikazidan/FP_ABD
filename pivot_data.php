<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

if (isset($_POST['database'], $_POST['table'], $_POST['pivotColumn'], $_POST['valueColumn'], $_POST['aggregationFunction'])) {
    $database = $_POST['database'];
    $table = $_POST['table'];
    $pivotColumn = $_POST['pivotColumn'];
    $valueColumn = $_POST['valueColumn'];
    $aggregationFunction = $_POST['aggregationFunction'];

    try {
        $dbh->query("USE `$database`");

        // Fetch distinct pivot column values
        $stmt = $dbh->query("SELECT DISTINCT `$pivotColumn` FROM `$table`");
        $pivotValues = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetch rows grouped by pivot column with the aggregation function applied to the value column
        $stmt = $dbh->prepare(
        "
        SELECT `$pivotColumn`, $aggregationFunction(`$valueColumn`) 
        AS `aggregated_value` 
        FROM `$table` 
        GROUP BY `$pivotColumn`
        "
        );
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt->errorCode() !== '00000') {
            $errorInfo = $stmt->errorInfo();
            echo json_encode(['error' => $errorInfo[2]]);
            exit();
        }

        // Prepare response data
        $columns = [$pivotColumn, $aggregationFunction];
        $results = [];

        foreach ($rows as $row) {
            $results[] = [
                $pivotColumn => $row[$pivotColumn],
                $aggregationFunction => $row['aggregated_value']
            ];
        }

        echo json_encode(['columns' => $columns, 'results' => $results]);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Required parameters are missing']);
}
?>
