<?php

namespace App\Services;

use App\Models\Meeting;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use App\Repositories\Contracts\PegawaiRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MeetingService
{
    public function __construct(
        private readonly MeetingRepositoryInterface $meetingRepository,
        private readonly PegawaiRepositoryInterface $pegawaiRepository,
    ) {
    }

    public function getPaginatedMeetings(): LengthAwarePaginator
    {
        return $this->meetingRepository->paginateLatestWithRelations(10);
    }

    public function getPegawais(): Collection
    {
        return $this->pegawaiRepository->getAllOrderedByName();
    }

    public function getParticipantIds(Meeting $meeting): array
    {
        return $this->meetingRepository->getParticipantIds($meeting);
    }

    public function createMeeting(array $validated): void
    {
        DB::transaction(function () use ($validated): void {
            $meeting = $this->meetingRepository->create(
                $this->extractMeetingData($validated)
            );

            $this->meetingRepository->syncParticipants(
                $meeting,
                $validated['peserta_ids']
            );
        });
    }

    public function updateMeeting(Meeting $meeting, array $validated): void
    {
        DB::transaction(function () use ($meeting, $validated): void {
            $this->meetingRepository->update(
                $meeting,
                $this->extractMeetingData($validated)
            );

            $this->meetingRepository->syncParticipants(
                $meeting,
                $validated['peserta_ids']
            );
        });
    }

    public function deleteMeeting(Meeting $meeting): void
    {
        $this->meetingRepository->delete($meeting);
    }

    private function extractMeetingData(array $validated): array
    {
        return [
            'judul' => $validated['judul'],
            'deskripsi' => $validated['deskripsi'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'lokasi' => $validated['lokasi'],
            'pembuat_id' => $validated['pembuat_id'],
        ];
    }
}