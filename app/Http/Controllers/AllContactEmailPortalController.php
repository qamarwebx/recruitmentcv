<?php

namespace App\Http\Controllers;

use App\Jobs\ExportAllContactToEmailPortalJob;
use App\Models\Admin;
use App\Models\Adminpermission;
use App\Models\Allcontact;
use App\Models\AllContactExportHistoryAdminFilter;
use App\Models\ExportAllContactEmailPortalHistory;
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

class AllContactEmailPortalController extends Controller
{
    protected EmailQamrPortalService $emailQamrPortalService;

    public function __construct(EmailQamrPortalService $emailQamrPortalService)
    {
        $this->emailQamrPortalService = $emailQamrPortalService;
    }

    /**
     * Same request-field => scope-method mapping used by
     * AllContactController::jsonData() so filtering logic isn't duplicated.
     */
    private const FILTER_SCOPES = [
        'country_id'                 => 'FilterCountry',
        'city_id'                    => 'FilterCity',
        'state_id'                   => 'FilterState',
        'lcs_id'                     => 'FilterLcs',
        'ls_id'                      => 'FilterLs',
        'business_type'              => 'FilterBusinesstype',
        'indust_id'                  => 'FilterIndustry',
        'group_id'                   => 'FilterGroup',
        'created_by'                 => 'FilterCreatedBy',
        'lead_priority'              => 'FilterLeadPriority',
        'careoff_id'                 => 'FilterLeadCareoff',
        'country_dial_code'          => 'FilterCountryDialCode',
        'country_dial_code_number'   => 'FilterCountryDialCodeField',
        'exlude_country_mobile_code' => 'FilterExludeCountryMobileCode',
        'conversation_type'          => 'FilterConversationType',
        'owner_id'                   => 'FilterLeadOwner',
        'followup_before'            => 'FilterFollowupBefore',
    ];

    /**
     * Request fields that hold foreign-key ids and the table/column they
     * must exist in. Anything not listed here is treated as an opaque
     * string filter (no FK to validate against).
     */
    private const ID_FIELD_TABLES = [
        'country_id' => ['countries', 'id'],
        'city_id'    => ['cities', 'id'],
        'state_id'   => ['regions', 'id'],
        'lcs_id'     => ['lifecyclestatuses', 'id'],
        'ls_id'      => ['leadstages', 'id'],
        'indust_id'  => ['industries', 'id'],
        'group_id'   => ['groupallcs', 'id'],
        'created_by' => ['admins', 'id'],
        'careoff_id' => ['admins', 'id'],
        'owner_id'   => ['admins', 'id'],
    ];

    /**
     * Returns [$isSuperAdmin, $scopeAdminId]. $scopeAdminId is null when the
     * admin may see every All Contact record, or their own id when they may
     * only see records where careoff_id = their id (mirrors
     * AllContactController::jsonData()).
     */
    private function resolveAccess(): array
    {
        $admin = Auth::guard('admin')->user();

        $isSuperAdmin = $admin->user_type == 1;

        if ($isSuperAdmin) {
            return [true, null];
        }

        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        if (!$permission || (!$permission->full_access && !$permission->allcontact_view && !$permission->allcontact)) {
            abort(403, 'You do not have permission to access All Contact.');
        }

        if ($permission->full_access == 1 || $permission->allcontact_view == 1) {
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

    private function stringArray($value, int $limit = 50): array
    {
        if (blank($value)) {
            return [];
        }

        $items = is_array($value) ? $value : [$value];

        return array_values(array_unique(array_slice(
            array_filter(array_map(fn ($v) => trim((string) $v), $items), fn ($v) => $v !== ''),
            0,
            $limit
        )));
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

    private function assertBusinessTypesExist(array $values): void
    {
        if (empty($values)) {
            return;
        }

        $found = Allcontact::query()
            ->whereIn('lead_type', $values)
            ->distinct()
            ->pluck('lead_type')
            ->all();

        if (count($found) !== count($values)) {
            throw ValidationException::withMessages([
                'business_type' => 'One or more selected Business Type values are invalid.',
            ]);
        }
    }

    private function buildFilterSnapshot(Request $request, ?int $scopeAdminId): array
    {
        $filters = [];

        foreach (self::FILTER_SCOPES as $field => $scope) {

            if ($field === 'business_type') {
                $values = $this->stringArray($request->input('business_type'));
                $this->assertBusinessTypesExist($values);
                $filters['business_type'] = $values;
                continue;
            }

            if (isset(self::ID_FIELD_TABLES[$field])) {
                [$table, $column] = self::ID_FIELD_TABLES[$field];
                $ids = $this->positiveIntArray($request->input($field));
                $this->assertIdsExist($ids, $table, $column, $field);
                $filters[$field] = $ids;
                continue;
            }

            $filters[$field] = $this->sanitizeScalarOrArray($request->input($field));
        }

        $filters['created_date']        = $request->input('created_date') ? (string) $request->input('created_date') : null;
        $filters['updated_date']        = $request->input('updated_date') ? (string) $request->input('updated_date') : null;
        $filters['staff_updated_date']  = $request->input('staff_updated_date') ? (string) $request->input('staff_updated_date') : null;
        $filters['search_text']         = $request->input('search_text') ? (string) $request->input('search_text') : null;
        $filters['scope_admin_id']      = $scopeAdminId;

        return $filters;
    }

    private function countFiltered(array $filters): int
    {
        $query = Allcontact::query();

        foreach (self::FILTER_SCOPES as $field => $scope) {
            $value = $filters[$field] ?? null;

            if (filled($value)) {
                $query->{$scope}($value);
            }
        }

        if (!empty($filters['created_date'])) {
            $query->FilterDateRange('created_at', $filters['created_date']);
        }

        if (!empty($filters['updated_date'])) {
            $query->FilterDateRange('updated_at', $filters['updated_date']);
        }

        if (!empty($filters['staff_updated_date'])) {
            $query->FilterDateRange('staff_updated_date', $filters['staff_updated_date']);
        }

        if (!empty($filters['search_text'])) {
            $query->FilterSearchText($filters['search_text']);
        }

        if (!empty($filters['scope_admin_id'])) {
            $query->where('careoff_id', $filters['scope_admin_id']);
        }

        return $query->count();
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

        $businessTypes = $this->stringArray($request->input('business_type'));
        $industryIds   = $this->positiveIntArray($request->input('indust_id'));

        try {
            $this->assertBusinessTypesExist($businessTypes);
            $this->assertIdsExist($industryIds, 'industries', 'id', 'indust_id');
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $config = [
            'business_type'  => $businessTypes[0] ?? null,
            'indust_id'      => $industryIds,
            'list_uid'       => $request->input('list_uid') ? (string) $request->input('list_uid') : null,
            'list_name'      => $request->input('list_name') ? (string) $request->input('list_name') : null,
            'field_mappings' => $this->buildFieldMappings($request),
        ];

        $admin->update([
            'email_qamr_api_token'         => $request->input('api_token'),
            'email_qamr_allcontact_config' => $config,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Configuration saved successfully.',
        ]);
    }

    /**
     * All columns on the allcontacts table, for the "All Contact Field" mapping dropdown.
     */
    public function contactFields()
    {
        $this->resolveAccess();

        $columns = Schema::getColumnListing((new Allcontact())->getTable());
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
     * "All Contact Field" must be a real column on the allcontacts table,
     * and duplicate "Email Portal Field" keys are dropped.
     */
    private function buildFieldMappings(Request $request): array
    {
        $raw = $request->input('field_mappings', []);

        if (!is_array($raw)) {
            return [];
        }

        $allowedColumns = Schema::getColumnListing((new Allcontact())->getTable());
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
            'list_uid'      => 'required|string|max:100',
            'list_name'     => 'required|string|max:255',
            'business_type' => 'required',
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

        if (empty($filters['business_type'])) {
            return response()->json([
                'success' => false,
                'errors'  => ['business_type' => ['Business Type is required.']],
            ], 422);
        }

        $filters['field_mappings'] = $this->buildFieldMappings($request);

        $industryNames = !empty($filters['indust_id'])
            ? Industry::whereIn('id', $filters['indust_id'])->pluck('name')->implode(', ')
            : null;

        $totalRecords = $this->countFiltered($filters);

        $history = ExportAllContactEmailPortalHistory::create([
            'admin_id'          => $admin->id,
            'api_token'         => $token,
            'list_uid'          => $request->input('list_uid'),
            'list_name'         => $request->input('list_name'),
            'business_type'     => implode(', ', $filters['business_type']),
            'industry_ids'      => $filters['indust_id'],
            'industry_names'    => $industryNames,
            'filters'           => $filters,
            'status'            => 'pending',
            'total_records'     => $totalRecords,
            'pending_count'     => $totalRecords,
        ]);

        ExportAllContactToEmailPortalJob::dispatch($history->id, $admin->id);

        return response()->json([
            'success'       => true,
            'message'       => 'Export started successfully. You can track the progress from Export History.',
            'history_id'    => $history->id,
            'total_records' => $totalRecords,
        ]);
    }

    public function history()
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $admin = Auth::guard('admin')->user();

        $statusOptions = ['pending', 'processing', 'completed', 'completed_with_errors', 'failed'];

        $businessTypeOptions = DB::table('export_all_contact_email_portal_histories')
            ->whereNotNull('business_type')
            ->where('business_type', '!=', '')
            ->distinct()
            ->orderBy('business_type')
            ->pluck('business_type');

        $adminOptions = $isSuperAdmin
            ? Admin::where('status', 1)->orderBy('name')->get(['id', 'name'])
            : collect();

        $savedFilter = AllContactExportHistoryAdminFilter::where('admin_id', $admin->id)->first();

        return view('admin.allcontact_email_portal_export_history.index', [
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

        $saved = AllContactExportHistoryAdminFilter::where('admin_id', Auth::guard('admin')->user()->id)->first();

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
            $query->whereIn('h.business_type', $filters['business_type']);
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

        $posts = DB::table('export_all_contact_email_portal_histories as h')
            ->leftJoin('admins as admin', 'admin.id', '=', 'h.admin_id')
            ->select(
                'h.id', 'h.admin_id', 'h.list_uid', 'h.list_name',
                'h.business_type', 'h.industry_ids', 'h.industry_names',
                'h.status', 'h.total_records', 'h.pending_count', 'h.success_count', 'h.failed_count',
                'h.error_message', 'h.started_at', 'h.completed_at', 'h.last_refreshed_at',
                'h.created_at', 'h.updated_at',
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

            ->editColumn('last_refreshed_at', fn ($row) => $this->formatDateOrDash($row->last_refreshed_at))

            ->editColumn('created_at', fn ($row) => $this->formatDateOrDash($row->created_at))

            ->editColumn('error_message', fn ($row) => $this->formatErrorMessage($row->error_message))

            ->addColumn('action', fn ($row) => $this->formatActionDropdown($row->id))

            ->rawColumns(['checkbox', 'status', 'error_message', 'action'])

            ->make(true);
    }

    public function refreshHistoryRow($id)
    {
        [$isSuperAdmin] = $this->resolveAccess();

        $query = ExportAllContactEmailPortalHistory::query()->where('id', $id);

        if (!$isSuperAdmin) {
            $query->where('admin_id', Auth::guard('admin')->user()->id);
        }

        $export = $query->first();

        if (!$export) {
            return response()->json(['success' => false, 'message' => 'Record not found.'], 404);
        }

        $export->update(['last_refreshed_at' => now()]);

        $row = DB::table('export_all_contact_email_portal_histories as h')
            ->leftJoin('admins as admin', 'admin.id', '=', 'h.admin_id')
            ->select(
                'h.id', 'h.admin_id', 'h.list_uid', 'h.list_name',
                'h.business_type', 'h.industry_ids', 'h.industry_names',
                'h.status', 'h.total_records', 'h.pending_count', 'h.success_count', 'h.failed_count',
                'h.error_message', 'h.started_at', 'h.completed_at', 'h.last_refreshed_at',
                'h.created_at', 'h.updated_at',
                'admin.name as admin_name'
            )
            ->where('h.id', $id)
            ->first();

        return response()->json([
            'success' => true,
            'row'     => [
                'id'                => $row->id,
                'admin_name'        => $row->admin_name,
                'list_name'         => $row->list_name,
                'list_uid'          => $row->list_uid,
                'business_type'     => $row->business_type,
                'industry_names'    => $row->industry_names,
                'total_records'     => $row->total_records,
                'progress'          => $this->formatProgress($row),
                'status'            => $this->formatStatusBadge($row->status),
                'started_at'        => $this->formatDateOrDash($row->started_at),
                'completed_at'      => $this->formatDateOrDash($row->completed_at),
                'last_refreshed_at' => $this->formatDateOrDash($row->last_refreshed_at),
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

        $filter = AllContactExportHistoryAdminFilter::firstOrNew(['admin_id' => $adminId]);

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

        $filter = AllContactExportHistoryAdminFilter::where('admin_id', $adminId)->first();

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

        $query = ExportAllContactEmailPortalHistory::query();

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

        $query = ExportAllContactEmailPortalHistory::whereIn('id', $ids);

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
