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
            /*
             * Lock row room agar perubahan state
             * tidak diproses bersamaan.
             */
            $room = Room::query()
                ->whereKey($room->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * Game hanya boleh dimulai
             * dari status WAITING.
             */
            abort_unless(
                $room->status === RoomStatus::Waiting,
                422,
                'Game hanya dapat dimulai ketika room sedang menunggu peserta.',
            );

            /*
             * Ambil semua soal berdasarkan
             * urutan konfigurasi.
             */
            $questions = $room
                ->questions()
                ->orderBy('position')
                ->get();

            abort_if(
                $questions->isEmpty(),
                422,
                'Room belum memiliki soal.',
            );

            /*
             * Pastikan sudah ada peserta.
             */
            $participantCount = $room
                ->participants()
                ->where(
                    'status',
                    ParticipantStatus::Waiting->value,
                )
                ->count();

            abort_if(
                $participantCount === 0,
                422,
                'Minimal satu peserta harus bergabung sebelum game dimulai.',
            );

            /*
             * Tentukan urutan game.
             *
             * Jika shuffle aktif, acak satu kali
             * pada saat game dimulai.
             */
            if ($room->shuffle_questions) {
                $questions = $questions
                    ->shuffle()
                    ->values();
            } else {
                $questions = $questions
                    ->values();
            }

            /*
             * Simpan urutan aktual permainan.
             */
            foreach ($questions as $index => $question) {
                $question->update([
                    'game_order' => $index + 1,
                    'opened_at' => null,
                    'closed_at' => null,
                ]);
            }

            /*
             * Siapkan soal pertama.
             *
             * Soal ini belum dikirim ke peserta
             * karena fase masih READY.
             */
            $firstQuestion = $questions->first();

            /*
             * Semua peserta waiting menjadi active.
             */
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

            /*
             * Update state room.
             */
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
