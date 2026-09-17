<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; font-size: 11px; }
    h3 { margin-bottom: 4px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
    th { background: #f2f2f2; }
    .text-end { text-align: right; }
</style>
</head>
<body>
    <h3>Fund & Advance - Transactions</h3>
    <p>Generated on {{ now()->format('d-m-Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>Date</th><th>Transaction No.</th><th>Party/Employee</th><th>Type</th><th>Nature</th>
                <th class="text-end">Amount</th><th class="text-end">Settled</th><th class="text-end">Outstanding</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row->transaction_date->format('d-m-Y') }}</td>
                    <td>{{ $row->transaction_no }}</td>
                    <td>{{ $row->party_display_name }}</td>
                    <td>{{ $row->transaction_type }}</td>
                    <td>{{ $row->transaction_nature }}</td>
                    <td class="text-end">{{ number_format($row->amount, 2) }}</td>
                    <td class="text-end">{{ number_format($row->settled_amount ?? 0, 2) }}</td>
                    <td class="text-end">{{ number_format($row->outstanding, 2) }}</td>
                    <td>{{ $row->computed_status }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center">No data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
