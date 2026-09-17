<div class="card-datatable table-responsive">

    <table class="datatables-leads table border-top" id="leadTable">

        <thead>
            <tr>

                <th>
                    <div class="form-check form-check-inline">
                        <input
                            class="form-check-input checkboxSelectAll"
                            type="checkbox"
                            id="checkboxSelectAll">

                        <label
                            class="form-check-label"
                            for="checkboxSelectAll">
                        </label>
                    </div>
                </th>

                <th>Name</th>

                @if (
                Auth::guard('admin')->user()->user_type == 1 ||
                (isset($permission) && $permission->full_access == 1) ||
                (isset($permission) && $permission->leads_company_column == 1)
                )
                <th>Company</th>
                @endif

                <th>Mobile No</th>
                <th>Whatsapp No</th>
                <th>Job Title</th>
                <th>Experience</th>
                <th>License</th>
                <th>Expected</th>
                <th>Expected</th>
                <th>Message</th>
                <th>Date</th>
                <th>Assign To</th>
                {{-- <th>Is Qualified</th> --}}
                <th>Status</th>
                <th>Actions</th>

            </tr>
        </thead>

        <tbody class="listitem"></tbody>

    </table>

</div>