<div class="report-scroll">
    <table class="table table-bordered table-hover table-sm text-center mb-0">
        <thead class="table-dark">
            <tr>
                <th class="sticky-col">Group</th>

                @foreach($users as $user)
                    <th>{{ $user->name }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($data as $row)
                <tr>

                    <td class="sticky-col text-start fw-bold">
                        {{ $row['group'] }}
                    </td>

                    @foreach($users as $user)
                        <td>{{ $row[$user->id] ?: '' }}</td>
                    @endforeach

                </tr>
            @endforeach
        </tbody>
    </table>
</div>