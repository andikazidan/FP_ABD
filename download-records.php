<?php
session_start();

if (strlen($_SESSION['alogin']) == 0) {	
    header('location:index.php');
} else {
    $data = json_decode($_POST['data'], true);
    $filename = "Exported_Excel_File.xls";

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=$filename");
    header("Pragma: no-cache");
    header("Expires: 0");

    if (count($data) > 0) {
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
