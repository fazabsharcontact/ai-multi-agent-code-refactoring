<?php

namespace App\Http\Requests\Admin;

use App\Models\MasterPotongan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMasterPotonganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var MasterPotongan $masterPotongan */
        $masterPotongan = $this->route('masterPotongan');

        return [
            'nama_potongan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('master_potongans')->ignore($masterPotongan->id),
            ],
            'jumlah_default' => 'nullable|numeric|min:0',
            'deskripsi' => 'nullable|string',
        ];
    }
}