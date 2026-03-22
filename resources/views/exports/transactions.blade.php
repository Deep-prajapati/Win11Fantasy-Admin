<!DOCTYPE html>
<html>
<head>
    <title>Transactions</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h3>Transaction List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Mobile</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $txn)
                <tr>
                    <td>{{ $txn->id }}</td>
                    <td>{{ $txn->user->name }}</td>
                    <td>{{ $txn->user->mobile_number }}</td>
                    <td>{{ $txn->type == 1 ? 'Credit' : 'Deduct' }}</td>
                    <td>{{ $txn->amount }}</td>
                    <td>{{ $txn->desc }}</td>
                    <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('d-m-Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
