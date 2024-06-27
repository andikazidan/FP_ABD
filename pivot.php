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

<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    
    <title>BBDMS | Admin Manage Blood groups</title>

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
    <!-- Admin Style -->
    <link rel="stylesheet" href="css/style.css">

    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
    </style>
</head>

<body>
    <?php include('includes/header.php'); ?>
    
                
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
    <div class="ts-main-content">
        
                
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
            

                        <!-- Dropdown to select database -->
                        <select id="selectDatabase" class="form-control">
                            <option value="">Pilih Database</option>
                            <?php
                            $databases = $dbh->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
                            foreach ($databases as $db_name) {
                                echo "<option value=\"$db_name\">$db_name</option>";
                            }
                            ?>
                        </select>

                        <!-- Dropdown to select table -->
                        <select id="selectTable" class="form-control" style="margin-top: 10px;">
                            <option value="">Pilih Tabel</option>
                        </select>

                        <!-- Dropdown to select column for grouping -->


                        <!-- Button to trigger data load -->
                        <button id="loadTableData" class="btn btn-primary" style="margin-top: 10px;">Load Table Data</button>

                        <!-- Button to show pivot table -->
                        <button id="showPivotTable" class="btn btn-secondary" style="margin-top: 10px;">Show Pivot Table</button>

                        <!-- Zero Configuration Table -->
                        <div class="panel panel-default" style="margin-top: 20px;">
                            <div class="panel-heading">Listed Data</div>
                            <div class="panel-body">
                                <?php if ($msg) { ?>
                                    <div class="succWrap"><strong>SUCCESS</strong>:<?php echo htmlentities($msg); ?></div>
                                <?php } ?>

                                <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                    <thead id="tableHead">
                                        <!-- Table header will be generated here -->
                                    </thead>
                                    <tbody id="tableBody">
                                        <!-- Table body will be generated here -->
                                    </tbody>
                                </table>

                                <!-- Pivot Table container -->
                                <div id="pivotTableContainer" style="margin-top: 20px; display: none;">
                                    <h3>Pivot Table</h3>
                                    <table id="pivotTable" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                        <!-- The pivot table will be generated here -->
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

    </div>

    <!-- Loading Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <form id="exportForm" method="POST" action="download-records.php">
    <input type="hidden" id="export_data" name="data">
    <button type="button" onclick="exportToExcel()">Export to Excel</button>
</form>

    <script>
        function exportToExcel() {
    var tableData = [];
    var headers = [];

    // Get headers
    $('#zctb thead th').each(function() {
        headers.push($(this).text());
    });

    // Get table data
    $('#zctb tbody tr').each(function() {
        var rowData = {};
        $(this).find('td').each(function(index) {
            rowData[headers[index]] = $(this).text();
        });
        tableData.push(rowData);
    });

    var json = JSON.stringify(tableData);
    $('#export_data').val(json);
    document.getElementById('exportForm').submit();
}

  $(document).ready(function() {
    // Function to load tables into selectTable dropdown
    $('#selectDatabase').change(function() {
        var selectedDatabase = $(this).val();
        $('#selectTable').html('<option value="">Loading...</option>');

        $.ajax({
            url: 'fetch_tables.php',
            type: 'POST',
            data: { database: selectedDatabase },
            dataType: 'json',
            success: function(response) {
                var tableDropdown = $('#selectTable');
                tableDropdown.empty();

                if (response.options.length > 0) {
                    $.each(response.options, function(index, tableName) {
                        tableDropdown.append($('<option>', {
                            value: tableName,
                            text: tableName
                        }));
                    });

                    // Trigger change to load columns for the first table
                    tableDropdown.trigger('change');
                } else {
                    tableDropdown.append($('<option>', {
                        value: '',
                        text: 'No tables found'
                    }));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching tables:', error);
            }
        });
    });

    // Function to load columns into selectGroupByColumn dropdown
    $('#selectTable').change(function() {
        var selectedDatabase = $('#selectDatabase').val();
        var selectedTable = $(this).val();
        $('#selectGroupByColumn').html('<option value="">Loading...</option>');

        $.ajax({
            url: 'fetch_columns.php',
            type: 'POST',
            data: { database: selectedDatabase, table: selectedTable },
            dataType: 'json',
            success: function(response) {
                var columnDropdown = $('#selectGroupByColumn');
                columnDropdown.empty();

                if (response.columns.length > 0) {
                    $.each(response.columns, function(index, column) {
                        columnDropdown.append($('<option>', {
                            value: column,
                            text: column
                        }));
                    });
                } else {
                    columnDropdown.append($('<option>', {
                        value: '',
                        text: 'No columns found'
                    }));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching columns:', error);
            }
        });
    });

    // Load initial table data on page load
    $('#loadTableData').click(function() {
        var selectedDatabase = $('#selectDatabase').val();
        var selectedTable = $('#selectTable').val();
        var selectedGroupByColumn = $('#selectGroupByColumn').val(); // Ambil nilai dari dropdown group by column

        if (selectedDatabase && selectedTable) {
            $.ajax({
                url: 'fetch_columns.php',
                type: 'POST',
                data: { database: selectedDatabase, table: selectedTable, groupByColumn: selectedGroupByColumn }, // Sertakan group by column dalam data yang dikirim
                dataType: 'json',
                success: function(response) {
                    var tableHead = $('#tableHead');
                    var tableBody = $('#tableBody');

                    tableHead.empty();
                    tableBody.empty();

                    if (response.columns.length > 0) {
                        var headerRow = $('<tr>');
                        $.each(response.columns, function(index, column) {
                            headerRow.append($('<th>').text(column));
                        });
                        headerRow.append($('<th>').text('Action'));
                        tableHead.append(headerRow);

                        $.each(response.results, function(index, rowData) {
                            var row = $('<tr>');
                            $.each(response.columns, function(colIndex, columnName) {
                                row.append($('<td>').text(rowData[columnName]));
                            });
                            row.append($('<td>').html('<a href="managebloodgroup.php?db=' + selectedDatabase + '&table=' + selectedTable + '&del=' + rowData.id + '" onclick="return confirm(\'Do you want to delete\');"><i class="fa fa-close"></i></a>'));
                            tableBody.append(row);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching columns:', error);
                }
            });
        }
    });

    // Function to show sorted data based on selected column
$('#showSortedData').click(function() {
    var selectedDatabase = $('#selectDatabase').val();
    var selectedTable = $('#selectTable').val();
    var selectedGroupByColumn = $('#selectGroupByColumn').val();

    if (selectedDatabase && selectedTable && selectedGroupByColumn) {
        $.ajax({
            url: 'fetch_columns.php',
            type: 'POST',
            data: { database: selectedDatabase, table: selectedTable },
            dataType: 'json',
            success: function(response) {
                var tableBody = $('#tableBody');
                tableBody.empty();

                if (response.results.length > 0) {
                    // Sorting based on selected column numerically
                    response.results.sort(function(a, b) {
                        var valueA = parseFloat(a[selectedGroupByColumn]);
                        var valueB = parseFloat(b[selectedGroupByColumn]);
                        return valueA - valueB;
                    });

                    $.each(response.results, function(index, rowData) {
                        var row = $('<tr>');
                        $.each(response.columns, function(colIndex, columnName) {
                            row.append($('<td>').text(rowData[columnName]));
                        });
                        row.append($('<td>').html('<a href="managebloodgroup.php?db=' + selectedDatabase + '&table=' + selectedTable + '&del=' + rowData.id + '" onclick="return confirm(\'Do you want to delete\');"><i class="fa fa-close"></i></a>'));
                        tableBody.append(row);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching columns:', error);
            }
        });
    }
});

    // Function to toggle pivot table visibility and generate pivot table
    $('#showPivotTable').click(function() {
        $('#pivotTableContainer').toggle();

        var tableData = [];
        var headers = [];

        // Get headers
        $('#zctb thead th').each(function() {
            headers.push($(this).text());
        });

        tableData.push(headers);

        // Get table data
        $('#zctb tbody tr').each(function() {
            var rowData = [];
            $(this).find('td').each(function() {
                rowData.push($(this).text());
            });
            tableData.push(rowData);
        });

        // Clear the existing pivot table
        $('#pivotTable').empty();

        // Generate pivot table (transpose the table data)
        for (var i = 0; i < tableData[0].length; i++) {
            var newRow = '<tr>';
            for (var j = 0; j < tableData.length; j++) {
                newRow += '<td>' + tableData[j][i] + '</td>';
            }
            newRow += '</tr>';
            $('#pivotTable').append(newRow);
        }
    });
});


    </script>
    
</body>
</html>