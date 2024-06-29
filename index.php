<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pivot Table</title>
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

        <button id="showOriginalTable" class="btn btn-secondary" style="margin-top: 10px;">Show Original Table</button>
        <button id="showPivotTable" class="btn btn-primary" style="margin-top: 10px;">Show Pivot Table</button>
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
</body>
</html>
