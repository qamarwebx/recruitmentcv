<?php

namespace App\Http\Controllers;

use App\Models\AdminDevice;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeviceApprovedNotification;
use Jenssegers\Agent\Agent;
use DB;
use App\Models\Adminpermission;


class AdminDeviceController extends Controller
{
    /**
     * Show grouped device list (latest per admin).
     */
    public function index(Request $request)
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();

        // Allow only super admin or users with access_setting permission
        if ($user->user_type != 1 && (!$permission || !$permission->access_setting)) {
            abort(403, 'Unauthorized action.');
        }

        $search = $request->search;

        // 🔍 Apply search here
        $devices = AdminDevice::select('admin_id', DB::raw('MAX(updated_at) as updated_at'))
            ->with('admin:id,name,username')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('admin', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%");
                });
            })
            ->groupBy('admin_id')
            ->orderByDesc('updated_at')
            ->get();

        $admin = Admin::where('status', 1)->where('user_type', 1)->first();

        // 🔥 If AJAX request → return only table
        if ($request->ajax()) {
            return view('admin.devicecontroller.load', compact('devices'))->render();
        }

        return view('admin.devicecontroller.index', compact('devices', 'permission','admin'));
    }
    

    /**
     * Return all activity (devices) for a given admin — AJAX partial view.
     */
    public function showAdminActivity($adminId)
    {
        $user = Auth::guard('admin')->user();
        $permission = Adminpermission::where('staff_id', $user->id)->first();
        $activities = AdminDevice::where('admin_id', $adminId)
            ->orderByDesc('updated_at')
            ->get()
            // Collapse rows sharing the exact same GPS coordinates down to the
            // most recent one (list is already newest-first, so the first row
            // seen per lat/long pair is the latest). Rows without a captured
            // location aren't "the same", so each of those stays as its own row.
            ->unique(function ($device) {
                if (is_null($device->latitude) || is_null($device->longitude)) {
                    return $device->id;
                }

                return $device->latitude . '|' . $device->longitude;
            })
            ->values();

        return view('admin.devicecontroller.partials.activity_table', compact('activities','permission'));
    }

    /**
     * Approve a device (AJAX response).
     */
    public function approve(Request $request)
    {
        $device = AdminDevice::findOrFail($request->id);
        $approver = Auth::guard('admin')->user();
        $agent = new Agent();
        $ip = $request->ip();

        // Get approver’s city
        $city = 'Unknown';
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,message,city");
            $data = $response ? json_decode($response, true) : null;
            if ($data && $data['status'] === 'success') {
                $city = $data['city'] ?? 'Unknown';
            }
        } catch (\Exception $e) {
            \Log::warning('City fetch failed: '.$e->getMessage());
        }

        // Update approval info
        $device->update([
            'is_approved' => true,
            'approved_by_id' => $approver->id,
            'approved_by_device_type' => $agent->device() ?: 'Unknown',
            'approved_by_browser' => $agent->browser() ?: 'Unknown',
            'approved_by_os' => $agent->platform() ?: 'Unknown',
            'approved_by_ip' => $ip,
            'approved_by_location' => $city,
        ]);

        // Send notification safely
        try {
            Notification::send($device->admin, new DeviceApprovedNotification($device, $agent));
        } catch (\Exception $e) {
            \Log::error('DeviceApprovedNotification failed: '.$e->getMessage());
        }

        return response()->json(['status' => true, 'message' => '✅ Device approved successfully! User has been notified.']);
    }

    /**
     * Revoke device access (AJAX).
     */
    public function revoke(Request $request)
    {
        $device = AdminDevice::findOrFail($request->id);
        $device->update(['is_approved' => false]);

        return response()->json(['status' => true, 'message' => 'Device access revoked!']);
    }

    /**
     * Delete a device (AJAX).
     */
    public function delete(Request $request)
    {
        $device = AdminDevice::findOrFail($request->id);
        $device->delete();

        return response()->json(['status' => true, 'message' => 'Device deleted successfully!']);
    }

    /**
     * Show pending (unapproved) devices.
     */
    public function pending()
    {
        $pendingDevices = AdminDevice::where('is_approved', false)
            ->with('admin:id,username')
            ->orderByDesc('id')
            ->get();

        return view('admin.devicecontroller.pending', compact('pendingDevices'));
    }


    public function toggleApproveAllLogin(Request $request)
    {
        $admin = Admin::where('status', 1)->where('user_type', 1)->first();

        if (!$admin) {
            return response()->json(['status' => false, 'message' => 'Admin not found.'], 404);
        }

        $admin->approve_all_login = $request->approve_all_login == 'true';
        $admin->save();

        return response()->json([
            'status' => true,
            'message' => 'Approve All Login setting updated successfully.',
            'approve_all_login' => $admin->approve_all_login
        ]);
    }

}
