<?php

namespace App\Repositories\Contracts;

use App\Models\Meeting;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface MeetingRepositoryInterface
{
    public function paginateLatestWithRelations(int $perPage): LengthAwarePaginator;

    public function create(array $data): Meeting;

    public function update(Meeting $meeting, array $data): void;

    public function syncParticipants(Meeting $meeting, array $participantIds): void;

    public function getParticipantIds(Meeting $meeting): array;

    public function delete(Meeting $meeting): void;
}