<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CekGajiPegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pegawai_id' => 'required|exists:pegawais,id',
            'bulan' => 'required|integer',
            'tahun' => 'required|integer',
        ];
    }
}