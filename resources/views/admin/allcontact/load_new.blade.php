<table class="datatables-users table border-top">
    <thead>
        <tr>
            <th>
                <div class="form-check form-check-inline">
                    <input class="form-check-input checkboxSelectAll" type="checkbox" id="checkboxSelectAll" />
                    <label class="form-check-label" for="checkboxSelectAll"></label>
                </div>
            </th>
            <th>Name</th>
            @if (Auth::guard('admin')->user()->user_type == 1 || (isset($permission) && $permission->full_access == 1) || (isset($permission) && $permission->allcontact_company_column == 1))
                <th>Company</th>
            @endif
            <th>Business</th>
            <th>Stage</th>
            <th>Priority</th>
            <th>Opt In</th>
            <th>Group</th>
            <th>Careoff</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody class="listitem">
      
    </tbody>
</table>


