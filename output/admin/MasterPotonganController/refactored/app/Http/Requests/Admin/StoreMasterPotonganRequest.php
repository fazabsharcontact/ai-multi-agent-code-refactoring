<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMasterPotonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_potongan' => 'required|string|max:100|unique:master_potongans,nama_potongan',
            'jumlah_default' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ];
    }
}