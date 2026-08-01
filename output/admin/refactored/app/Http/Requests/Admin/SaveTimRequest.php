<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveTimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_tim' => 'required|string|max:100',
            'divisi_id' => 'required|exists:divisis,id',
        ];
    }
}