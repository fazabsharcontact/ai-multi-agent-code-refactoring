<?php

namespace App\Http\Requests\Admin\Meeting;

use Illuminate\Foundation\Http\FormRequest;

abstract class MeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'lokasi' => 'required|string|max:255',
            'pembuat_id' => 'required|exists:pegawais,id',
            'peserta_ids' => 'required|array',
            'peserta_ids.*' => 'exists:pegawais,id',
        ];
    }
}