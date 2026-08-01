<?php

namespace App\Repositories;

use App\Models\Divisi;
use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\MasterPotongan;
use App\Models\MasterTunjangan;
use App\Models\Pegawai;
use App\Models\Tim;
use App\Repositories\Contracts\GajiMassalRepositoryInterface;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentGajiMassalRepository implements GajiMassalRepositoryInterface
{
    public function getFilteredPegawais(array $filters): Collection
    {
        $query = Pegawai::query()->with('jabatan');

        $query->when(
            array_key_exists('search', $filters),
            fn ($query) => $query->where(
                'nama',
                'like',
                '%' . $filters['search'] . '%',
            ),
        );

        $query->when(
            array_key_exists('divisi_id', $filters),
            fn ($query) => $query->whereHas(
                'tim.divisi',
                fn ($subQuery) => $subQuery->where(
                    'id',
                    $filters['divisi_id'],
                ),
            ),
        );

        $query->when(
            array_key_exists('tim_id', $filters),
            fn ($query) => $query->where('tim_id', $filters['tim_id']),
        );

        $query->when(
            array_key_exists('jabatan_id', $filters),
            fn ($query) => $query->where(
                'jabatan_id',
                $filters['jabatan_id'],
            ),
        );

        return $query->orderBy('nama')->get();
    }

    public function getPegawaiBelumGajian(
        int $bulan,
        int $tahun,
        Carbon $tanggalSekarang,
    ): Collection {
        return Pegawai::where('tanggal_masuk', '<=', $tanggalSekarang)
            ->whereDoesntHave('gajis', function ($query) use ($bulan, $tahun) {
                $query->where('bulan', $bulan)
                    ->where('tahun', $tahun);
            })
            ->with(['jabatan', 'tim.divisi'])
            ->orderBy('nama')
            ->get();
    }

    public function getDivisis(): Collection
    {
        return Divisi::orderBy('nama_divisi')->get();
    }

    public function getTims(): Collection
    {
        return Tim::orderBy('nama_tim')->get();
    }

    public function getJabatans(): Collection
    {
        return Jabatan::orderBy('nama_jabatan')->get();
    }

    public function getPegawaisByIds(array $pegawaiIds): Collection
    {
        return Pegawai::whereIn('id', $pegawaiIds)
            ->orderBy('nama')
            ->get();
    }

    public function getMasterTunjangans(): Collection
    {
        return MasterTunjangan::orderBy('nama_tunjangan')->get();
    }

    public function getMasterPotongans(): Collection
    {
        return MasterPotongan::orderBy('nama_potongan')->get();
    }

    public function getPegawaiSudahGajian(
        array $pegawaiIds,
        int $bulan,
        int $tahun,
    ): Collection {
        return Pegawai::whereIn('id', $pegawaiIds)
            ->whereHas('gajis', function ($query) use ($bulan, $tahun) {
                $query->where('bulan', $bulan)
                    ->where('tahun', $tahun);
            })
            ->with(['jabatan', 'tim.divisi'])
            ->get();
    }

    public function createGaji(array $data): Gaji
    {
        return Gaji::create($data);
    }

    public function createTunjanganDetail(Gaji $gaji, array $data): void
    {
        $gaji->tunjanganDetails()->create($data);
    }

    public function createPotonganDetail(Gaji $gaji, array $data): void
    {
        $gaji->potonganDetails()->create($data);
    }

    public function transaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }
}