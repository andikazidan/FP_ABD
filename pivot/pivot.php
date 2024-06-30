<?php
session_start();
error_reporting(0);
include('config.php');
if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}
$msg = "";
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Pivot Table</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script src="script.js"></script>
        <!-- Font awesome -->
		<link rel="stylesheet" href="css/font-awesome.min.css">
	<!-- Sandstone Bootstrap CSS -->
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<!-- Bootstrap Datatables -->
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<!-- Bootstrap social button library -->
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<!-- Bootstrap select -->
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<!-- Bootstrap file input -->
	<link rel="stylesheet" href="css/fileinput.min.css">
	<!-- Awesome Bootstrap checkbox -->
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<!-- Admin Stye -->
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include('includes/header.php');?>
    <h1>Report Generator</h1>
    
	<a href="http://localhost/Final_ABD/dashboard.php" style="font-size: 20px; color:skyblue; text-decoration: underline;">
		<h4>
			Group by and Case Report 
		</h4>
	</a>  

	<a href="http://localhost/Final_ABD/pivot/pivot.php" style="font-size: 20px; color:skyblue; text-decoration: underline;">
		<h4>
			Pivot and Unpivot Report
		</h4>
	</a>
    <div>
        <select id="selectDatabase" class="form-control" style="margin-top: 10px;">
            <option value="">Select Database</option>
        </select>

        <select id="selectTable" class="form-control" style="margin-top: 10px;">
            <option value="">Select Table</option>
        </select>

        <select id="selectPivotColumn" class="form-control" style="margin-top: 10px;">
            <option value="">Select Pivot Column</option>
        </select>

        <select id="selectValueColumn" class="form-control" style="margin-top: 10px;">
            <option value="">Select Value Column</option>
        </select>

        <select id="selectAggregationFunction" class="form-control" style="margin-top: 10px;">
            <option value="">Select Aggregation Function</option>
            <option value="SUM">SUM</option>
            <option value="AVG">AVG</option>
            <option value="COUNT">COUNT</option>
            <option value="MAX">MAX</option>
            <option value="MIN">MIN</option>
        </select>

        <button id="showOriginalTable" class="btn btn-secondary" style="margin-top: 10px;">Show Original Table</button>
        <button id="showPivotTable" class="btn btn-primary" style="margin-top: 10px;">Show Pivot Table</button>
        <button id="unpivotTable" class="btn btn-primary" style="margin-top: 10px;">Unpivot Table</button>
        <button id="exportToExcel" class="btn btn-success" style="margin-top: 10px;">Export to Excel</button>
        
    </div>

    <div id="originalTableContainer" style="display:none; margin-top: 20px;">
        <h3>Original Table</h3>
        <table id="originalTable" border="1">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="pivotTableContainer" style="display:none; margin-top: 20px;">
        <h3>Pivot Table</h3>
        <table id="pivotTable" border="1">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="unpivotTableContainer" style="display:none; margin-top: 20px;">
        <h3>Unpivot Table</h3>
        <table id="unpivotTable" border="1">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
</body>
</html>