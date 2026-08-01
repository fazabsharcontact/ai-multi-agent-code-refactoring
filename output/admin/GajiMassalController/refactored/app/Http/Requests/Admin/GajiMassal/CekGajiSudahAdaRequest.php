<?php

namespace App\Http\Requests\Admin\GajiMassal;

use Illuminate\Foundation\Http\FormRequest;

class CekGajiSudahAdaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_ids' => 'required|array',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ];
    }
}