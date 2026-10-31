<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveGajiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'bulan' => 'required|integer|min:1|max:12',
            'tahun' => 'required|integer|min:2020',
            'gaji_pokok' => 'required|numeric|min:0',
            'tunjangans' => 'nullable|array',
            'tunjangans.*.master_tunjangan_id' => 'required_with:tunjangans|exists:master_tunjangans,id',
            'tunjangans.*.jumlah' => 'required_with:tunjangans|numeric|min:0',
            'potongans' => 'nullable|array',
            'potongans.*.master_potongan_id' => 'required_with:potongans|exists:master_potongans,id',
            'potongans.*.jumlah' => 'required_with:potongans|numeric|min:0',
        ];
    }
}