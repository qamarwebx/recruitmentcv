<?php

namespace App\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class StoreFolderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:file_manager_items,id'],
            'name' => ['required', 'string', 'max:255'],
            'owner_id' => ['nullable', 'integer', 'exists:admins,id'],
        ];
    }
}
