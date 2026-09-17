<?php

namespace App\Http\Controllers;

use App\Models\AdminDevice;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use App\Models\Admin;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DeviceApprovedNotification;

class AdminDeviceApprovalController extends Controller
{
    public function showApprovalForm(AdminDevice $device)
    {
        $admin = Admin::select('id','user_type','login_access_email')->where('user_type', 1)->first();
        return view('admin.devices.approval_form', compact('device','admin'));
    }

    public function handleApproval(Request $request, AdminDevice $device)
    {
        $request->validate([
            'approval_status' => 'required|in:approved,rejected',
            'comments' => 'required|string|max:500',
        ]);

        $agent = new Agent();

        $ip = $request->ip();

         // ✅ Get approver’s city (safe API call, no caching)
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

        // Update device approval status
        $device->update([
            'is_approved' => $request->approval_status === 'approved',
            'approved_by_id' => auth()->id(),
            'approved_by_device_type' => $agent->device() ?: 'Unknown',
            'approved_by_browser' => $agent->browser() ?: 'Unknown',
            'approved_by_os' => $agent->platform() ?: 'Unknown',
            'approved_by_ip' => $request->ip(),
            'approved_by_location' => $city,
            'comments' => $request->comments,
        ]); 

        if($request->approval_status === 'approved'){
            // Notify the admin about approval
            try {
                Notification::send($device->admin, new DeviceApprovedNotification($device, $agent));
            } catch (\Exception $e) {
                \Log::error('DeviceApprovedNotification failed: '.$e->getMessage());
            }

        } 

        return redirect()->route('admin.device.approval.form', $device->id)
                         ->with('success', 'Device approval status updated successfully.');
    }
}
