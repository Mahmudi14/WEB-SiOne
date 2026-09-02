<?php

namespace App\Actions\Room;

use App\Enums\RoomStatus;
use App\Models\Room;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DuplicateRoomAction
{
    public function execute(Room $sourceRoom): Room
    {
        return DB::transaction(function () use ($sourceRoom) {
            /*
            |--------------------------------------------------------------------------
            | Reload Source Room
            |--------------------------------------------------------------------------
            */

            $sourceRoom = Room::query()
                ->whereKey($sourceRoom->id)
                ->with([
                    'questions' => fn($query) =>
                    $query->orderBy('position'),

                    'questions.options' => fn($query) =>
                    $query->orderBy('label'),
                ])
                ->firstOrFail();

            /*
            |--------------------------------------------------------------------------
            | Create New Room
            |--------------------------------------------------------------------------
            |
            | Yang dicopy:
            | - nama
            | - deskripsi
            | - shuffle_questions
            |
            | Yang TIDAK dicopy:
            | - status pertandingan
            | - current question
            | - revision
            | - waktu game
            | - peserta
            | - answers
            |
            */

            $newRoom = Room::create([
                'name' =>
                $this->generateName(
                    $sourceRoom->name,
                ),

                'code' =>
                $this->generateUniqueCode(
                    $sourceRoom->code,
                ),

                'description' =>
                $sourceRoom->description,

                'status' =>
                RoomStatus::Locked,

                'shuffle_questions' =>
                $sourceRoom->shuffle_questions,

                'game_phase' =>
                null,

                'current_question_id' =>
                null,

                'revision' =>
                0,

                'started_at' =>
                null,

                'finished_at' =>
                null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Copy Questions
            |--------------------------------------------------------------------------
            */

            foreach (
                $sourceRoom->questions
                as $sourceQuestion
            ) {
                $newQuestion = $newRoom
                    ->questions()
                    ->create([
                        'text' =>
                        $sourceQuestion->text,

                        /*
                         * Gunakan posisi konfigurasi,
                         * bukan game_order runtime.
                         */
                        'position' =>
                        $sourceQuestion->position,

                        'game_order' =>
                        null,

                        'opened_at' =>
                        null,

                        'closed_at' =>
                        null,
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Copy Question Options
                |--------------------------------------------------------------------------
                */

                foreach (
                    $sourceQuestion->options
                    as $sourceOption
                ) {
                    $newQuestion
                        ->options()
                        ->create([
                            'label' =>
                            $sourceOption->label,

                            'text' =>
                            $sourceOption->text,

                            'is_correct' =>
                            $sourceOption
                                ->is_correct,
                        ]);
                }
            }

            return $newRoom->refresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Name
    |--------------------------------------------------------------------------
    */

    private function generateName(
        string $sourceName,
    ): string {
        /*
         * Supaya nama tidak terlalu panjang.
         */
        $name = Str::limit(
            $sourceName,
            230,
            '',
        );

        return $name . ' - Salinan';
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Unique Room Code
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | BPS2026
    |
    | menjadi:
    |
    | BPS2026-4F7K2P
    |
    */

    private function generateUniqueCode(
        string $sourceCode,
    ): string {
        do {
            /*
             * rooms.code maksimum 20 karakter.
             *
             * 13 + "-" + 6 = 20
             */
            $prefix = substr(
                strtoupper($sourceCode),
                0,
                13,
            );

            $suffix = strtoupper(
                Str::random(6),
            );

            $code =
                $prefix .
                '-' .
                $suffix;
        } while (
            Room::query()
            ->where('code', $code)
            ->exists()
        );

        return $code;
    }
}