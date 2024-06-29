<?php
include 'includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = $_POST['database'];
    $table = $_POST['table'];
    $columns = $_POST['columns'];
    $group_by = $_POST['group_by'];

    // Optional filter parameters
    $filter_column = $_POST['filter_column'];
    $filter_value = $_POST['filter_value'];

    // Connect to database
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Initialize SQL query
    $query = "SELECT $columns FROM $table";

    // harus banget dijelasin
    // Add WHERE clause with CASE statement for specific filtering
    if (!empty($filter_column) && !empty($filter_value)) {
        $query .= " WHERE CASE WHEN $filter_column = '$filter_value' THEN 1 ELSE 0 END = 1";
    }

    // Add GROUP BY clause if provided
    if (!empty($group_by)) {
        $query = "
        SELECT $group_by, COUNT($group_by) AS total_count FROM $table
        GROUP BY $group_by
        ";
    }

    // Execute query
    $result = $conn->query($query);

    if ($result) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Send data back as JSON
        echo json_encode($data);
    } else {
        echo "Error: " . $conn->error;
    }

    // Close connection
    $conn->close();
}
?>
