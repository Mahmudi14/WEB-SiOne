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

            $question = Question::query()
                ->whereKey(
                    $room->current_question_id,
                )
                ->where(
                    'room_id',
                    $room->id,
                )
                ->firstOrFail();

            $activeParticipants = Participant::query()
                ->where(
                    'room_id',
                    $room->id,
                )
                ->where(
                    'status',
                    ParticipantStatus::Active->value,
                )
                ->lockForUpdate()
                ->get();

            $activeParticipantIds =
                $activeParticipants
                ->pluck('id');

            $correctParticipantIds = Answer::query()
                ->where(
                    'room_id',
                    $room->id,
                )
                ->where(
                    'question_id',
                    $question->id,
                )
                ->whereIn(
                    'participant_id',
                    $activeParticipantIds,
                )
                ->where(
                    'is_correct',
                    true,
                )
                ->pluck(
                    'participant_id',
                );

            $correctCount =
                $correctParticipantIds->count();

            if ($correctCount > 0) {
                $eliminatedParticipantIds =
                    $activeParticipantIds
                    ->diff(
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

            $question->update([
                'closed_at' => now(),
            ]);

            $remainingParticipants = Participant::query()
                ->where(
                    'room_id',
                    $room->id,
                )
                ->where(
                    'status',
                    ParticipantStatus::Active->value,
                )
                ->lockForUpdate()
                ->get();

            if (
                $remainingParticipants->count() === 1
            ) {
                Participant::query()
                    ->whereKey(
                        $remainingParticipants
                            ->first()
                            ->id,
                    )
                    ->update([
                        'status' =>
                        ParticipantStatus::Winner->value,
                    ]);

                $room->update([
                    'status' =>
                    RoomStatus::Finished,

                    'game_phase' =>
                    null,

                    'finished_at' =>
                    now(),

                    'revision' =>
                    $room->revision + 1,
                ]);

                return $room->refresh();
            }

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