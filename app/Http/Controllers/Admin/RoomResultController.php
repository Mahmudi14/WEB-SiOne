<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ParticipantStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Inertia\Inertia;
use Inertia\Response;

class RoomResultController extends Controller
{
    public function show(Room $room): Response
    {
        abort_unless(
            $room->status === RoomStatus::Finished,
            422,
            'Hasil pertandingan hanya tersedia setelah game selesai.',
        );

        /*
        |--------------------------------------------------------------------------
        | Participants
        |--------------------------------------------------------------------------
        */

        $participantCount = $room
            ->participants()
            ->count();

        $winners = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Winner->value,
            )
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'code',
            ]);

        $eliminatedParticipants = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Eliminated->value,
            )
            ->with([
                'eliminatedQuestion:id,text,game_order',
            ])
            ->orderBy('eliminated_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Used Questions
        |--------------------------------------------------------------------------
        |
        | Hanya soal yang benar-benar sudah ditutup
        | yang masuk ke riwayat pertandingan.
        |
        */

        $questions = $room
            ->questions()
            ->whereNotNull('game_order')
            ->whereNotNull('closed_at')
            ->orderBy('game_order')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Question History
        |--------------------------------------------------------------------------
        */

        $participantsBeforeRound =
            $participantCount;

        $questionHistory = [];

        foreach ($questions as $question) {
            $answeredCount = $room
                ->answers()
                ->where(
                    'question_id',
                    $question->id,
                )
                ->count();

            $correctCount = $room
                ->answers()
                ->where(
                    'question_id',
                    $question->id,
                )
                ->where(
                    'is_correct',
                    true,
                )
                ->count();

            $wrongCount = $room
                ->answers()
                ->where(
                    'question_id',
                    $question->id,
                )
                ->where(
                    'is_correct',
                    false,
                )
                ->count();

            $eliminatedCount = $room
                ->participants()
                ->where(
                    'eliminated_question_id',
                    $question->id,
                )
                ->count();

            /*
             * Peserta yang tidak mengirim jawaban.
             */
            $noAnswerCount = max(
                0,
                $participantsBeforeRound -
                    $answeredCount,
            );

            /*
             * Rule kita:
             *
             * correctCount = 0
             * → no_elimination
             *
             * correctCount > 0
             * → normal
             */
            $roundResult =
                $correctCount === 0
                ? 'no_elimination'
                : 'normal';

            $remainingCount = max(
                0,
                $participantsBeforeRound -
                    $eliminatedCount,
            );

            $questionHistory[] = [
                'id' =>
                $question->id,

                'number' =>
                $question->game_order,

                'text' =>
                $question->text,

                'round_result' =>
                $roundResult,

                'participants_before' =>
                $participantsBeforeRound,

                'answered_count' =>
                $answeredCount,

                'correct_count' =>
                $correctCount,

                'wrong_count' =>
                $wrongCount,

                'no_answer_count' =>
                $noAnswerCount,

                'eliminated_count' =>
                $eliminatedCount,

                'remaining_count' =>
                $remainingCount,

                'opened_at' =>
                $question->opened_at
                    ?->toISOString(),

                'closed_at' =>
                $question->closed_at
                    ?->toISOString(),
            ];

            $participantsBeforeRound =
                $remainingCount;
        }

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return Inertia::render(
            'admin/rooms/Result',
            [
                'room' => [
                    'id' =>
                    $room->id,

                    'name' =>
                    $room->name,

                    'code' =>
                    $room->code,

                    'description' =>
                    $room->description,

                    'status' =>
                    $room->status->value,

                    'status_label' =>
                    $room->status->label(),

                    'started_at' =>
                    $room->started_at
                        ?->toISOString(),

                    'finished_at' =>
                    $room->finished_at
                        ?->toISOString(),
                ],

                'summary' => [
                    'participant_count' =>
                    $participantCount,

                    'winner_count' =>
                    $winners->count(),

                    'eliminated_count' =>
                    $eliminatedParticipants
                        ->count(),

                    'questions_used' =>
                    count(
                        $questionHistory,
                    ),
                ],

                'winners' =>
                $winners
                    ->map(
                        fn($participant) => [
                            'id' =>
                            $participant->id,

                            'name' =>
                            $participant->name,

                            'code' =>
                            $participant->code,
                        ],
                    )
                    ->values()
                    ->all(),

                'eliminated_participants' =>
                $eliminatedParticipants
                    ->map(
                        fn($participant) => [
                            'id' =>
                            $participant->id,

                            'name' =>
                            $participant->name,

                            'code' =>
                            $participant->code,

                            'eliminated_at_question' =>
                            $participant
                                ->eliminatedQuestion
                                ? [
                                    'number' =>
                                    $participant
                                        ->eliminatedQuestion
                                        ->game_order,

                                    'text' =>
                                    $participant
                                        ->eliminatedQuestion
                                        ->text,
                                ]
                                : null,
                        ],
                    )
                    ->values()
                    ->all(),

                'questions' =>
                $questionHistory,
            ],
        );
    }
}