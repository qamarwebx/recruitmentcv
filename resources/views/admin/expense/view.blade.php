<table class="table table-bordered">
    <tbody>
        <tr>
            <th width="200">Expense For</th>
            <td>{{ $expense->expensefor->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>Name</th>
            <td>{{ $expense->name }}</td>
        </tr>

        <tr>
            <th>Category</th>
            <td>{{ $expense->expensecat->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>Amount</th>
            <td>₹ {{ number_format($expense->amount, 2) }}</td>
        </tr>

        <tr>
            <th>Paid To</th>
            <td>{{ $expense->paid_to ?? '-' }}</td>
        </tr>

        <tr>
            <th>Expense Date</th>
            <td>{{ $expense->expense_date }}</td>
        </tr>

        <tr>
            <th>Payment Mode</th>
            <td>{{ $expense->payment_mode ?? '-' }}</td>
        </tr>

        <tr>
            <th>Payment From</th>
            <td>{{ $expense->payment_from ?? '-' }}</td>
        </tr>

        <tr>
            <th>Transaction No</th>
            <td>{{ $expense->reference_no ?? '-' }}</td>
        </tr>

        <tr>
            <th>Tax</th>
            <td>{{ $expense->tax ?? '-' }}</td>
        </tr>

        <tr>
            <th>Subtract Tax</th>
            <td>{{ $expense->subtract_tax ? 'Yes' : 'No' }}</td>
        </tr>

        <tr>
            <th>Notes</th>
            <td>{{ $expense->notes ?? '-' }}</td>
        </tr>

        <tr>
            <th>Receipts</th>
            <td>
                @php
                    $files = [];
                    if (!empty($expense->receipt_file)) {
                        $files = json_decode($expense->receipt_file, true) ?? [];
                    }
                @endphp

                @if (!empty($files))
                    @foreach ($files as $file)

                        @php
                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $filePath = asset('admin/assets/images/payment/expense/'.$file);
                        @endphp

                        {{-- IMAGE --}}
                        @if (in_array($ext, ['jpg','jpeg','png','gif','webp']))
                            <img src="{{ $filePath }}"
                                class="img-fluid mb-2"
                                style="max-height:100px;">
                        @endif

                        {{-- PDF --}}
                        @if ($ext === 'pdf')
                            <a target="_blank" href="{{ $filePath }}">
                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                View PDF
                            </a>
                        @endif

                        <br>
                        <a target="_blank"
                        href="{{ $filePath }}"
                        class="btn btn-sm btn-primary mt-1 mb-3">
                            Download
                        </a>

                        <hr>
                    @endforeach
                @else
                    <span class="text-muted">No receipt uploaded</span>
                @endif
            </td>
        </tr>

    </tbody>
</table>
