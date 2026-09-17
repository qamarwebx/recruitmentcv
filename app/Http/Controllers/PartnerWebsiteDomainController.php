<?php

namespace App\Http\Controllers;

use App\Models\PartnerWebsiteDomain;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Manages the "RecruitmentCV Domain" CRM tab - the *.recruitmentcv.com
 * partner-subdomain branding system. Deliberately independent of
 * DomainController (which owns the pre-existing "Domain"/"Website" tabs
 * and the add-domain.sh symlink-based custom-domain feature): separate
 * table, separate model, separate upload folder. Do not merge these.
 */
class PartnerWebsiteDomainController extends Controller
{
    public function store(Request $request)
    {
        try {
            $id = $request->id;

            $request->validate([
                'partner_id' => 'required|exists:partners,id',

                'domain' => [
                    'required',
                    'regex:/^(?!-)[a-z0-9-]+(?<!-)\.recruitmentcv\.com$/i',
                    Rule::unique('partner_website_domains', 'domain')->ignore($id),
                ],

                'status' => 'nullable|in:active,inactive',
            ], [
                'domain.required' => 'Subdomain is required.',
                'domain.regex' => 'Only a *.recruitmentcv.com subdomain is allowed (example: raha.recruitmentcv.com).',
                'domain.unique' => 'This subdomain is already assigned to a partner.',
            ]);

            DB::beginTransaction();

            $domain = strtolower(trim($request->domain));

            if ($id) {
                $record = PartnerWebsiteDomain::lockForUpdate()->findOrFail($id);

                $record->update([
                    'partner_id' => $request->partner_id,
                    'domain' => $domain,
                    'status' => $request->status ?? $record->status,
                ]);

                $message = 'Domain updated successfully';
            } else {
                $record = PartnerWebsiteDomain::create([
                    'partner_id' => $request->partner_id,
                    'domain' => $domain,
                    'status' => $request->status ?? 'inactive',
                ]);

                $message = 'Domain added successfully';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $record,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('PartnerWebsiteDomain Store Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => app()->environment('local') ? $e->getMessage() : 'Something went wrong.',
            ], 500);
        }
    }

    public function logoUpdate(Request $request, $partnerId)
    {
        $record = PartnerWebsiteDomain::firstOrNew(['partner_id' => $partnerId]);

        if (!$record->exists) {
            return response()->json([
                'success' => false,
                'message' => 'Add and save the subdomain before uploading logos.',
            ], 422);
        }

        $uploadPath = public_path('admin/assets/images/partnerwebsite');

        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        foreach (['english_logo' => 'en', 'arabic_logo' => 'ar'] as $field => $suffix) {
            $image = $request->input($field);

            if (!$image) {
                continue;
            }

            if (strpos($image, ',') !== false) {
                $image = explode(',', $image)[1];
            }

            $decoded = base64_decode($image);

            if ($decoded === false) {
                continue;
            }

            if ($record->{$field} && file_exists($uploadPath . '/' . $record->{$field})) {
                unlink($uploadPath . '/' . $record->{$field});
            }

            $name = time() . '_' . $suffix . '.png';

            file_put_contents($uploadPath . '/' . $name, $decoded);

            $record->{$field} = $name;
        }

        $record->save();

        return response()->json([
            'success' => true,
            'message' => 'Logos updated successfully',
            'data' => $record,
        ]);
    }

    public function delete(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:partner_website_domains,id',
            ]);

            $record = PartnerWebsiteDomain::findOrFail($request->id);

            $uploadPath = public_path('admin/assets/images/partnerwebsite');

            foreach (['english_logo', 'arabic_logo'] as $field) {
                if ($record->{$field} && file_exists($uploadPath . '/' . $record->{$field})) {
                    unlink($uploadPath . '/' . $record->{$field});
                }
            }

            $record->delete();

            return response()->json([
                'success' => true,
                'message' => 'Domain removed successfully',
            ]);
        } catch (\Throwable $e) {
            Log::error('PartnerWebsiteDomain Delete Error', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return response()->json([
                'success' => false,
                'message' => app()->environment('local') ? $e->getMessage() : 'Unable to remove domain',
            ], 500);
        }
    }
}
