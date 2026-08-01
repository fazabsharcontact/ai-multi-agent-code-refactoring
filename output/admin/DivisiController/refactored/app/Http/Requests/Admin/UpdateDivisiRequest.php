<?php

namespace App\Http\Requests\Admin;

use App\Models\Divisi;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDivisiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Divisi $divisi */
        $divisi = $this->route('divisi');

        return [
            'nama_divisi' => [
                'required',
                'string',
                'max:100',
                Rule::unique('divisis')->ignore($divisi->id),
            ],
        ];
    }
}