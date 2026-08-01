<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_jabatan' => 'required|unique:jabatans,nama_jabatan',
            'gaji_awal' => 'required|numeric|min:0',
        ];
    }
}