<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th></th>
            <th>Date</th>
            <th>Partner</th>
            <th>Amount</th>
            <th>Payment Mode</th>
            <th>Transaction ID</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                <tr>
                    <td></td>
                    <td>{{ date('d-m-Y',strtotime($post->payment_date)) }}</td>
                    <td>
                        @if(!empty($post->payment_slip))
                            <a href="javascript:void(0);"
                            class="text-primary viewPaymentSlip"
                            data-slip="{{ asset('admin/assets/images/payment/slip/' . $post->payment_slip) }}">
                                {{ $post->partneroffice->rec_off_name }}
                            </a>
                        @else
                            {{ $post->partneroffice->rec_off_name }}
                        @endif
                    </td>


                    <td>{{ $post->amount }}</td>
                    <td>{{ $post->payment_mode }}</td>
                    <td>{{ $post->transaction_id }}</td>

                    <td>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                            <div class="dropdown-menu dropdown-menu-end m-0">
                                <a href="javascript:;" class="dropdown-item editpayment" data-bs-toggle="offcanvas" data-id="{{ $post->id }}" data-bs-target="#editpayment">Edit</a>
                                <a href="javascript:;" class="dropdown-item deletepayment" data-bs-toggle="modal" data-id="{{ $post->id }}" data-bs-target="#deletepayment">Delete</a>
                                @if(!empty($post->payment_slip))
                                <a href="{{ '/admin/assets/images/payment/slip/' . $post->payment_slip }}" target="_blank" class="dropdown-item">View Payment Slip</a>
                                @endif
                            </div>
                        </div>
                    </td>
                  
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7" class="text-center">No Data Found</td>
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
