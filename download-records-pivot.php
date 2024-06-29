<?php
session_start();

if (strlen($_SESSION['alogin']) == 0) {	
    header('location:index.php');
    exit();
} else {
    $data = json_decode($_POST['data'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'Invalid JSON data.';
        exit();
    }

    $filename = "Exported_Excel_File.xls";

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    if (is_array($data) && count($data) > 0) {
        echo '<table border="1">';
        echo '<tr>';
        foreach (array_keys($data[0]) as $column) {
            echo "<th>$column</th>";
        }
        echo '</tr>';
        foreach ($data as $row) {
            echo '<tr>';
            foreach ($row as $cell) {
                echo "<td>$cell</td>";
            }
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo 'No data available';
    }
}
?>
