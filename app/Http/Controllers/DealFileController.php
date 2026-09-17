<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DealFile;
use App\Models\Basepathstatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;


class DealFileController extends Controller
{
    // Upload file
    public function store(Request $request)
    {
        try {
            $request->validate([
                'deal_id' => 'required|exists:deal_pipeline,id',
                'file_upload' => 'required|file|max:2048',
                'file_name' => 'required|string|max:255',
            ], [
                'deal_id.required' => 'Deal ID is required.',
                'deal_id.exists' => 'Selected deal does not exist.',
                'file_upload.required' => 'Please upload a file.',
                'file_upload.max' => 'File must not exceed 2MB.',
                'file_name.required' => 'File name is required.',
            ]);

            $uploadedFile = $request->file('file_upload');
            $originalName = $uploadedFile->getClientOriginalName();
            $filenameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = pathinfo($originalName, PATHINFO_EXTENSION);

            $cleanFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filenameOnly);
            $finalFilename = $cleanFilename . '_' . time() . '.' . $extension;

            $basepathstatus = Basepathstatus::first();

            $destinationPath = $basepathstatus->base_path_status == 1
            ? base_path() . '/public/admin/assets/deal_files'
            : base_path() . '/public_html/admin/assets/deal_files';

            $uploadedFile->move($destinationPath, $finalFilename);

            $dealFile = DealFile::create([
                'deal_id' => $request->deal_id,
                'uploaded_by' => auth()->id(),
                'file_name' => $request->file_name,
                'file_path' => $finalFilename, // just store filename
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'File uploaded successfully',
                'data' => [
                    'id' => $dealFile->id,
                    'file_name' => $dealFile->file_name,
                    'uploaded_by_name' => auth()->user()->name,
                    'created_at' => $dealFile->created_at->format('Y-m-d h:i A'),
                    'download_url' => route('admin.dealfiles.download', $dealFile->id),
                    'delete_url' => route('admin.dealfiles.destroy', $dealFile->id),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors(),
                'message' => 'Validation failed',
            ], 422);
        }
    }

    // List files
    public function list(Request $request)
    {
        $files = DealFile::where('deal_id', $request->deal_id)
                    ->with('uploader')
                    ->orderBy('created_at', 'desc')
                    ->get();
                    

    
        return response()->json($files->map(function($file) {
    
            $extension = strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION)); // ✅
            $basepathstatus = Basepathstatus::first();

            return [
                'id' => $file->id,
                'file_name' => $file->file_name,
                'extension' => $extension, // 👈 send to frontend
                'uploader' => $file->uploader ?? null,
                'created_at' => $file->created_at->format('Y-m-d h:i A'),
                'file_url' => $basepathstatus->base_path_status == 1
                ? asset('admin/assets/deal_files/' . $file->file_path)
                : url('admin/assets/deal_files/' . $file->file_path),
                'download_url' => route('admin.dealfiles.download', $file->id),
                'delete_url' => route('admin.dealfiles.destroy', $file->id),
            ];
        }));
    }
    
    // Download file
    public function download($id)
    {
        $file = DealFile::findOrFail($id);
    
        // Full path to stored file
        $basepathstatus = Basepathstatus::first();

        $filePath = $basepathstatus->base_path_status == 1
        ? base_path() . '/public/admin/assets/deal_files/' . $file->file_path
        : base_path() . '/public_html/admin/assets/deal_files/' . $file->file_path;
    
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
    
        // Extract original extension from stored file path
        $extension = pathinfo($file->file_path, PATHINFO_EXTENSION);
    
        // Build download name: file_name + extension
        $downloadName = $file->file_name . '.' . $extension;
    
        return response()->download($filePath, $downloadName);
    }
    

    // Delete file
    public function destroy($id)
    {
        
        $file = DealFile::findOrFail($id);

        $basepathstatus = Basepathstatus::first();
        
        $filePath = $basepathstatus->base_path_status == 1
        ? base_path() . '/public/admin/assets/deal_files/' . $file->file_path
        : base_path() . '/public_html/admin/assets/deal_files/' . $file->file_path;
    
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $file->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'File deleted successfully',
        ]);
    }
}
