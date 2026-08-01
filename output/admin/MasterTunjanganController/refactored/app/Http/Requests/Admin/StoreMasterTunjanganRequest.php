<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterTunjanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_tunjangan' => 'required|string|max:100|unique:master_tunjangans,nama_tunjangan',
            'jumlah_default' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ];
    }
}