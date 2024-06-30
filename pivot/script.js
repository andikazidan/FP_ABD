$(document).ready(function() {
    let originalData = [];
    let pivotData = [];
    let currentView = 'original';

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
        console.log('Selected Database:', selectedDatabase);

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
        console.log('Selected Table:', selectedTable);

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

    // Fetch and display the original table
    $('#showOriginalTable').click(function() {
        if ($('#originalTableContainer').is(':visible')) {
            $('#originalTableContainer').hide();
            return;
        }
    
        var selectedDatabase = $('#selectDatabase').val();
        var selectedTable = $('#selectTable').val();
    
        if (selectedDatabase && selectedTable) {
            $.ajax({
                url: 'fetch_original_table.php',
                type: 'POST',
                data: { database: selectedDatabase, table: selectedTable },
                dataType: 'json',
                success: function(response) {
                    console.log('Original Table Data Response:', response);
                    if (response.error) {
                        console.error('Error:', response.error);
                        return;
                    }
    
                    originalData = response.results;
                    currentView = 'original';
                    displayTable(originalData, response.columns, 'originalTable');
    
                    $('#originalTableContainer').show();
                    $('#pivotTableContainer').hide();
                    $('#unpivotTableContainer').hide();
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                }
            });
        } else {
            alert('Please select a database and table.');
        }
    });
    
    $('#showPivotTable').click(function() {
        if ($('#pivotTableContainer').is(':visible')) {
            $('#pivotTableContainer').hide();
            return;
        }

        var selectedDatabase = $('#selectDatabase').val();
        var selectedTable = $('#selectTable').val();
        var pivotColumn = $('#selectPivotColumn').val();
        var valueColumn = $('#selectValueColumn').val();
        var aggregationFunction = $('#selectAggregationFunction').val();

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
                    if (response.error) {
                        alert('Error: ' + response.error);
                        return;
                    }

                    pivotData = response.results;
                    currentView = 'pivot';
                    
                    displayTable(pivotData, response.columns, 'pivotTable');

                    $('#pivotTableContainer').show();
                    $('#originalTableContainer').hide();
                    $('#unpivotTableContainer').hide();
                },
                error: function(xhr, status, error) {
                    alert('An error occurred while fetching pivot data');
                }
            });
        } else {
            alert('Please select all required fields for pivot table.');
        }
    });

    // Fungsi untuk unpivot tabel
    $('#unpivotTable').click(function() {
        console.log("Unpivot button clicked");
    
        if ($('#unpivotTableContainer').is(':visible')) {
            $('#unpivotTableContainer').hide();
            return;
        }
    
        if (!pivotData || pivotData.length === 0) {
            console.log("No pivot data available");
            alert('Please create a pivot table first.');
            return;
        }
        $.ajax({
            url: 'unpivot_data.php',
            type: 'POST',
            data: {
                database: $('#selectDatabase').val(),
                table: $('#selectTable').val(),
                pivotColumn: $('#selectPivotColumn').val(),
                valueColumn: $('#selectValueColumn').val(),
                pivotData: JSON.stringify(pivotData)
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    console.error('Error: ' + response.error);
                    alert('Error: ' + response.error);
                    return;
                }
    
                console.log('Unpivot Response:', response);
    
                displayTable(response.results, response.columns, 'unpivotTable');
                currentView = 'unpivot';
                $('#unpivotTableContainer').show();
                $('#pivotTableContainer').hide();
                $('#originalTableContainer').hide();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('An error occurred while unpivoting the table');
            }
        });
    });

    function displayTable(data, columns, tableId) {
        
        var tableHead = $('#' + tableId + ' thead');
        var tableBody = $('#' + tableId + ' tbody');
    
        tableHead.empty();
        tableBody.empty();
    
        var headerRow = $('<tr>');
        var filteredColumns = columns;
        
        // Filter out columns if tableId is 'unpivotTable'
        if (tableId === 'unpivotTable') {
            filteredColumns = columns.filter((col, index) => index < 2);
        }
    
        $.each(filteredColumns, function(index, column) {
            headerRow.append($('<th>').text(column));
        });
        tableHead.append(headerRow);
    
        $.each(data, function(index, rowData) {
            var row = $('<tr>');
            $.each(filteredColumns, function(colIndex, columnName) {
                row.append($('<td>').text(rowData[columnName]));
            });
            tableBody.append(row);
        });
    }
    // Export to Excel function
function exportToExcel(data, filename) {
    var ws = XLSX.utils.json_to_sheet(data);
    var wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    XLSX.writeFile(wb, filename);
}

// Export to Excel button click handler
$('#exportToExcel').click(function() {
    let dataToExport;
    let filename;

    if (currentView === 'pivot' && pivotData.length > 0) {
        dataToExport = pivotData;
        filename = 'pivot_table.xlsx';
    } else if (currentView === 'unpivot' && $('#unpivotTable tbody tr').length > 0) {
        dataToExport = [];
        let headers = [];
        $('#unpivotTable thead th').each(function() {
            headers.push($(this).text());
        });
        $('#unpivotTable tbody tr').each(function() {
            let row = {};
            $(this).find('td').each(function(index) {
                row[headers[index]] = $(this).text();
            });
            dataToExport.push(row);
        });
        filename = 'unpivot_table.xlsx';
    } else {
        alert('Please generate a pivot or unpivot table first.');
        return;
    }

    exportToExcel(dataToExport, filename);
});
});