<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Meeting\StoreMeetingRequest;
use App\Http\Requests\Admin\Meeting\UpdateMeetingRequest;
use App\Models\Meeting;
use App\Services\MeetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MeetingController extends Controller
{
    public function __construct(
        private readonly MeetingService $meetingService,
    ) {
    }

    public function index(): View
    {
        $meetings = $this->meetingService->getPaginatedMeetings();

        return view('admin.meeting.index', compact('meetings'));
    }

    public function create(): View
    {
        $pegawais = $this->meetingService->getPegawais();

        return view('admin.meeting.create', compact('pegawais'));
    }

    public function store(StoreMeetingRequest $request): RedirectResponse
    {
        $this->meetingService->createMeeting($request->validated());

        return redirect()->route('admin.meeting.index')
            ->with('success', 'Meeting baru berhasil dijadwalkan.');
    }

    public function edit(Meeting $meeting): View
    {
        $pegawais = $this->meetingService->getPegawais();
        $pesertaIds = $this->meetingService->getParticipantIds($meeting);

        return view(
            'admin.meeting.edit',
            compact('meeting', 'pegawais', 'pesertaIds')
        );
    }

    public function update(
        UpdateMeetingRequest $request,
        Meeting $meeting
    ): RedirectResponse {
        $this->meetingService->updateMeeting(
            $meeting,
            $request->validated()
        );

        return redirect()->route('admin.meeting.index')
            ->with('success', 'Data meeting berhasil diperbarui.');
    }

    public function destroy(Meeting $meeting): RedirectResponse
    {
        $this->meetingService->deleteMeeting($meeting);

        return redirect()->route('admin.meeting.index')
            ->with('success', 'Meeting berhasil dihapus.');
    }
}