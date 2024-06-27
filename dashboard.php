<?php
session_start();
error_reporting(0);
include('config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}

$msg = "";


// Default database and table
$defaultDatabase = "bbdms";
$defaultTable = "tblblooddonars";

$database = isset($_GET['db']) ? $_GET['db'] : $defaultDatabase;
$table = isset($_GET['table']) ? $_GET['table'] : $defaultTable;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    
    <title>Report Generator</title>
    <script>
        function fetchDatabases() {
            fetch('fetch_data.php?action=databases')
            .then(response => response.json())
            .then(data => {
                let databaseSelect = document.getElementById('database');
                databaseSelect.innerHTML = '<option value="">Select Database</option>';
                data.forEach(database => {
                    databaseSelect.innerHTML += `<option value="${database}">${database}</option>`;
                });
            });
        }

        function fetchTables() {
            let database = document.getElementById('database').value;
            if (database) {
                fetch(`fetch_data.php?action=tables&database=${database}`)
                .then(response => response.json())
                .then(data => {
                    let tableSelect = document.getElementById('table');
                    tableSelect.innerHTML = '<option value="">Select Table</option>';
                    data.forEach(table => {
                        tableSelect.innerHTML += `<option value="${table}">${table}</option>`;
                    });
                });
            }
        }

        function fetchColumns() {
            let database = document.getElementById('database').value;
            let table = document.getElementById('table').value;
            if (database && table) {
                fetch(`fetch_data.php?action=columns&database=${database}&table=${table}`)
                .then(response => response.json())
                .then(data => {
                    let columnsDiv = document.getElementById('columns');
                    columnsDiv.innerHTML = '';
                    data.forEach(column => {
                        columnsDiv.innerHTML += `<input type="checkbox" name="columns" value="${column}">${column}<br>`;
                    });
                });
            }
        }

      function generateReport() {
        let database = document.getElementById('database').value;
        let table = document.getElementById('table').value;
        let columns = Array.from(document.querySelectorAll('input[name="columns"]:checked')).map(checkbox => checkbox.value).join(', ');
        let group_by = document.getElementById('group_by').value;
        let filter_column = document.getElementById('filter_column').value;
        let filter_value = document.getElementById('filter_value').value;

        let formData = new FormData();
        formData.append('database', database);
        formData.append('table', table);
        formData.append('columns', columns);
        formData.append('group_by', group_by);
        formData.append('filter_column', filter_column);
        formData.append('filter_value', filter_value);

        fetch('generate_report.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let resultDiv = document.getElementById('result');
            resultDiv.innerHTML = generateTable(data);

            // Save data for export
            document.getElementById('export_data').value = JSON.stringify(data);
        });
    }

    function generateTable(data) {
        if (data.length === 0) return 'No data available';

        let table = '<table border="1">';
        // Generate table header
        table += '<tr>';
        for (let key in data[0]) {
            table += `<th>${key}</th>`;
        }
        table += '</tr>';
        // Generate table rows
        data.forEach(row => {
            table += '<tr>';
            for (let key in row) {
                table += `<td>${row[key]}</td>`;
            }
            table += '</tr>';
        });
        table += '</table>';
        return table;
    }

        window.onload = fetchDatabases;

    function exportToExcel() {
        document.getElementById('exportForm').submit();
    }
    </script>
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
    
	<a href="dashboard.php" style="font-size: 20px; color:skyblue; text-decoration: underline;">
		<h4>
			Group by and Case Report 
		</h4>
	</a>  

	<a href="pivot.php" style="font-size: 20px; color:skyblue; text-decoration: underline;">
		<h4>
			Pivot and Unpivot Report 
		</h4>
	</a>

    <form id="reportForm">
        <select id="database" name="database" onchange="fetchTables()" required>
            <option value="">Select Database</option>
        </select>
        <select id="table" name="table" onchange="fetchColumns()" required>
            <option value="">Select Table</option>
        </select>

        <div id="columns"></div>

        <input type="text" id="group_by" name="group_by" placeholder="Group By Column">
    </form>

	<input type="text" id="filter_column" name="filter_column" placeholder="Filter Column">
	<input type="text" id="filter_value" name="filter_value" placeholder="Filter Value">
    <p>
    <button type="button" onclick="generateReport()">Generate Report</button>
    <h4>Report Result</h4>
    <div id="result"></div>
    <form id="exportForm" method="POST" action="download-records.php">
        <input type="hidden" id="export_data" name="data">
        <button type="button" onclick="exportToExcel()">Export to Excel</button>
    </form>


</body>
</html>
