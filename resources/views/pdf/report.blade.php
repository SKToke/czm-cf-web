<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h4 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 10px;
        }
        th, td {
            padding: 4px;
            text-align: left;
            border: 1px solid #ddd;
            max-width: 50px !important;
            word-wrap: break-word;
        }
        th {
            background-color: #4ea2cc;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f0f0f0;
        }
        th[colspan="2"] {
            background-color: #34a853;
        }
    </style>
</head>
<body>
<h4>{{ $title }}</h4>
@if (count($reportData) > 0)
    <table>
        <tr>
            @if ($headers)
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            @else
            @foreach($reportData[0] as $index => $reportHeader)
                <th>{{ str_replace('_', ' ', $index) }}</th>
            @endforeach
            @endif
        </tr>
        @foreach($reportData as $reportDataRow)
            <tr>
                @foreach($reportDataRow as $reportDataRowVal)
                    <td>{{ $reportDataRowVal }}</td>
                @endforeach
            </tr>
        @endforeach
    </table>
@endif

</body>
</html>
