<?php

namespace App\Http\Controllers;

use App\Jobs\ExportContactPlusToEmailPortalJob;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Businesstype;
use App\Models\Contactplus;
use App\Models\ContactPlusExportHistoryAdminFilter;
use App\Models\ExportContactPlusEmailPortalHistory;
use App\Models\Industry;
use App\Services\EmailQamrPortalService;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ContactPlusEmailPortalController extends Controller
{
    protected EmailQamrPortalService $emailQamrPortalService;

    public function __construct(EmailQamrPortalService $emailQamrPortalService)
    {
        $this->emailQamrPortalService = $emailQamrPortalService;
    }

    /**
     * Returns [$isSuperAdmin, $scopeAdminId]. $scopeAdminId is null when the
     * admin may see every Contact Plus record, or their own id when they may
     * only see records where careoff_id = their id (mirrors
     * ContactpController::refinedcontact()).
     */
    private function resolveAccess(): array
    {
        $admin = Auth::guard('admin')->user();

        $isSuperAdmin = $admin->user_type == 1;

        if ($isSuperAdmin) {
            return [true, null];
        }

        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        if (!$permission || (!$permission->full_access && !$permission->view_contactp && !$permission->contactp)) {
            abort(403, 'You do not have permission to access Contact Plus.');
        }

        if ($permission->full_access == 1 || $permission->view_contactp == 1) {
            return [false, null];
        }

        return [false, $admin->id];
    }

    private function positiveIntArray($value): array
    {
        if (blank($value)) {
            return [];
        }

        $items = is_array($value) ? $value : explode(',', (string) $value);

        return array_values(array_unique(array_filter(
            array_map('intval', $items),
            fn ($v) => $v > 0
        )));
    }

    private function assertIdsExist(array $ids, string $table, string $column, string $field): void
    {
        if (empty($ids)) {
            return;
        }

        $found = DB::table($table)->whereIn($column, $ids)->pluck($column)->all();

        if (count($found) !== count($ids)) {
            throw ValidationException::withMessages([
                $field => "One or more selected {$field} values are invalid.",
            ]);
        }
    }

    private function sanitizeScalarOrArray($value, int $limit = 100)
    {
        if (blank($value)) {
            return null;
        }

        if (is_array($value)) {
            return array_slice(array_map('strval', $value), 0, $limit);
        }

        return (string) $value;
    }

    private function buildFilterSnapshot(Request $request, ?int $scopeAdminId): array
    {
        $businesstypeIds = $this->positiveIntArray($request->input('businesstype_id'));
        $industryIds     = $this->positiveIntArray($request->input('industries'));
        $countryIds      = $this->positiveIntArray($request->input('country_id'));
        $cityIds         = $this->positiveIntArray($request->input('city_id'));
        $lcsIds          = $this->positiveIntArray($request->input('lcs_id'));
        $lsIds           = $this->positiveIntArray($request->input('ls_id'));
        $groupIds        = $this->positiveIntArray($request->input('group_id'));
        $createdByIds    = $this->positiveIntArray($request->input('created_by'));

        $this->assertIdsExist($businesstypeIds, 'businesstypes', 'id', 'businesstype_id');
        $this->assertIdsExist($industryIds, 'industries', 'id', 'industries');
        $this->assertIdsExist($countryIds, 'countries', 'id', 'country_id');
        $this->assertIdsExist($cityIds, 'cities', 'id', 'city_id');
        $this->assertIdsExist($lcsIds, 'lifecyclestatuses', 'id', 'lcs_id');
        $this->assertIdsExist($lsIds, 'leadstages', 'id', 'ls_id');
        $this->assertIdsExist($createdByIds, 'admins', 'id', 'created_by');

        return [
            'country_id'               => $countryIds,
            'city_id'                  => $cityIds,
            'lcs_id'                   => $lcsIds,
            'ls_id'                    => $lsIds,
            'businesstype_id'          => $businesstypeIds,
            'industries'               => $industryIds,
            'group_id'                 => $groupIds,
            'created_by'               => $createdByIds,
            'send_tag'                 => $this->sanitizeScalarOrArray($request->input('send_tag')),
            'subscribe'                => $this->sanitizeScalarOrArray($request->input('subscribe')),
            'send_date'                => $request->input('send_date') ? (string) $request->input('send_date') : null,
            'update_lead_status_date'  => $request->input('update_lead_status_date') ? (string) $request->input('update_lead_status_date') : null,
            'created_at'               => $request->input('created_at') ? (string) $request->input('created_at') : null,
            'updated_at'               => $request->input('updated_at') ? (string) $request->input('updated_at') : null,
            'search_text'              => $request->input('search_text') ? (string) $request->input('search_text') : null,
            'scope_admin_id'           => $scopeAdminId,
        ];
    }

    private function countFiltered(array $filters): int
    {
        $query = Contactplus::query()
            ->FilterCountry($filters['country_id'])
            ->FilterCity($filters['city_id'])
            ->FilterLcs($filters['lcs_id'])
            ->FilterLs($filters['ls_id'])
            ->FilterBusinesstype($filters['businesstype_id'])
            ->FilterIndustry($filters['industries'])
            ->FilterGroup($filters['group_id'])
            ->FilterCreatedBy($filters['created_by'])
            ->FilterSendTag($filters['send_tag'])
            ->FilterSubscribe($filters['subscribe'])
            ->FilterDate('send_date', $filters['send_date'])
            ->FilterDateRange('update_lead_status_date', $filters['update_lead_status_date'])
            ->FilterDate('created_at', $filters['created_at'])
            ->FilterDate('updated_at', $filters['updated_at'])
            ->FilterSearchText($filters['search_text']);

        if (!empty($filters['scope_admin_id'])) {
            $query->where('careoff_id', $filters['scope_admin_id']);
        }

        return $query->count();
    }

    /**
     * Saves the whole modal configuration in one request: API token plus
     * Business Type, Industry, Email List, and Custom Field Mappings. This
     * is the only save trigger for the modal - reuses the same admin-level
     * storage the token already used, plus the existing validation helpers
     * already written for export().
     */
    public function saveConfig(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $this->resolveAccess();

        $request->validate([
            'api_token' => 'required|string|max:255',
        ]);

        $businesstypeIds = $this->positiveIntArray($request->input('businesstype_id'));
        $industryIds     = $this->positiveIntArray($request->input('industries'));

        try {
            $this->assertIdsExist($businesstypeIds, 'businesstypes', 'id', 'businesstype_id');
            $this->assertIdsExist($industryIds, 'industries', 'id', 'industries');
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $config = [
            'businesstype_id' => $businesstypeIds[0] ?? null,
            'industries'      => $industryIds,
            'list_uid'        => $request->input('list_uid') ? (string) $request->input('list_uid') : null,
            'list_name'       => $request->input('list_name') ? (string) $request->input('list_name') : null,
            'field_mappings'  => $this->buildFieldMappings($request),
        ];

        $admin->update([
            'email_qamr_api_token'          => $request->input('api_token'),
            'email_qamr_contactplus_config' => $config,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Configuration saved successfully.',
        ]);
    }

    public function fetchLists(Request $request)
    {
        $this->resolveAccess();

        $request->validate([
            'page' => 'nullable|integer|min:1',
        ]);

        $token = Auth::guard('admin')->user()->email_qamr_api_token;

        if (blank($token)) {
            return response()->json([
                'success'  => false,
                'lists'    => [],
                'has_more' => false,
                'message'  => 'Please save your API Token first.',
            ]);
        }

        $result = $this->emailQamrPortalService->getLists(
            $token,
            (int) $request->input('page', 1)
        );

        return response()->json($result);
    }

    public function filteredCount(Request $request)
    {
        [, $scopeAdminId] = $this->resolveAccess();

        try {
            $filters = $this->buildFilterSnapshot($request, $scopeAdminId);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        return response()->json([
            'success' => true,
            'count'   => $this->countFiltered($filters),
        ]);
    }

    /**
     * All columns on the contactpluses table, for the "Contact Plus Field" mapping dropdown.
     */
    public function contactFields()
    {
        $this->resolveAccess();

        $columns = Schema::getColumnListing((new Contactplus())->getTable());
        sort($columns);

        return response()->json([
            'success' => true,
            'fields'  => array_values($columns),
        ]);
    }

    /**
     * Custom fields defined on the selected email.qamr.in list, for the
     * "Email Portal Field" mapping dropdown.
     */
    public function listFields(Request $request)
    {
        $this->resolveAccess();

        $request->validate([
            'list_uid' => 'required|string|max:100',
        ]);

        $token = Auth::guard('admin')->user()->email_qamr_api_token;

        if (blank($token)) {
            return response()->json([
                'success' => false,
                'fields'  => [],
                'message' => 'Please save your API Token first.',
            ]);
        }

        $result = $this->emailQamrPortalService->getListFields($token, $request->input('list_uid'));

        return response()->json($result);
    }

    /**
     * Validates and sanitizes the submitted custom field mapping rows.
     * Frontend-supplied field names are never trusted blindly - each
     * "Contact Plus Field" must be a real column on the contactpluses table,
     * and duplicate "Email Portal Field" keys are dropped.
     */
    private function buildFieldMappings(Request $request): array
    {
        $raw = $request->input('field_mappings', []);

        if (!is_array($raw)) {
            return [];
        }

        $allowedColumns = Schema::getColumnListing((new Contactplus())->getTable());
        $seenKeys = [];
        $mappings = [];

        foreach ($raw as $row) {
            $column = is_array($row) ? ($row['all_contact_field'] ?? null) : null;
            $key    = is_array($row) ? ($row['email_portal_field'] ?? null) : null;

            $column = is_string($column) ? trim($column) : null;
            $key    = is_string($key) ? trim($key) : null;

            if (blank($column) || blank($key)) {
                continue;
            }

            if (!in_array($column, $allowedColumns, true)) {
                continue;
            }

            if (strlen($key) > 100 || isset($seenKeys[$key])) {
                continue;
            }

            $seenKeys[$key] = true;
            $mappings[] = ['field' => $column, 'key' => $key];
        }

        return $mappings;
    }

    public function export(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        [, $scopeAdminId] = $this->resolveAccess();

        $request->validate([
            'list_uid'         => 'required|string|max:100',
            'list_name'        => 'required|string|max:255',
            'businesstype_id'  => 'required',
        ]);

        $token = $admin->email_qamr_api_token;

        if (blank($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Please save your API Token first.',
            ], 422);
        }

        try {
            $filters = $this->buildFilterSnapshot($request, $scopeAdminId);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        if (empty($filters['businesstype_id'])) {
            return response()->json([
                'success' => false,
                'errors'  => ['businesstype_id' => ['Business Type is required.']],
            ], 422);
        }

        $filters['field_mappings'] = $this->buildFieldMappings($request);

        $businesstypeName = $filters['businesstype_id']
            ? Businesstype::whereIn('id', $filters['businesstype_id'])->pluck('name')->implode(', ')
            : null;

        $industryNames = $filters['industries']
            ? Industry::whereIn('id', $filters['industries'])->pluck('name')->implode(', ')
            : null;

        $totalRecords = $this->countFiltered($filters);

        $history = ExportContactPlusEmailPortalHistory::create([
            'admin_id'           => $admin->id,
            'api_token'          => $token,
            'list_uid'           => $request->input('list_uid'),
            'list_name'          => $request->input('list_name'),
            'businesstype_id'    => $filters['businesstype_id'][0] ?? null,
            'businesstype_name'  => $businesstypeName,
            'industry_ids'       => $filters['industries'],
            'industry_names'     => $industryNames,
            'filters'            => $filters,
            'status'             => 'pending',
            'total_records'      => $totalRecords,
            'pending_count'      => $totalRecords,
        ]);

        ExportContactPlusToEmailPortalJob::dispatch($history->id, $admin->id);

        return response()->json([
            'success'      => true,
            'message'      => 'Export started successfully. You can track the progress from Export History.',
            'history_id'   => $history->id,
            'total_records' => $totalRecords,
        ]);
    }

    public function history()
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $admin = Auth::guard('admin')->user();

        $statusOptions = ['pending', 'processing', 'completed', 'completed_with_errors', 'failed'];

        $businessTypeOptions = DB::table('export_contact_plus_email_portal_histories')
            ->whereNotNull('businesstype_name')
            ->where('businesstype_name', '!=', '')
            ->distinct()
            ->orderBy('businesstype_name')
            ->pluck('businesstype_name');

        $adminOptions = $isSuperAdmin
            ? Admin::where('status', 1)->orderBy('name')->get(['id', 'name'])
            : collect();

        $savedFilter = ContactPlusExportHistoryAdminFilter::where('admin_id', $admin->id)->first();

        return view('admin.email_qamr_portal_export_history.index', [
            'isSuperAdmin'        => $isSuperAdmin,
            'statusOptions'       => $statusOptions,
            'businessTypeOptions' => $businessTypeOptions,
            'adminOptions'        => $adminOptions,
            'savedFilter'         => $savedFilter,
            'savedStatuses'       => $savedFilter ? $this->toArrayValue($savedFilter->status) : [],
            'savedAdminIds'       => $savedFilter ? $this->toArrayValue($savedFilter->filter_admin_id) : [],
            'savedBusinessTypes'  => $savedFilter ? $this->toArrayValue($savedFilter->business_type) : [],
        ]);
    }

    /**
     * Normalizes a Select2 multi-select value (already an array) or a
     * comma-joined string (as saved to the database) into a clean array.
     */
    private function toArrayValue($value): array
    {
        if (blank($value)) {
            return [];
        }

        $items = is_array($value) ? $value : explode(',', (string) $value);

        return array_values(array_filter(array_map('trim', $items), fn ($v) => $v !== ''));
    }

    private function formatStatusBadge(string $status): string
    {
        $badges = [
            'pending'                => 'bg-label-warning',
            'processing'             => 'bg-label-info',
            'completed'              => 'bg-label-success',
            'completed_with_errors'  => 'bg-label-secondary',
            'failed'                 => 'bg-label-danger',
        ];

        $labels = [
            'pending'                => 'Pending',
            'processing'             => 'Processing',
            'completed'              => 'Completed',
            'completed_with_errors'  => 'Completed With Errors',
            'failed'                 => 'Failed',
        ];

        $class = $badges[$status] ?? 'bg-label-secondary';
        $label = $labels[$status] ?? $status;

        return '<span class="badge ' . $class . '">' . $label . '</span>';
    }

    private function formatDateOrDash(?string $value): string
    {
        return $value ? Carbon::parse($value)->format('d M Y h:i A') : '---';
    }

    private function formatProgress($row): string
    {
        return $row->success_count . ' / ' . $row->total_records . ' (' . $row->failed_count . ' failed)';
    }

    private function formatErrorMessage(?string $errorMessage): string
    {
        if (blank($errorMessage)) {
            return '---';
        }

        $short = Str::limit($errorMessage, 60);

        return '<span class="text-danger">' . e($short) . '</span> '
            . '<button type="button" class="btn btn-icon btn-xs btn-outline-danger view-error-summary" data-error="' . e($errorMessage) . '" title="View full error">'
            . '<i class="ti ti-eye ti-xs"></i>'
            . '</button>';
    }

    private function formatCheckbox($id): string
    {
        return '
            <div class="form-check form-check-inline">
                <input class="form-check-input dt-checkboxes sub-chk" data-id="' . $id . '" type="checkbox" value="' . $id . '" id="checkbox' . $id . '">
                <label class="form-check-label" for="checkbox' . $id . '"></label>
            </div>
        ';
    }

    private function formatActionDropdown($id): string
    {
        return '
            <div class="dropdown">
                <button class="btn p-0" type="button" id="historyActions' . $id . '" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="historyActions' . $id . '">
                    <a class="dropdown-item reload-email-qamr-export" href="javascript:void(0);" data-id="' . $id . '">
                        <i class="ti ti-refresh me-2"></i> Reload
                    </a>
                    <a class="dropdown-item text-danger delete-email-qamr-export" href="javascript:void(0);" data-id="' . $id . '">
                        <i class="ti ti-trash me-2"></i> Delete
                    </a>
                </div>
            </div>
        ';
    }

    /**
     * Live ajax.data filter params win when present (any filled), otherwise
     * fall back to the admin's saved filter row - same dual-path rule
     * LeadController::jsonData() uses.
     */
    private function resolveHistoryFilters(Request $request, bool $isSuperAdmin): array
    {
        $fields = ['status', 'filter_admin_id', 'list_name', 'business_type', 'created_date'];

        $hasLiveFilter = collect($fields)->contains(fn ($f) => filled($request->input($f)));

        if ($hasLiveFilter) {
            return [
                'status'          => $this->toArrayValue($request->input('status')),
                'filter_admin_id' => $isSuperAdmin ? $this->toArrayValue($request->input('filter_admin_id')) : [],
                'list_name'       => $request->input('list_name'),
                'business_type'   => $this->toArrayValue($request->input('business_type')),
                'created_date'    => $request->input('created_date'),
            ];
        }

        $saved = ContactPlusExportHistoryAdminFilter::where('admin_id', Auth::guard('admin')->user()->id)->first();

        if (!$saved) {
            return [];
        }

        return [
            'status'          => $this->toArrayValue($saved->status),
            'filter_admin_id' => $isSuperAdmin ? $this->toArrayValue($saved->filter_admin_id) : [],
            'list_name'       => $saved->list_name,
            'business_type'   => $this->toArrayValue($saved->business_type),
            'created_date'    => $saved->created_date,
        ];
    }

    private function applyHistoryFilters($query, array $filters): void
    {
        if (!empty($filters['status'])) {
            $query->whereIn('h.status', $filters['status']);
        }

        if (!empty($filters['filter_admin_id'])) {
            $query->whereIn('h.admin_id', $filters['filter_admin_id']);
        }

        if (!empty($filters['list_name'])) {
            $query->where('h.list_name', 'like', '%' . $filters['list_name'] . '%');
        }

        if (!empty($filters['business_type'])) {
            $query->whereIn('h.businesstype_name', $filters['business_type']);
        }

        if (!empty($filters['created_date'])) {
            $parts = array_map('trim', explode('-', $filters['created_date']));

            if (count($parts) === 2 && $parts[0] !== '' && $parts[1] !== '') {
                $start = date('Y-m-d', strtotime($parts[0]));
                $end = date('Y-m-d', strtotime($parts[1]));
                $query->whereBetween('h.created_at', [$start . ' 00:00:00', $end . ' 23:59:59']);
            }
        }
    }

    public function historyJson(Request $request)
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $posts = DB::table('export_contact_plus_email_portal_histories as h')
            ->leftJoin('admins as admin', 'admin.id', '=', 'h.admin_id')
            ->select(
                'h.id', 'h.admin_id', 'h.list_uid', 'h.list_name',
                'h.businesstype_id', 'h.businesstype_name', 'h.industry_ids', 'h.industry_names',
                'h.status', 'h.total_records', 'h.pending_count', 'h.success_count', 'h.failed_count',
                'h.error_message', 'h.started_at', 'h.completed_at', 'h.created_at', 'h.updated_at',
                'admin.name as admin_name'
            );

        if ($isSuperAdmin) {
            $posts->orderByDesc('h.id');
        } else {
            $posts->where('h.admin_id', Auth::guard('admin')->user()->id)->orderByDesc('h.id');
        }

        $this->applyHistoryFilters($posts, $this->resolveHistoryFilters($request, $isSuperAdmin));

        return DataTables::of($posts)

            ->addColumn('checkbox', fn ($row) => $this->formatCheckbox($row->id))

            ->addColumn('progress', fn ($row) => $this->formatProgress($row))

            ->editColumn('status', fn ($row) => $this->formatStatusBadge($row->status))

            ->editColumn('started_at', fn ($row) => $this->formatDateOrDash($row->started_at))

            ->editColumn('completed_at', fn ($row) => $this->formatDateOrDash($row->completed_at))

            ->editColumn('created_at', fn ($row) => $this->formatDateOrDash($row->created_at))

            ->editColumn('error_message', fn ($row) => $this->formatErrorMessage($row->error_message))

            ->addColumn('action', fn ($row) => $this->formatActionDropdown($row->id))

            ->rawColumns(['checkbox', 'status', 'error_message', 'action'])

            ->make(true);
    }

    public function refreshHistoryRow($id)
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $query = DB::table('export_contact_plus_email_portal_histories as h')
            ->leftJoin('admins as admin', 'admin.id', '=', 'h.admin_id')
            ->select(
                'h.id', 'h.admin_id', 'h.list_uid', 'h.list_name',
                'h.businesstype_id', 'h.businesstype_name', 'h.industry_ids', 'h.industry_names',
                'h.status', 'h.total_records', 'h.pending_count', 'h.success_count', 'h.failed_count',
                'h.error_message', 'h.started_at', 'h.completed_at', 'h.created_at', 'h.updated_at',
                'admin.name as admin_name'
            )
            ->where('h.id', $id);

        if (!$isSuperAdmin) {
            $query->where('h.admin_id', Auth::guard('admin')->user()->id);
        }

        $row = $query->first();

        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'row'     => [
                'id'                => $row->id,
                'admin_name'        => $row->admin_name,
                'list_name'         => $row->list_name,
                'list_uid'          => $row->list_uid,
                'businesstype_name' => $row->businesstype_name,
                'industry_names'    => $row->industry_names,
                'total_records'     => $row->total_records,
                'progress'          => $this->formatProgress($row),
                'status'            => $this->formatStatusBadge($row->status),
                'started_at'        => $this->formatDateOrDash($row->started_at),
                'completed_at'      => $this->formatDateOrDash($row->completed_at),
                'error_message'     => $this->formatErrorMessage($row->error_message),
                'created_at'        => $this->formatDateOrDash($row->created_at),
                'checkbox'          => $this->formatCheckbox($row->id),
                'action'            => $this->formatActionDropdown($row->id),
            ],
        ]);
    }

    public function saveExportHistoryFilter(Request $request)
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $adminId = Auth::guard('admin')->user()->id;

        $filter = ContactPlusExportHistoryAdminFilter::firstOrNew(['admin_id' => $adminId]);

        $filter->status          = implode(',', $this->toArrayValue($request->input('status'))) ?: null;
        $filter->filter_admin_id = $isSuperAdmin ? (implode(',', $this->toArrayValue($request->input('filter_admin_id'))) ?: null) : null;
        $filter->list_name       = $request->input('list_name') ?: null;
        $filter->business_type   = implode(',', $this->toArrayValue($request->input('business_type'))) ?: null;
        $filter->created_date    = $request->input('created_date') ?: null;

        $filter->save();

        return response()->json([
            'success' => true,
            'message' => $filter->wasRecentlyCreated ? 'Filter saved successfully!' : 'Filter updated successfully!',
        ]);
    }

    public function resetExportHistoryFilter(Request $request)
    {
        $this->resolveAccess();

        $adminId = Auth::guard('admin')->user()->id;

        $filter = ContactPlusExportHistoryAdminFilter::where('admin_id', $adminId)->first();

        if ($filter) {
            $filter->update([
                'status'          => null,
                'filter_admin_id' => null,
                'list_name'       => null,
                'business_type'   => null,
                'created_date'    => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Filter reset successfully!',
        ]);
    }

    public function deleteHistory($id)
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $query = ExportContactPlusEmailPortalHistory::query();

        if (!$isSuperAdmin) {
            $query->where('admin_id', Auth::guard('admin')->user()->id);
        }

        $export = $query->findOrFail($id);

        $export->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Export history deleted successfully.',
        ]);
    }

    public function bulkDeleteHistory(Request $request)
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $ids = $this->positiveIntArray($request->input('ids'));

        if (empty($ids)) {
            return response()->json([
                'status'  => false,
                'message' => 'No export history records selected.',
            ], 422);
        }

        $query = ExportContactPlusEmailPortalHistory::whereIn('id', $ids);

        if (!$isSuperAdmin) {
            $query->where('admin_id', Auth::guard('admin')->user()->id);
        }

        $deleted = $query->delete();

        return response()->json([
            'status'  => true,
            'message' => $deleted . ' export history record(s) deleted successfully.',
        ]);
    }
}
