<?php

namespace App\Http\Controllers;

use App\Models\Adminpermission;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class HolidayController extends Controller
{
    private function loggedAdmin()
    {
        return Auth::guard('admin')->user();
    }

    private function isSuperAdmin($admin)
    {
        return $admin->user_type == 1;
    }

    /**
     * Same tier as AttendanceController::canViewAll() - Super Admin,
     * full_access, or the dedicated hr_attendance_view_all permission
     * ("HR Management -> View All" in the permission matrix). Holidays are
     * managed through that same Attendance permission flow rather than
     * requiring Super Admin.
     */
    private function canManageHolidays($admin)
    {
        if ($this->isSuperAdmin($admin)) {
            return true;
        }

        $permission = Adminpermission::where('staff_id', $admin->id)->first();

        return optional($permission)->full_access == 1 || optional($permission)->hr_attendance_view_all == 1;
    }

    public function datatable(Request $request)
    {
        $posts = Holiday::query()
            ->FilterDateFrom($request->date_from)
            ->FilterDateTo($request->date_to)
            ->latest('holiday_date');

        return DataTables::of($posts)
            ->filter(function ($query) use ($request) {
                $searchText = data_get($request->all(), 'search.value');
                if (filled($searchText)) {
                    $query->FilterSearchText($searchText);
                }
            })
            ->addColumn('date', fn ($row) => optional($row->holiday_date)->format('d M Y'))
            ->addColumn('day', fn ($row) => optional($row->holiday_date)->format('l'))
            ->addColumn('actions', function ($row) use ($request) {
                $admin = $this->loggedAdmin();

                if (!$this->canManageHolidays($admin)) {
                    return '<span class="text-muted">-</span>';
                }

                return '
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="holidayActions' . $row->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="holidayActions' . $row->id . '">
                            <a class="dropdown-item holiday-edit-trigger" href="javascript:void(0);" data-id="' . $row->id . '">
                                <i class="ti ti-edit me-2"></i> Edit
                            </a>
                            <a class="dropdown-item holiday-delete-trigger text-danger" href="javascript:void(0);" data-id="' . $row->id . '">
                                <i class="ti ti-trash me-2"></i> Delete
                            </a>
                        </div>
                    </div>
                ';
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canManageHolidays($admin)) {
            return response()->json(['res' => 'You are not authorized to manage holidays.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'holiday_date' => 'required|date|unique:holidays,holiday_date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'holiday_date.unique' => 'A holiday is already defined for this date.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Holiday::create([
            'holiday_date' => $request->holiday_date,
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => $admin->id,
        ]);

        return response()->json(['res' => 'Holiday added successfully!']);
    }

    public function edit(Request $request, $id)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canManageHolidays($admin)) {
            return response()->json(['res' => 'You are not authorized to manage holidays.'], 403);
        }

        $holiday = Holiday::find($id);

        if (!$holiday) {
            return response()->json(['res' => 'Holiday not found.'], 404);
        }

        return response()->json([
            'id' => $holiday->id,
            'holiday_date' => $holiday->holiday_date->format('Y-m-d'),
            'name' => $holiday->name,
            'description' => $holiday->description,
        ]);
    }

    public function update(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canManageHolidays($admin)) {
            return response()->json(['res' => 'You are not authorized to manage holidays.'], 403);
        }

        $holiday = Holiday::find($request->id);

        if (!$holiday) {
            return response()->json(['res' => 'Holiday not found.'], 404);
        }

        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:holidays,id',
            'holiday_date' => 'required|date|unique:holidays,holiday_date,' . $holiday->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'holiday_date.unique' => 'A holiday is already defined for this date.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $holiday->holiday_date = $request->holiday_date;
        $holiday->name = $request->name;
        $holiday->description = $request->description;
        $holiday->save();

        return response()->json(['res' => 'Holiday updated successfully!']);
    }

    public function delete(Request $request)
    {
        $admin = $this->loggedAdmin();

        if (!$this->canManageHolidays($admin)) {
            return response()->json(['res' => 'You are not authorized to manage holidays.'], 403);
        }

        $holiday = Holiday::find($request->id);

        if (!$holiday) {
            return response()->json(['res' => 'Holiday not found.'], 404);
        }

        $holiday->delete();

        return response()->json(['res' => 'Holiday deleted successfully!']);
    }
}
