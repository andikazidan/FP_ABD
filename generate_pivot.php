<?php
include('config-pivot.php');

// Get user inputs
$table = $_POST['table'];
$staticColumn = $_POST['static_column'];
$pivotColumn = $_POST['pivot_column'];
$valueColumn = $_POST['value_column'];
$aggregateFunction = isset($_POST['aggregate']) ? $_POST['aggregate'] : 'SUM';

// Fetch unique pivot values
$pivotValuesQuery = "SELECT DISTINCT $pivotColumn FROM $table WHERE $pivotColumn IS NOT NULL";
$pivotValuesResult = $conn->query($pivotValuesQuery);

$pivotValues = [];
while ($row = $pivotValuesResult->fetch_assoc()) {
    $pivotValues[] = $row[$pivotColumn];
}

// Generate pivot columns
$pivotColumns = implode(', ', array_map(function($value) {
    return "[$value]";
}, $pivotValues));

// Create dynamic pivot query
$query = "SELECT '$aggregateFunction($valueColumn)' AS $staticColumn, $pivotColumns
          FROM (
              SELECT $staticColumn, $pivotColumn, $valueColumn
              FROM $table
          ) AS SourceTable
          PIVOT (
              $aggregateFunction($valueColumn)
              FOR $pivotColumn IN ($pivotColumns)
          ) AS PivotTable";

$result = $conn->query($query);

// Fetch results as an associative array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output results as JSON
echo json_encode($data);
?>
