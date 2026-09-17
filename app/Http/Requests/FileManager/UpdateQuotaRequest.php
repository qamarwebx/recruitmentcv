<?php

namespace App\Http\Requests\FileManager;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuotaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'admin_id' => ['required', 'integer', 'exists:admins,id'],
            'allocated_bytes' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,suspended'],
        ];
    }
}
