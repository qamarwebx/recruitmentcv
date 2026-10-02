<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Domain;
use App\Models\Basepathstatus;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Log;
class DomainController extends Controller
{

    public function store(Request $request)
    {
        try {

            $id = $request->domain_id;

            $request->validate([
                'partner_id' => 'required|exists:partners,id',

                'domain_name' => [
                    'required',
                    'regex:/^(?!https?:\/\/)([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/',
                    Rule::unique('domains', 'domain_name')->ignore($id),
                ],

            ], [

                'domain_name.required' => 'Domain name is required.',
                'domain_name.unique'   => 'This domain already exists.',
                'domain_name.regex'    => 'Only domain allowed (example: apnafly.com)',

            ]);

            \DB::beginTransaction();

            $domainName = strtolower(trim($request->domain_name));

            /*
            |--------------------------------------------------------------------------
            | Create / Update Domain
            |--------------------------------------------------------------------------
            */

            if ($id) {

                $domain = Domain::lockForUpdate()->findOrFail($id);

                $oldDomain = $domain->domain_name;

                $domain->update([

                    'partner_id' => $request->partner_id,
                    'domain_name' => $domainName,

                ]);

                $message = 'Domain updated successfully';

            } else {

                $domain = Domain::create([

                    'partner_id' => $request->partner_id,
                    'domain_name' => $domainName,
                    'server_ip' => request()->server('SERVER_ADDR'),

                ]);

                $oldDomain = null;

                $message = 'Domain added successfully';

            }

            /*
            |--------------------------------------------------------------------------
            | Execute Shell
            |--------------------------------------------------------------------------
            */

            $output = [];

            $code = 0;

            exec(
                sprintf(
                    'bash %s %s 2>&1',
                    escapeshellarg('/home/u454401052/add-domain.sh'),
                    escapeshellarg($domainName)
                ),
                $output,
                $code
            );

            /*
            |--------------------------------------------------------------------------
            | Shell Failed
            |--------------------------------------------------------------------------
            */

            if ($code !== 0) {

                \DB::rollBack();

                Log::error('Domain Setup Failed', [

                    'domain' => $domainName,
                    'old_domain' => $oldDomain,
                    'code' => $code,
                    'output' => $output,

                ]);

                return response()->json([

                    'success' => false,

                    'message' => implode("\n", $output),

                    'errors' => $output,

                ], 500);

            }

            \DB::commit();

            return response()->json([

                'success' => true,

                'message' => $message,

                'shell_output' => $output,

                'data' => $domain,

            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([

                'success' => false,

                'message' => 'Validation failed.',

                'errors' => $e->errors()

            ], 422);

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('Domain Store Error', [

                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),

            ]);

            return response()->json([

                'success' => false,

                'message' => app()->environment('local')
                    ? $e->getMessage()
                    : 'Something went wrong.',

            ], 500);

        }
    }

    public function generateDns(Request $request)
    {
        $request->validate([
            'domain_name' => [
                'required',
                'regex:/^(?!https?:\/\/)([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/',
            ]
        ]);
    
        $serverIp = env('SERVER_IPV4', '147.79.72.94');
    
        return response()->json([
            'success' => true,
    
            'data' => [
                'domain' => strtolower($request->domain_name),
    
                'dns_records' => [
    
                    [
                        'type'  => 'A',
                        'host'  => '@',
                        'value' => $serverIp,
                        'ttl'   => '14400',
                    ],
    
                    [
                        'type'  => 'CNAME',
                        'host'  => 'www',
                        'value' => '@',
                        'ttl'   => '14400',
                    ]
                ]
            ],
    
            'server_ip' => $serverIp,
        ]);
    }

    public function websitelogoupdt(Request $request, $id)
    {
        Domain::updateOrCreate(
            ['partner_id' => $id],
            ['partner_id' => $id]
        );

        $domain = Domain::where('partner_id', $id)->firstOrFail();

        // =========================
        // 🔹 Sub Domain (Website tab, *.recruitmentcv.com)
        // =========================
        if ($request->has('sub_domain')) {

            $subDomain = strtolower(trim((string) $request->sub_domain));

            $validator = Validator::make(
                ['sub_domain' => $subDomain],
                [
                    'sub_domain' => [
                        'nullable',
                        'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/',
                        Rule::unique('domains', 'sub_domain')->ignore($domain->id),
                    ],
                ],
                [
                    'sub_domain.regex' => 'Subdomain may only contain lowercase letters, numbers and hyphens (no spaces, dots or slashes).',
                    'sub_domain.unique' => 'This subdomain is already taken by another partner.',
                ]
            );

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $domain->sub_domain = $subDomain;
        }

        // ✅ Base path handling (safe)
        $basepathstatus = Basepathstatus::first();

        $uploadPath = ($basepathstatus && $basepathstatus->base_path_status == 1)
            ? base_path('public/admin/assets/images/partner')
            : base_path('public_html/admin/assets/images/partner');

        // ✅ Ensure folder exists
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Same image rules used elsewhere in the project (e.g. TeamMemberPageController)
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxBytes = 2048 * 1024; // 2048 KB

        $decoded = [];

        foreach (['website_logo', 'website_logo_ar'] as $field) {

            $value = $request->input($field);

            if (!$value) {
                continue;
            }

            if (!preg_match('/^data:(image\/[a-zA-Z0-9.+-]+);base64,(.+)$/', $value, $matches)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => [$field => ['Invalid image data.']],
                ], 422);
            }

            if (!in_array(strtolower($matches[1]), $allowedMimes, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => [$field => ['Only JPG, PNG or WEBP images are allowed.']],
                ], 422);
            }

            $binary = base64_decode($matches[2]);

            if ($binary === false || strlen($binary) > $maxBytes) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => [$field => ['Image must not be larger than 2MB.']],
                ], 422);
            }

            $decoded[$field] = $binary;
        }

        // Branding files share one folder with every other partner (and with
        // the RecruitmentCV Partner Portal), so new uploads get a name unique
        // to this domain row (Domain::newBrandingFileName()) and a replaced
        // file is deleted only after saving, and only if no other domain /
        // partner row still references it.
        $replaced = [];

        // =========================
        // 🔹 English Logo Upload
        // =========================
        if (isset($decoded['website_logo'])) {

            $name = $domain->newBrandingFileName('en', 'png');

            file_put_contents($uploadPath.'/'.$name, $decoded['website_logo']);

            if ($domain->website_logo) {
                $replaced[] = $domain->website_logo;
            }
            $domain->website_logo = $name;
        }

        // =========================
        // 🔹 Arabic Logo Upload
        // =========================
        if (isset($decoded['website_logo_ar'])) {

            $name = $domain->newBrandingFileName('ar', 'png');

            file_put_contents($uploadPath.'/'.$name, $decoded['website_logo_ar']);

            if ($domain->website_logo_ar) {
                $replaced[] = $domain->website_logo_ar;
            }
            $domain->website_logo_ar = $name;
        }

        $domain->save();

        foreach ($replaced as $oldFile) {
            Domain::deleteBrandingFileIfUnreferenced($uploadPath, $oldFile);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logos updated successfully',
            'data' => $domain
        ]);
    }

    public function updateAddress(Request $request)
    {
        $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_name_ar' => 'nullable|string|max:255',
            'company_email' => 'nullable|email',
            'company_mobile' => 'nullable|string|max:20',
        ]);
    
        // 🔥 Better: use id (primary key)
        $domain = Domain::where('partner_id', $request->id)->firstOrFail();
    
        $domain->update([
            'company_name' => $request->company_name,
            'company_name_ar' => $request->company_name_ar,
            'company_address' => $request->company_address,
            'company_address_ar' => $request->company_address_ar,
            'company_mobile' => $request->company_mobile,
            'company_email' => $request->company_email,
        ]);
    
        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'data' => $domain
        ]);
    }

    public function delete(Request $request)
    {
        try {

            $request->validate([
                'id' => 'required|exists:domains,id'
            ]);

            $domain = Domain::findOrFail($request->id);

            $domainName = strtolower($domain->domain_name);

            $base = '/home/u762330075/domains';

            /*
            |--------------------------------------------------------------------------
            | Detect Symlink Path
            |--------------------------------------------------------------------------
            */

            if (is_dir("$base/$domainName")) {

                // Custom domain

                $link = "$base/$domainName/public_html";

            } else {

                $parts = explode('.', $domainName);

                $sub = array_shift($parts);

                $parent = implode('.', $parts);

                $link = "$base/$parent/public_html/$sub";

            }

            /*
            |--------------------------------------------------------------------------
            | Remove Symlink Only
            |--------------------------------------------------------------------------
            */

            if (is_link($link)) {

                unlink($link);

            }

            /*
            |--------------------------------------------------------------------------
            | Remove Domain Log
            |--------------------------------------------------------------------------
            */

            $logFile = "$base/qamarhire.com/domains-list.txt";

            if (file_exists($logFile)) {

                $domains = file(
                    $logFile,
                    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
                );

                $domains = array_filter(

                    $domains,

                    fn($item) => trim($item) !== $domainName

                );

                file_put_contents(

                    $logFile,

                    implode("\n", $domains)

                    . "\n"

                );

            }

            /*
            |--------------------------------------------------------------------------
            | Delete DB
            |--------------------------------------------------------------------------
            */

            $domain->delete();

            return response()->json([

                'success' => true,

                'message' => 'Domain removed successfully'

            ]);

        } catch (\Exception $e) {

            \Log::error('Domain Delete Error', [

                'message' => $e->getMessage(),

                'line' => $e->getLine(),

                'file' => $e->getFile()

            ]);

            return response()->json([

                'success' => false,

                'message' => app()->environment('local')

                    ? $e->getMessage()

                    : 'Unable to remove domain'

            ], 500);

        }
    }
}
