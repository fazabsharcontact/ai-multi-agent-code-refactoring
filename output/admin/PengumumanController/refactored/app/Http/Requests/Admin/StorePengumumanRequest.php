<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePengumumanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'target_type' => 'required|in:semua,divisi,tim,jabatan,pegawai',
            'target_ids' => 'nullable|array',
            'target_ids.*' => 'integer',
        ];
    }

    public function messages(): array
    {
        return [
            'target_ids.required_if' => 'Pilihan target harus diisi.',
        ];
    }
}