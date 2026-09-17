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
    <h3>Fund & Advance - Settlement Report</h3>
    <p>Generated on {{ now()->format('d-m-Y H:i') }}</p>
    <table>
        <thead>
            <tr>
                <th>Date</th><th>Settlement No.</th><th>Original Transaction</th><th>Party/Employee</th>
                <th class="text-end">Original Amount</th><th class="text-end">Settlement Amount</th><th>Mode</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    <td>{{ $row->settlement_date->format('d-m-Y') }}</td>
                    <td>{{ $row->settlement_no }}</td>
                    <td>{{ optional($row->transaction)->transaction_no }}</td>
                    <td>{{ optional($row->transaction)->party_display_name }}</td>
                    <td class="text-end">{{ number_format(optional($row->transaction)->amount, 2) }}</td>
                    <td class="text-end">{{ number_format($row->amount, 2) }}</td>
                    <td>{{ $row->payment_mode }}</td>
                    <td>{{ $row->status }}</td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center">No data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
