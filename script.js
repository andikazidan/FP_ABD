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

    // Fetch and display the list of tables when a database is selected
    $('#selectDatabase').change(function() {
        var selectedDatabase = $(this).val();
        console.log('Selected Database:', selectedDatabase); // Debugging

        $.ajax({
            url: 'fetch_tables.php',
            type: 'POST',
            data: { database: selectedDatabase },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error:', response.error);
                    return;
                }

                var tableDropdown = $('#selectTable');
                tableDropdown.empty();
                tableDropdown.append('<option value="">Select Table</option>');

                $.each(response.tables, function(index, table) {
                    tableDropdown.append($('<option>', {
                        value: table,
                        text: table
                    }));
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });

    // Fetch and display the list of columns when a table is selected
    $('#selectTable').change(function() {
        var selectedDatabase = $('#selectDatabase').val();
        var selectedTable = $(this).val();
        console.log('Selected Table:', selectedTable); // Debugging

        $.ajax({
            url: 'fetch_columns.php',
            type: 'POST',
            data: { database: selectedDatabase, table: selectedTable },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error:', response.error);
                    return;
                }

                var columnDropdown = $('#selectPivotColumn, #selectValueColumn');
                columnDropdown.empty();
                columnDropdown.append('<option value="">Select Column</option>');

                $.each(response.columns, function(index, column) {
                    columnDropdown.append($('<option>', {
                        value: column,
                        text: column
                    }));
                });
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });

    // Fetch and display the pivot table
    $('#showPivotTable').click(function() {
        var selectedDatabase = $('#selectDatabase').val();
        var selectedTable = $('#selectTable').val();
        var pivotColumn = $('#selectPivotColumn').val();
        var valueColumn = $('#selectValueColumn').val();
        var aggregationFunction = $('#selectAggregationFunction').val();

        console.log('Selected Database:', selectedDatabase);
        console.log('Selected Table:', selectedTable);
        console.log('Pivot Column:', pivotColumn);
        console.log('Value Column:', valueColumn);
        console.log('Aggregation Function:', aggregationFunction);

        if (selectedDatabase && selectedTable && pivotColumn && valueColumn && aggregationFunction) {
            $.ajax({
                url: 'pivot_data.php',
                type: 'POST',
                data: {
                    database: selectedDatabase,
                    table: selectedTable,
                    pivotColumn: pivotColumn,
                    valueColumn: valueColumn,
                    aggregationFunction: aggregationFunction
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Pivot Data Response:', response);
                    if (response.error) {
                        console.error('Error:', response.error);
                        return;
                    }

                    var tableHead = $('#pivotTable thead');
                    var tableBody = $('#pivotTable tbody');

                    tableHead.empty();
                    tableBody.empty();

                    // Debugging: Check if the columns are being processed
                    console.log('Columns:', response.columns);

                    var headerRow = $('<tr>');
                    $.each(response.columns, function(index, column) {
                        headerRow.append($('<th>').text(column));
                    });
                    tableHead.append(headerRow);

                    // Debugging: Check if the rows are being processed
                    console.log('Results:', response.results);

                    $.each(response.results, function(index, rowData) {
                        var row = $('<tr>');
                        $.each(response.columns, function(colIndex, columnName) {
                            row.append($('<td>').text(rowData[columnName]));
                        });
                        tableBody.append(row);
                    });

                    $('#pivotTableContainer').show();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        } else {
            alert('Please select all required fields.');
        }
    });
});