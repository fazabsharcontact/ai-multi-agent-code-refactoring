<?php

namespace App\Http\Requests\Admin\GajiMassal;

use Illuminate\Foundation\Http\FormRequest;

class SimpanGajiMassalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_gaji' => 'required|array',
            'pegawai_gaji.*.pegawai_id' => 'required|exists:pegawais,id',
            'pegawai_gaji.*.gaji_pokok' => 'required|numeric',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
            'tunjangans' => 'nullable|array',
            'tunjangans.*.master_tunjangan_id' => 'required_with:tunjangans|exists:master_tunjangans,id',
            'tunjangans.*.jumlah' => 'required_with:tunjangans|numeric|min:0',
            'potongans' => 'nullable|array',
            'potongans.*.master_potongan_id' => 'required_with:potongans|exists:master_potongans,id',
            'potongans.*.jumlah' => 'required_with:potongans|numeric|min:0',
        ];
    }
}