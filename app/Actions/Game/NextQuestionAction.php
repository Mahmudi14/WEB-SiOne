<?php

namespace App\Actions\Game;

use App\Enums\GamePhase;
use App\Enums\ParticipantStatus;
use App\Enums\RoomStatus;
use App\Models\Question;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class NextQuestionAction
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
                $room->game_phase === GamePhase::Result,
                422,
                'Soal harus ditutup terlebih dahulu.',
            );

            $activeParticipantCount = $room
                ->participants()
                ->where(
                    'status',
                    ParticipantStatus::Active->value,
                )
                ->count();

            abort_if(
                $activeParticipantCount <= 1,
                422,
                'Soal berikutnya tidak dapat dilanjutkan karena pemenang sudah ditentukan.',
            );

            abort_unless(
                $room->current_question_id !== null,
                422,
                'Tidak ada soal aktif.',
            );

            $currentQuestion = Question::query()
                ->whereKey(
                    $room->current_question_id,
                )
                ->where(
                    'room_id',
                    $room->id,
                )
                ->firstOrFail();

            $nextQuestion = Question::query()
                ->where(
                    'room_id',
                    $room->id,
                )
                ->whereNotNull(
                    'game_order',
                )
                ->where(
                    'game_order',
                    '>',
                    $currentQuestion->game_order,
                )
                ->orderBy(
                    'game_order',
                )
                ->first();

            abort_unless(
                $nextQuestion,
                422,
                'Tidak ada soal berikutnya. Silakan tutup game.',
            );

            $room->update([
                'current_question_id' =>
                $nextQuestion->id,

                'game_phase' =>
                GamePhase::Ready,

                'revision' =>
                $room->revision + 1,
            ]);

            return $room->refresh();
        });
    }
}