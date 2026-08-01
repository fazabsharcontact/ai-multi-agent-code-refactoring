<?php

namespace App\Data;

use Illuminate\Http\Request;

final readonly class GajiMassalFilterData
{
    public function __construct(
        public bool $shouldFilter,
        public array $inputs,
        public array $filters,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $filters = [];

        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
        }

        if ($request->filled('divisi_id')) {
            $filters['divisi_id'] = $request->input('divisi_id');
        }

        if ($request->filled('tim_id')) {
            $filters['tim_id'] = $request->input('tim_id');
        }

        if ($request->filled('jabatan_id')) {
            $filters['jabatan_id'] = $request->input('jabatan_id');
        }

        return new self(
            shouldFilter: $request->has('filter'),
            inputs: $request->all(),
            filters: $filters,
        );
    }
}