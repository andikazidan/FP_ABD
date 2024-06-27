<?php
include('config.php');

if (isset($_POST['database']) && isset($_POST['table'])) {
    $database = $_POST['database'];
    $table = $_POST['table'];
    
    // Switch to the selected database
    $dbh->exec("USE $database");

    $columnsStmt = $dbh->query("SHOW COLUMNS FROM $table");
    $columns = $columnsStmt->fetchAll(PDO::FETCH_COLUMN);

    $dataStmt = $dbh->query("SELECT * FROM $table");
    $results = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['columns' => $columns, 'results' => $results]);
} else {
    echo json_encode(['columns' => [], 'results' => []]);
}
?>
