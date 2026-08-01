<?php

namespace App\Http\Requests\Admin\GajiMassal;

use Illuminate\Foundation\Http\FormRequest;

class LangkahDuaGajiMassalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_ids' => 'required|array|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'pegawai_ids.required' => 'Anda harus memilih setidaknya satu pegawai.',
        ];
    }
}