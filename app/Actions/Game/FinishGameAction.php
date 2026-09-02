<?php

namespace App\Actions\Game;

use App\Enums\ParticipantStatus;
use App\Enums\RoomStatus;
use App\Models\Participant;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

class FinishGameAction
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

            /*
            |--------------------------------------------------------------------------
            | Winners
            |--------------------------------------------------------------------------
            |
            | Semua peserta yang masih aktif ketika
            | admin menutup game dianggap sebagai pemenang.
            |
            */

            Participant::query()
                ->where(
                    'room_id',
                    $room->id,
                )
                ->where(
                    'status',
                    ParticipantStatus::Active->value,
                )
                ->update([
                    'status' =>
                    ParticipantStatus::Winner->value,
                ]);

            /*
            |--------------------------------------------------------------------------
            | Finish Room
            |--------------------------------------------------------------------------
            */

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
        });
    }
}