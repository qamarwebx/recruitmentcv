<?php

namespace App\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class MoveItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id' => ['required', 'integer', 'exists:file_manager_items,id'],
            'parent_id' => ['nullable', 'integer', 'exists:file_manager_items,id'],
        ];
    }
}
