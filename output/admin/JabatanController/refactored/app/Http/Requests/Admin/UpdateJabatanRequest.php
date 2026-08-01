<?php

namespace App\Http\Requests\Admin;

use App\Models\Jabatan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJabatanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Jabatan $jabatan */
        $jabatan = $this->route('jabatan');

        return [
            'nama_jabatan' => [
                'required',
                Rule::unique('jabatans')->ignore($jabatan->id),
            ],
            'gaji_awal' => 'required|numeric|min:0',
        ];
    }
}