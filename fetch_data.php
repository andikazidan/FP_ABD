<?php
include 'includes/config.php';

// Fetch databases
if ($_GET['action'] == 'databases') {
    $sql = "SHOW DATABASES";
    $result = $conn->query($sql);

    $databases = [];
    while ($row = $result->fetch_assoc()) {
        $databases[] = $row['Database'];
    }

    echo json_encode($databases);
}

// Fetch tables
if ($_GET['action'] == 'tables' && isset($_GET['database'])) {
    $database = $_GET['database'];
    $conn->select_db($database);

    $sql = "SHOW TABLES";
    $result = $conn->query($sql);

    $tables = [];
    while ($row = $result->fetch_assoc()) {
        $tables[] = array_values($row)[0];
    }

    echo json_encode($tables);
}

// Fetch columns
if ($_GET['action'] == 'columns' && isset($_GET['database']) && isset($_GET['table'])) {
    $database = $_GET['database'];
    $table = $_GET['table'];
    $conn->select_db($database);

    $sql = "SHOW COLUMNS FROM $table";
    $result = $conn->query($sql);

    $columns = [];
    while ($row = $result->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    echo json_encode($columns);
}

$conn->close();
?>
