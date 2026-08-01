<?php

namespace App\Http\Requests\Admin;

use App\Models\Pegawai;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UpdatePegawaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Pegawai $pegawai */
        $pegawai = $this->route('pegawai');
        $user = $pegawai->user;

        $rules = [
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
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

        if ($this->filled('password')) {
            $rules['password'] = [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ];
        }

        return $rules;
    }
}