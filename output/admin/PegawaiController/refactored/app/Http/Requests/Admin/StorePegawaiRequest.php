<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StorePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:'.User::class,
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:'.User::class,
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
            'nama' => [
                'required',
                'string',
                'max:255',
            ],
            'telepon' => [
                'required',
                'string',
                'max:20',
            ],
            'alamat' => [
                'required',
                'string',
            ],
            'jabatan' => [
                'required',
                'exists:jabatans,id',
            ],
            'tim_id' => [
                'nullable',
                'exists:tims,id',
            ],
            'tanggal_masuk' => [
                'required',
                'date',
            ],
            'gaji' => [
                'required',
                'numeric',
                'min:0',
            ],
            'nama_bank' => [
                'nullable',
                'string',
                'max:50',
            ],
            'nomor_rekening' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }
}