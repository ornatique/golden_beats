<!DOCTYPE html>
<html>
<head>
    <title>Custom Estimate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 13px;
        }

        .container {
            width: 700px;
            margin: auto;
        }

        h2, h3 {
            margin: 5px 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }

        table th {
            background: #f2f2f2;
        }

        .no-border td {
            border: none;
            padding: 4px;
        }

        .text-left {
            text-align: left;
        }

        img {
            max-width: 120px;
            height: auto;
        }
    </style>
</head>
<body>

<div class="container">

    <h3>Custom Estimate</h3>
    <h2>Estimate</h2>

    <table class="no-border">
        <tr>
            <td class="text-left">
                <b>Customer Name:</b>
                {{ ucfirst($customOrder->user->name ?? '-') }}
            </td>
            <td class="text-left">
                <b>Date:</b>
                {{ $customOrder->created_at->format('d/m/Y') }}
                |
                <b>Time:</b>
                {{ $customOrder->created_at->format('h:i A') }}
            </td>
        </tr>
    </table>

    <br>

    <table>
        <thead>
            <tr>
                <th style="width:50px">Sr</th>
                <th style="width:150px">Image</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>

                <td>
                    @if($customOrder->image)
                        <img src="{{ asset($customOrder->image) }}">
                    @else
                        No Image
                    @endif
                </td>

                <td>
                    {{ $customOrder->remarks ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table>

</div>

<script>
    window.onload = function () {
        window.print();
    }
</script>

</body>
</html>
