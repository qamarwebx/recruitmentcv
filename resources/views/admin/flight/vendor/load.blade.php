<table class="datatables-users table border-top">
    <thead>
        <tr>
            {{-- <th></th> --}}
            <th>Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Website</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if ($posts->count() > 0)
            @foreach ($posts as $post)
                <tr>
                    {{-- <td></td> --}}
                    <td>{{ $post->name }}</td>
                    <td>@if($post->contact_no != '') {{ $post->contact_no }} @else {{ '---' }} @endif</td>
                    <td>@if($post->email != '') {{ $post->email }} @else {{ '---' }} @endif</td>
                    <td>@if($post->website != '') {{ $post->website }} @else {{ '---' }} @endif</td>
                    <td>
                        <a href="javascript:;" class="text-body delemployer delete-record" data-bs-toggle="modal" data-id="{{ $post->id }}" data-bs-target="#deleteemployer" ><i class="ti ti-trash ti-sm mx-2"></i></a>
                        <a href="javascript:;" class="text-body" data-bs-toggle="offcanvas" data-bs-target="#offcanvasEditUser" data-id="{{ $post->id }}"><i class="ti ti-edit ti-sm"></i></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="5">No Data Found</td>
            </tr>
        @endif

    </tbody>
</table>

<div class="px-4 pt-3">
    <div class="float-start">
        {{ 'Showing '.$posts->firstItem().' to '.$posts->lastItem().' of '.$posts->total().' Entries' }}
    </div>
    <div class="float-end">
        {{ $posts->links('vendor.pagination.bootstrap-4') }}
    </div>
</div>
