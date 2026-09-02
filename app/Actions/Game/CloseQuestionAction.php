<?php

namespace App\Actions\Game;

use App\Enums\GamePhase;
use App\Enums\ParticipantStatus;
use App\Enums\RoomStatus;
use App\Models\Answer;
use App\Models\Participant;
use App\Models\Question;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class CloseQuestionAction
{
    public function execute(Room $room): Room
    {
        return DB::transaction(function () use ($room) {
            /*
            |--------------------------------------------------------------------------
            | Lock Room
            |--------------------------------------------------------------------------
            */

            $room = Room::query()
                ->whereKey($room->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $room->status === RoomStatus::Running,
                422,
                'Game tidak sedang berjalan.',
            );

            abort_unless(
                $room->game_phase === GamePhase::Open,
                422,
                'Soal tidak sedang dibuka.',
            );

            abort_unless(
                $room->current_question_id !== null,
                422,
                'Tidak ada soal aktif.',
            );

            /*
            |--------------------------------------------------------------------------
            | Current Question
            |--------------------------------------------------------------------------
            */

            $question = Question::query()
                ->whereKey($room->current_question_id)
                ->where('room_id', $room->id)
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Active Participants
            |--------------------------------------------------------------------------
            |
            | Ini adalah peserta yang masih bertahan SEBELUM soal ditutup.
            |
            */

            $activeParticipants = Participant::query()
                ->where('room_id', $room->id)
                ->where(
                    'status',
                    ParticipantStatus::Active->value,
                )
                ->lockForUpdate()
                ->get();

            $activeParticipantIds =
                $activeParticipants->pluck('id');

            /*
            |--------------------------------------------------------------------------
            | Correct Participants
            |--------------------------------------------------------------------------
            */

            $correctParticipantIds = Answer::query()
                ->where('room_id', $room->id)
                ->where(
                    'question_id',
                    $question->id,
                )
                ->whereIn(
                    'participant_id',
                    $activeParticipantIds,
                )
                ->where('is_correct', true)
                ->pluck('participant_id');

            $correctCount =
                $correctParticipantIds->count();

            /*
            |--------------------------------------------------------------------------
            | Elimination Rule
            |--------------------------------------------------------------------------
            |
            | RULE RANKING SATU:
            |
            | correct_count > 0
            |     → hanya yang benar bertahan
            |
            | correct_count = 0
            |     → TIDAK ADA ELIMINASI
            |     → semua peserta aktif tetap bertahan
            |
            */

            if ($correctCount > 0) {
                $eliminatedParticipantIds =
                    $activeParticipantIds->diff(
                        $correctParticipantIds,
                    );

                if (
                    $eliminatedParticipantIds
                    ->isNotEmpty()
                ) {
                    Participant::query()
                        ->whereIn(
                            'id',
                            $eliminatedParticipantIds,
                        )
                        ->update([
                            'status' =>
                            ParticipantStatus::Eliminated->value,

                            'eliminated_question_id' =>
                            $question->id,

                            'eliminated_at' =>
                            now(),
                        ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Close Question
            |--------------------------------------------------------------------------
            */

            $question->update([
                'closed_at' => now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | Result Phase
            |--------------------------------------------------------------------------
            */

            $room->update([
                'game_phase' =>
                GamePhase::Result,

                'revision' =>
                $room->revision + 1,
            ]);

            return $room->refresh();
        });
    }
}