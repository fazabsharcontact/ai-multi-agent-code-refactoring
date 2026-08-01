<?php

namespace App\Repositories;

use App\Models\Meeting;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentMeetingRepository implements MeetingRepositoryInterface
{
    public function paginateLatestWithRelations(int $perPage): LengthAwarePaginator
    {
        return Meeting::with('pembuat')
            ->withCount('pesertas')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Meeting
    {
        return Meeting::create($data);
    }

    public function update(Meeting $meeting, array $data): void
    {
        $meeting->update($data);
    }

    public function syncParticipants(Meeting $meeting, array $participantIds): void
    {
        $meeting->pesertas()->sync($participantIds);
    }

    public function getParticipantIds(Meeting $meeting): array
    {
        return $meeting->pesertas
            ->pluck('id')
            ->toArray();
    }

    public function delete(Meeting $meeting): void
    {
        $meeting->delete();
    }
}