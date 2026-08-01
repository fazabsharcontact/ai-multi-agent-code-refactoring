<?php

namespace App\Http\Requests\Admin;

use App\Models\MasterTunjangan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMasterTunjanganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var MasterTunjangan $masterTunjangan */
        $masterTunjangan = $this->route('masterTunjangan');

        return [
            'nama_tunjangan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('master_tunjangans')->ignore($masterTunjangan->id),
            ],
            'jumlah_default' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ];
    }
}