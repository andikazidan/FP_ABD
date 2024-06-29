<?php
include 'includes/config.php';

$sql = "SELECT * FROM report_definitions";
$result = $conn->query($sql);

$reports = [];
while ($row = $result->fetch_assoc()) {
    $reports[] = $row;
}

echo json_encode($reports);

$conn->close();
?>
