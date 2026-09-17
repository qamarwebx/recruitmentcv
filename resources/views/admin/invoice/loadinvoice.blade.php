<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th></th>
            <th>Partner / Employer Name</th>
            <th>Invoice Number</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                @php

                    $badge = match ($post->payment_status) {
                        'Paid' => 'success',
                        'Partially Paid' => 'warning',
                        default => 'danger',
                    };

                @endphp

                <tr>
                    <td></td>
                    <td>{{ $post->partneroffice->rec_off_name }}</td>
                    <td>{{ $post->invoice_no }}</td>
                    <td>{{ $post->invoice_amount }}</td>
                    <td><span class="badge bg-label-{{ $badge }}">{{ $post->payment_status }}</span></td>
                    <td>{{ date('d-m-Y',strtotime($post->invoice_date)) }}</td>
                    <td>
                        <a href="javascript:;" class="text-body deleteinvoice" data-bs-toggle="modal" data-id="{{ $post->id }}" data-bs-target="#deleteinvoice" ><i class="ti ti-trash ti-sm"></i></a>
                        <a href="javascript:;" class="text-body editinvoice" @if($post->payment_status != 'Unpaid') disabled title="Only Unpaid created invoice is editable" @endif data-bs-toggle="offcanvas" data-id="{{ $post->id }}" data-bs-target="#editinvoice" ><i class="ti ti-edit ti-sm"></i></a>
                        <a href="{{ route('admin.invoice.show',$post->id) }}" class="text-body" target="_blank"><i class="ti ti-eye ti-sm "></i></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center">No Invoices Found</td>
            </tr>
        @endif
    </tbody>
</table>

<div class="d-flex justify-content-between align-items-center px-4 pt-3">
    <div>
        {{ __('Showing :start to :end of :total Entries', [
            'start' => $posts->firstItem(),
            'end' => $posts->lastItem(),
            'total' => $posts->total(),
        ]) }}
    </div>
    <div>
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
