<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pivot and Unpivot Tableaa</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="script.js"></script>
</head>
<body>
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

        <button id="showPivotTable" class="btn btn-primary" style="margin-top: 10px;">Show Pivot Table</button>
        <button id="showUnpivotTable" class="btn btn-secondary" style="margin-top: 10px;">Show Unpivot Table</button>
    </div>

    <div id="pivotTableContainer" style="display:none; margin-top: 20px;">
        <table id="pivotTable" border="1">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
    
</body>
</html>

<script>
    $(document).ready(function() {
        // ... (existing code for fetching databases, tables, and columns) ...

        // Fetch and display the pivot table data
        $('#showPivotTable').click(function() {
            var selectedDatabase = $('#selectDatabase').val();
            var selectedTable = $('#selectTable').val();
            var selectedPivotColumn = $('#selectPivotColumn').val();
            var selectedValueColumn = $('#selectValueColumn').val();
            var selectedAggregationFunction = $('#selectAggregationFunction').val();

            $.ajax({
                url: 'pivot_data.php',
                type: 'POST',
                data: {
                    database: selectedDatabase,
                    table: selectedTable,
                    pivotColumn: selectedPivotColumn,
                    valueColumn: selectedValueColumn,
                    aggregationFunction: selectedAggregationFunction
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        console.error('Error:', response.error);
                        alert('An error occurred: ' + response.error);
                        return;
                    }

                    var table = $('#pivotTable');
                    var thead = table.find('thead');
                    var tbody = table.find('tbody');

                    thead.empty();
                    tbody.empty();

                    // Create table headers
                    var headerRow = $('<tr>');
                    $.each(response.columns, function(index, column) {
                        headerRow.append($('<th>').text(column));
                    });
                    thead.append(headerRow);

                    // Create table rows
                    $.each(response.results, function(index, row) {
                        var tableRow = $('<tr>');
                        $.each(response.columns, function(index, column) {
                            tableRow.append($('<td>').text(row[column] || ''));
                        });
                        tbody.append(tableRow);
                    });

                    $('#pivotTableContainer').show();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while fetching data');
                }
            });
        });

        // Fetch and display the unpivot table data
        $('#showUnpivotTable').click(function() {
            var selectedDatabase = $('#selectDatabase').val();
            var selectedTable = $('#selectTable').val();

            $.ajax({
                url: 'unpivot_data.php',
                type: 'POST',
                data: {
                    database: selectedDatabase,
                    table: selectedTable
                },
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        console.error('Error:', response.error);
                        alert('An error occurred: ' + response.error);
                        return;
                    }

                    var table = $('#pivotTable');
                    var thead = table.find('thead');
                    var tbody = table.find('tbody');

                    thead.empty();
                    tbody.empty();

                    // Create table headers
                    var headerRow = $('<tr>');
                    $.each(response.columns, function(index, column) {
                        headerRow.append($('<th>').text(column));
                    });
                    thead.append(headerRow);

                    // Create table rows
                    $.each(response.results, function(index, row) {
                        var tableRow = $('<tr>');
                        $.each(response.columns, function(index, column) {
                            tableRow.append($('<td>').text(row[column] || ''));
                        });
                        tbody.append(tableRow);
                    });

                    $('#pivotTableContainer').show();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while fetching data');
                }
            });
        });
        $(document).ready(function() {
    // Fetch and display the list of databases
    $.ajax({
        url: 'fetch_databases.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                console.error('Error:', response.error);
                return;
            }

            var databaseDropdown = $('#selectDatabase');
            databaseDropdown.empty();
            databaseDropdown.append('<option value="">Select Database</option>');

            $.each(response.databases, function(index, database) {
                databaseDropdown.append($('<option>', {
                    value: database,
                    text: database
                }));
            });
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });

    // ... rest of your code ...
});
    });
    </script>
</body>