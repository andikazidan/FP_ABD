<?php
include('config.php');

if (isset($_POST['database'])) {
    $database = $_POST['database'];
    
    // Switch to the selected database
    $dbh->exec("USE $database");

    $stmt = $dbh->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode(['options' => $tables]);
} else {
    echo json_encode(['options' => []]);
}
?>
