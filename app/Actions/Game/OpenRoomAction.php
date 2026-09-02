<?php

namespace App\Actions\Game;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class OpenRoomAction
{
    public function execute(Room $room): Room
    {
        return DB::transaction(function () use ($room) {
            $room = Room::query()
                ->whereKey($room->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $room->status === RoomStatus::Locked,
                422,
                'Room hanya dapat dibuka ketika masih terkunci.',
            );

            abort_if(
                $room->questions()->count() === 0,
                422,
                'Room harus memiliki minimal satu soal sebelum dibuka.',
            );

            $room->update([
                'status' => RoomStatus::Waiting,
                'game_phase' => null,
                'current_question_id' => null,
                'revision' => $room->revision + 1,
            ]);

            return $room->refresh();
        });
    }
}
