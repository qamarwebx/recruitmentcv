<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="listitem">
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                @php
                    $states = ['danger', 'success'];
                    $status_title = ['Inactive','Active'];
                @endphp
                <tr>
                    <td>{{ $post->name }}</td>

                    <td>
                        <span class="badge bg-label-{{ $states[$post->status] }}">{{ $status_title[$post->status] }}</span>
                    </td>

                    <td>
                        <a href="#" data-bs-target="#updateReg" data-id="{{ $post->id }}" data-bs-toggle="offcanvas" class="text-body"><i  class="ti ti-edit ti-sm"></i></a>
                        <a href="#" data-bs-target="#deletescon" data-bs-toggle="modal" data-id="{{ $post->id }}" class="text-body"><i class="ti ti-trash ti-sm"></i></a>

                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="10" class="text-center">No Data Found</td>
            </tr>
        @endif
    </tbody>
</table>
{{-- <div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div> --}}
