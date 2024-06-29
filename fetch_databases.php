<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

try {
    $stmt = $dbh->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Filter out system databases
    $databases = array_filter($databases, function($db) {
        return !in_array($db, ['information_schema', 'mysql', 'performance_schema', 'sys']);
    });

    echo json_encode(['databases' => array_values($databases)]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>