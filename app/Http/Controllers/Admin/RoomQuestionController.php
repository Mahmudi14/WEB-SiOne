<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class RoomQuestionController extends Controller
{
    private const OPTION_LABELS = [
        'A',
        'B',
        'C',
        'D',
    ];

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreQuestionRequest $request,
        Room $room,
    ): RedirectResponse {
        $this->ensureEditable(
            $room,
        );

        $data = $request->validated();

        DB::transaction(
            function () use (
                $room,
                $data,
            ) {
                $position =
                    ((int) $room
                        ->questions()
                        ->max('position')) + 1;

                $question = $room
                    ->questions()
                    ->create([
                        'text' =>
                        $data['text'],

                        'position' =>
                        $position,

                        'game_order' =>
                        null,

                        'opened_at' =>
                        null,

                        'closed_at' =>
                        null,
                    ]);

                foreach (
                    $data['options']
                    as $index => $option
                ) {
                    $question
                        ->options()
                        ->create([
                            'label' =>
                            self::OPTION_LABELS[$index],

                            'text' =>
                            $option['text'],

                            'is_correct' =>
                            $option['is_correct'],
                        ]);
                }
            },
        );

        return back()->with(
            'success',
            'Soal berhasil ditambahkan.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateQuestionRequest $request,
        Room $room,
        Question $question,
    ): RedirectResponse {
        $this->ensureEditable(
            $room,
        );

        $this->ensureQuestionBelongsToRoom(
            $room,
            $question,
        );

        $data = $request->validated();

        DB::transaction(
            function () use (
                $question,
                $data,
            ) {
                $question->update([
                    'text' =>
                    $data['text'],
                ]);

                /*
                 * Room masih locked,
                 * jadi aman recreate options.
                 */
                $question
                    ->options()
                    ->delete();

                foreach (
                    $data['options']
                    as $index => $option
                ) {
                    $question
                        ->options()
                        ->create([
                            'label' =>
                            self::OPTION_LABELS[$index],

                            'text' =>
                            $option['text'],

                            'is_correct' =>
                            $option['is_correct'],
                        ]);
                }
            },
        );

        return back()->with(
            'success',
            'Soal berhasil diperbarui.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Room $room,
        Question $question,
    ): RedirectResponse {
        $this->ensureEditable(
            $room,
        );

        $this->ensureQuestionBelongsToRoom(
            $room,
            $question,
        );

        DB::transaction(
            function () use (
                $room,
                $question,
            ) {
                $question->delete();

                /*
                 * Rapikan kembali posisi:
                 *
                 * 1, 2, 4
                 * menjadi
                 * 1, 2, 3
                 */
                $questions = $room
                    ->questions()
                    ->orderBy('position')
                    ->get();

                foreach (
                    $questions
                    as $index => $item
                ) {
                    $newPosition =
                        $index + 1;

                    if (
                        $item->position !==
                        $newPosition
                    ) {
                        $item->update([
                            'position' =>
                            $newPosition,
                        ]);
                    }
                }
            },
        );

        return back()->with(
            'success',
            'Soal berhasil dihapus.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guard Room
    |--------------------------------------------------------------------------
    */

    private function ensureEditable(
        Room $room,
    ): void {
        abort_unless(
            $room->status === RoomStatus::Locked,
            422,
            'Soal hanya dapat diubah ketika room masih terkunci.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guard Question
    |--------------------------------------------------------------------------
    */

    private function ensureQuestionBelongsToRoom(
        Room $room,
        Question $question,
    ): void {
        abort_unless(
            (int) $question->room_id ===
                (int) $room->id,
            404,
        );
    }
}