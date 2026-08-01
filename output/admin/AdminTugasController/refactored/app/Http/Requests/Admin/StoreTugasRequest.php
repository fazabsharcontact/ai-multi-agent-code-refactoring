<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTugasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul_tugas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'penerima_id' => 'required|exists:pegawais,id',
            'tenggat_waktu' => 'required|date',
        ];
    }
}