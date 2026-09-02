<?php

namespace App\Actions\Game;

use App\Enums\GamePhase;
use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class OpenQuestionAction
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
                'Game belum berjalan.',
            );

            abort_unless(
                $room->game_phase === GamePhase::Ready,
                422,
                'Soal hanya dapat dibuka ketika berada pada fase siap.',
            );

            abort_unless(
                $room->current_question_id !== null,
                422,
                'Tidak ada soal yang siap dibuka.',
            );

            $question = $room
                ->questions()
                ->whereKey($room->current_question_id)
                ->first();

            abort_unless(
                $question,
                422,
                'Soal aktif tidak ditemukan.',
            );

            $question->update([
                'opened_at' => now(),
                'closed_at' => null,
            ]);

            $room->update([
                'game_phase' => GamePhase::Open,
                'revision' => $room->revision + 1,
            ]);

            return $room->refresh();
        });
    }
}
