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
        // Check if the database exists
        $stmt = $dbh->query("SHOW DATABASES LIKE " . $dbh->quote($database));
        if ($stmt->rowCount() == 0) {
            throw new Exception("Database '$database' does not exist");
        }

        // Select the database
        $dbh->exec("USE `$database`");

        // Check if the table exists
        $stmt = $dbh->query("SHOW TABLES LIKE " . $dbh->quote($table));
        if ($stmt->rowCount() == 0) {
            throw new Exception("Table '$table' does not exist in database '$database'");
        }

        // Fetch distinct pivot column values to create dynamic columns
        $stmt = $dbh->query("SELECT DISTINCT `$pivotColumn` FROM `$table`");
        $pivotValues = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Create dynamic CASE statements for each pivot column value
        $caseStatements = [];
        foreach ($pivotValues as $value) {
            $caseStatements[] = "$aggregationFunction(CASE WHEN `$pivotColumn` = " . $dbh->quote($value) . " THEN `$valueColumn` ELSE NULL END) AS `" . $dbh->quote($value) . "`";
        }
        $caseStatementsString = implode(", ", $caseStatements);

        // Construct dynamic SQL query
        $query = "
        SELECT 'Aggregation' AS 'Pivot_$pivotColumn', $caseStatementsString
        FROM `$table`";

        // Execute the pivot query
        $stmt = $dbh->query($query);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($stmt->errorCode() !== '00000') {
            $errorInfo = $stmt->errorInfo();
            throw new Exception($errorInfo[2]);
        }

        // Prepare response data
        $columns = array_keys($rows[0]);
        $results = $rows;

        echo json_encode(['columns' => $columns, 'results' => $results]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Required parameters are missing']);
}
?>