<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dynamic Pivot Table Generator</title>
    <script>
        function generatePivotTable() {
            let table = document.getElementById('table').value;
            let staticColumn = document.getElementById('static_column').value;
            let pivotColumn = document.getElementById('pivot_column').value;
            let valueColumn = document.getElementById('value_column').value;
            let aggregate = document.getElementById('aggregate').value;

            let formData = new FormData();
            formData.append('table', table);
            formData.append('static_column', staticColumn);
            formData.append('pivot_column', pivotColumn);
            formData.append('value_column', valueColumn);
            formData.append('aggregate', aggregate);

            fetch('generate_pivot.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                let resultDiv = document.getElementById('result');
                resultDiv.innerHTML = generateTable(data);
            });
        }

        function generateTable(data) {
            if (data.length === 0) return 'No data available';

            let table = '<table border="1">';
            table += '<tr>';
            for (let key in data[0]) {
                table += `<th>${key}</th>`;
            }
            table += '</tr>';
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
    </script>
</head>
<body>
    <h1>Dynamic Pivot Table Generator</h1>
    <form id="pivotForm">
        <label for="table">Table:</label>
        <input type="text" id="table" name="table" required><br>

        <label for="static_column">Static Column:</label>
        <input type="text" id="static_column" name="static_column" required><br>

        <label for="pivot_column">Pivot Column:</label>
        <input type="text" id="pivot_column" name="pivot_column" required><br>

        <label for="value_column">Value Column:</label>
        <input type="text" id="value_column" name="value_column" required><br>

        <label for="aggregate">Aggregate Function:</label>
        <input type="text" id="aggregate" name="aggregate" value="SUM"><br>

        <button type="button" onclick="generatePivotTable()">Generate Pivot Table</button>
    </form>

    <h3>Pivot Table Result</h3>
    <div id="result"></div>
</body>
</html>
