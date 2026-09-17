<?php

namespace App\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class UploadFileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $maxKb = (int) ceil(config('file-manager.max_upload_size') / 1024);
        $maxBatch = (int) config('file-manager.max_batch_files');

        return [
            'parent_id' => ['nullable', 'integer', 'exists:file_manager_items,id'],
            'owner_id' => ['nullable', 'integer', 'exists:admins,id'],
            'files' => ['required', 'array', 'min:1', "max:{$maxBatch}"],
            'files.*' => ['required', 'file', "max:{$maxKb}"],
        ];
    }
}
