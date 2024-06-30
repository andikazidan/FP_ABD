<?php
$host = 'localhost';
$username = 'root';
$password = ''; // or your actual password if set

try {
    $dbh = new PDO("mysql:host=$host", $username, $password);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>