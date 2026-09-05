<?php

namespace App\Actions\Game;

use App\Enums\GamePhase;
use App\Enums\ParticipantStatus;
use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class StartGameAction
{
    public function execute(Room $room): Room
    {
        return DB::transaction(function () use ($room) {
            $room = Room::query()
                ->whereKey($room->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $room->status === RoomStatus::Waiting,
                422,
                'Game hanya dapat dimulai ketika room sedang menunggu peserta.',
            );

            $questions = $room
                ->questions()
                ->orderBy('position')
                ->get();

            abort_if(
                $questions->isEmpty(),
                422,
                'Room belum memiliki soal.',
            );

            $participantCount = $room
                ->participants()
                ->where(
                    'status',
                    ParticipantStatus::Waiting->value,
                )
                ->count();

            abort_if(
                $participantCount < 2,
                422,
                'Minimal dua peserta harus bergabung sebelum game dimulai.',
            );

            if ($room->shuffle_questions) {
                $questions = $questions
                    ->shuffle()
                    ->values();
            } else {
                $questions = $questions
                    ->values();
            }

            foreach ($questions as $index => $question) {
                $question->update([
                    'game_order' => $index + 1,
                    'opened_at' => null,
                    'closed_at' => null,
                ]);
            }

            $firstQuestion = $questions->first();

            $room
                ->participants()
                ->where(
                    'status',
                    ParticipantStatus::Waiting->value,
                )
                ->update([
                    'status' =>
                    ParticipantStatus::Active->value,
                ]);

            $room->update([
                'status' => RoomStatus::Running,
                'game_phase' => GamePhase::Ready,
                'current_question_id' =>
                $firstQuestion->id,
                'started_at' => now(),
                'finished_at' => null,
                'revision' =>
                $room->revision + 1,
            ]);

            return $room->refresh();
        });
    }
}