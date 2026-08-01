<?php

namespace App\Repositories;

use App\Models\Gaji;
use App\Models\Jabatan;
use App\Models\Meeting;
use App\Models\Pegawai;
use App\Models\Pengumuman;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use DateTimeInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentDashboardRepository implements DashboardRepositoryInterface
{
    public function countPegawai(): int
    {
        return Pegawai::count();
    }

    public function getPeriodeGajiTerbaru(): ?object
    {
        return Gaji::select('tahun', 'bulan')
            ->orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->first();
    }

    public function getTotalGajiByPeriode(mixed $tahun, mixed $bulan): mixed
    {
        return Gaji::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->sum('gaji_bersih');
    }

    public function getPegawaiPerJabatan(): array
    {
        return Jabatan::withCount('pegawais')
            ->pluck('pegawais_count', 'nama_jabatan')
            ->toArray();
    }

    public function getAktivitasTerbaru(
        DateTimeInterface $jangkauanWaktu,
        int $limit = 5
    ): Collection {
        $pegawaiBaru = DB::table('pegawais')
            ->where('created_at', '>=', $jangkauanWaktu)
            ->select(
                'created_at as waktu',
                DB::raw("CONCAT('New Employee Added: <b>', nama, '</b>') as keterangan")
            );

        $kehadiran = DB::table('kehadirans as k')
            ->join('pegawais as p', 'k.pegawai_id', '=', 'p.id')
            ->where('k.created_at', '>=', $jangkauanWaktu)
            ->select(
                'k.created_at as waktu',
                DB::raw("CONCAT('Kehadiran: <b>', p.nama, '</b> (', k.status, ')') as keterangan")
            );

        $gaji = DB::table('gajis as g')
            ->join('pegawais as p', 'g.pegawai_id', '=', 'p.id')
            ->where('g.created_at', '>=', $jangkauanWaktu)
            ->select(
                'g.created_at as waktu',
                DB::raw("CONCAT('Gaji dibuat: <b>', p.nama, '</b> (Rp ', FORMAT(g.gaji_bersih, 0, 'id_ID'), ',-)') as keterangan")
            );

        $pegawaiUpdate = DB::table('pegawais as p')
            ->join('jabatans as j', 'p.jabatan_id', '=', 'j.id')
            ->where('p.updated_at', '>=', $jangkauanWaktu)
            ->whereColumn('p.updated_at', '!=', 'p.created_at')
            ->select(
                'p.updated_at as waktu',
                DB::raw("CONCAT('Data Pegawai diperbarui: <b>', p.nama, '</b> (Jabatan: <b>', j.nama_jabatan, '</b>)') as keterangan")
            );

        return $pegawaiBaru
            ->unionAll($kehadiran)
            ->unionAll($gaji)
            ->unionAll($pegawaiUpdate)
            ->orderBy('waktu', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPengumumans(int $perPage = 10): LengthAwarePaginator
    {
        return Pengumuman::with('pembuat')
            ->latest()
            ->paginate($perPage);
    }

    public function getMeetings(int $perPage = 10): LengthAwarePaginator
    {
        return Meeting::with('pembuat')
            ->withCount('pesertas')
            ->latest()
            ->paginate($perPage);
    }
}