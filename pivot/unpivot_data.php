<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

// Tambahkan ini di awal file untuk logging
error_log("Unpivot request received");
error_log("POST data: " . print_r($_POST, true));

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

header('Content-Type: application/json');

// Ensure all required parameters are present
if (isset($_POST['database'], $_POST['table'], $_POST['pivotColumn'], $_POST['valueColumn'], $_POST['pivotData'])) {
    error_log("All required parameters are present");
    $database = $_POST['database'];
    $table = $_POST['table'];
    $pivotColumn = $_POST['pivotColumn'];
    $valueColumn = $_POST['valueColumn'];
    $pivotData = json_decode($_POST['pivotData'], true);

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

        // Process unpivot
        $unpivotedData = [];
        $columns = array_keys($pivotData[0]);
        $nonPivotColumns = array_diff($columns, [$valueColumn]);

        foreach ($pivotData as $row) {
            foreach ($nonPivotColumns as $column) {
                if ($column !== $pivotColumn) {
                    $unpivotedRow = [
                        $pivotColumn => $column,
                        $valueColumn => $row[$column]
                    ];
                    foreach ($nonPivotColumns as $nonPivotColumn) {
                        if ($nonPivotColumn !== $column) {
                            $unpivotedRow[$nonPivotColumn] = $row[$nonPivotColumn];
                        }
                    }
                    $unpivotedData[] = $unpivotedRow;
                }
            }
        }

        error_log("Unpivoted data: " . print_r($unpivotedData, true));

        echo json_encode([
            'columns' => array_keys($unpivotedData[0]),
            'results' => $unpivotedData
        ]);
    } catch (Exception $e) {
        error_log("Error occurred: " . $e->getMessage());
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    error_log("Missing parameters: " . print_r($_POST, true));
    echo json_encode(['error' => 'Required parameters are missing']);
}
?>