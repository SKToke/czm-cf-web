@php
    use \App\Models\Campaign;
@endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Statement</title>
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
            font-size: 12px;
        }
        th, td {
            padding: 6px;
            text-align: left;
            border: 1px solid #ddd;
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
<div style="text-align: center">
    <img width="100%"
         src="{{ public_path('images/zakat_calculation_header.png') }}"/>
</div>
<h4>Payment Statement</h4>
<table>
    <tr>
        <th colspan="2" style="text-align: center">User Information</th>
    </tr>
    <tr>
        <td>Name</td>
        <td>{{ $currentUser->getFullNameAttribute() }}</td>
    </tr>
    <tr>
        <td>Email</td>
        <td>{{ $currentUser->email }}</td>
    </tr>
    <tr>
        <td>Mobile Number</td>
        <td>{{ $currentUser->mobile_no }}</td>
    </tr>
</table>
@php

@endphp

<table>
    <tr>
        <th style="text-align: center">Statement Range</th>
        <th style="text-align: center">Start Date</th>
        <th style="text-align: center">End Date</th>
    </tr>
    <tr>
        <td style="text-align: center"></td>
        <td style="text-align: center">{{ $start_date }}</td>
        <td style="text-align: center">{{ $end_date }}</td>
    </tr>
</table>

<table>
    <tr>
        <th style="text-align: center">Serial</th>
        <th style="text-align: center">Date</th>
        <th style="text-align: center">Type</th>
        <th style="text-align: center">Program</th>
        <th style="text-align: center">Campaign</th>
        <th style="text-align: center">Payment Amount</th>
    </tr>

    @foreach ($payments as $index => $payment)
        @php
            $program = null;
            $campaign = null;
            $type = 'general';
            if ($payment->campaign_id && $payment->campaign) {
                $campaign =  $payment->campaign;
                $program = $payment->campaign->program;
                $type = 'usual';
            } elseif ($payment->campaign_id && !$payment->campaign) {
                $campaign = Campaign::withTrashed()->where('id', $payment->campaign_id)->first();
                $program = $campaign->program;
                $type = 'deleted';
            }
        @endphp
        <tr>
            <td>{{ $index+1 }}</td>
            <td>{{ $payment->created_at->format('Y-m-d') }}</td>
            <td>{{ $payment->donation_type->getTitle() }}</td>
            @if ($type == 'general')
                <td>General Purpose</td>
                <td>General Purpose</td>
            @else
                <td>{{ $program->title }}</td>
                <td>{{ $campaign->title }}</td>
            @endif
            <td>{{ $payment->amount }} TK</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="5">Total</td>
        <td>{{ $totalAmount }} TK</td>
    </tr>
</table>

</body>
</html>
