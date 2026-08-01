<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IndexTugasRequest extends FormRequest
{
    private const FILTERS = [
        'search',
        'bulan',
        'tahun',
        'jabatan_id',
        'tim_id',
        'divisi_id',
        'status',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function filters(): array
    {
        $filters = [];

        foreach (self::FILTERS as $filter) {
            if ($this->filled($filter)) {
                $filters[$filter] = $this->input($filter);
            }
        }

        return $filters;
    }
}