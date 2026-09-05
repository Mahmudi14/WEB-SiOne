<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Game\CloseQuestionAction;
use App\Actions\Game\FinishGameAction;
use App\Actions\Game\NextQuestionAction;
use App\Actions\Game\OpenQuestionAction;
use App\Actions\Game\OpenRoomAction;
use App\Actions\Game\StartGameAction;
use App\Enums\GamePhase;
use App\Enums\ParticipantStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoomGameController extends Controller
{
    public function show(Room $room): Response
    {
        $room->load([
            'currentQuestion.options',
        ]);

        return Inertia::render(
            'admin/rooms/Play',
            [
                'room' =>
                $this->roomData($room),
            ],
        );
    }

    public function state(Room $room): JsonResponse
    {
        $participantCount = $room
            ->participants()
            ->count();

        $activeParticipantCount = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Active->value,
            )
            ->count();

        $eliminatedParticipantCount = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Eliminated->value,
            )
            ->count();

        $winnerCount = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Winner->value,
            )
            ->count();

        $answeredCount = 0;
        $waitingAnswerCount = 0;

        if (
            $room->status === RoomStatus::Running &&
            $room->game_phase === GamePhase::Open &&
            $room->current_question_id
        ) {
            $answeredCount = $room
                ->answers()
                ->where(
                    'question_id',
                    $room->current_question_id,
                )
                ->count();

            $waitingAnswerCount = max(
                0,
                $activeParticipantCount -
                    $answeredCount,
            );
        }

        return response()->json([
            'revision' =>
            $room->revision,

            'status' =>
            $room->status->value,

            'game_phase' =>
            $room->game_phase?->value,

            'participant_count' =>
            $participantCount,

            'active_participant_count' =>
            $activeParticipantCount,

            'eliminated_participant_count' =>
            $eliminatedParticipantCount,

            'winner_count' =>
            $winnerCount,

            'answered_count' =>
            $answeredCount,

            'waiting_answer_count' =>
            $waitingAnswerCount,
        ]);
    }

    public function open(
        Room $room,
        OpenRoomAction $action,
    ): RedirectResponse {
        $action->execute($room);

        return to_route(
            'admin.rooms.play',
            $room,
        )->with(
            'success',
            'Room berhasil dibuka.',
        );
    }

    public function start(
        Room $room,
        StartGameAction $action,
    ): RedirectResponse {
        $action->execute($room);

        return to_route(
            'admin.rooms.play',
            $room,
        )->with(
            'success',
            'Game berhasil dimulai.',
        );
    }

    public function openQuestion(
        Room $room,
        OpenQuestionAction $action,
    ): RedirectResponse {
        $action->execute($room);

        return to_route(
            'admin.rooms.play',
            $room,
        )->with(
            'success',
            'Soal berhasil dibuka.',
        );
    }

    public function closeQuestion(
        Room $room,
        CloseQuestionAction $action,
    ): RedirectResponse {
        $updatedRoom =
            $action->execute($room);

        $message =
            $updatedRoom->status ===
            RoomStatus::Finished
            ? 'Soal ditutup dan pemenang berhasil ditentukan.'
            : 'Soal berhasil ditutup.';

        return to_route(
            'admin.rooms.play',
            $updatedRoom,
        )->with(
            'success',
            $message,
        );
    }

    public function nextQuestion(
        Room $room,
        NextQuestionAction $action,
    ): RedirectResponse {
        $action->execute($room);

        return to_route(
            'admin.rooms.play',
            $room,
        )->with(
            'success',
            'Soal berikutnya sudah disiapkan.',
        );
    }

    public function finish(
        Room $room,
        FinishGameAction $action,
    ): RedirectResponse {
        $action->execute($room);

        return to_route(
            'admin.rooms.play',
            $room,
        )->with(
            'success',
            'Game berhasil diselesaikan.',
        );
    }

    private function roomData(
        Room $room,
    ): array {
        $room->loadMissing([
            'currentQuestion.options',
        ]);

        $currentQuestion =
            $room->currentQuestion;

        $participantCount = $room
            ->participants()
            ->count();

        $activeParticipantCount = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Active->value,
            )
            ->count();

        $eliminatedParticipantCount = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Eliminated->value,
            )
            ->count();

        $winnerCount = $room
            ->participants()
            ->where(
                'status',
                ParticipantStatus::Winner->value,
            )
            ->count();

        $answeredCount = 0;
        $waitingAnswerCount = 0;
        $hasNextQuestion = false;
        $result = null;

        if ($currentQuestion) {
            $answeredCount = $room
                ->answers()
                ->where(
                    'question_id',
                    $currentQuestion->id,
                )
                ->count();

            if (
                $room->status === RoomStatus::Running &&
                $room->game_phase === GamePhase::Open
            ) {
                $waitingAnswerCount = max(
                    0,
                    $activeParticipantCount -
                        $answeredCount,
                );
            }

            if (
                $room->status === RoomStatus::Running &&
                $room->game_phase === GamePhase::Result &&
                $activeParticipantCount > 1
            ) {
                $hasNextQuestion = $room
                    ->questions()
                    ->whereNotNull(
                        'game_order',
                    )
                    ->where(
                        'game_order',
                        '>',
                        $currentQuestion->game_order,
                    )
                    ->exists();
            }

            if (
                $room->game_phase ===
                GamePhase::Result
            ) {
                $correctAnswers = $room
                    ->answers()
                    ->where(
                        'question_id',
                        $currentQuestion->id,
                    )
                    ->where(
                        'is_correct',
                        true,
                    )
                    ->with(
                        'participant:id,name,code',
                    )
                    ->get();

                $correctCount =
                    $correctAnswers->count();

                $wrongCount = $room
                    ->answers()
                    ->where(
                        'question_id',
                        $currentQuestion->id,
                    )
                    ->where(
                        'is_correct',
                        false,
                    )
                    ->count();

                $roundResult =
                    $correctCount === 0
                    ? 'no_elimination'
                    : 'normal';

                $eliminatedThisQuestionCount =
                    $room
                    ->participants()
                    ->where(
                        'eliminated_question_id',
                        $currentQuestion->id,
                    )
                    ->count();

                $remainingCount = $room
                    ->participants()
                    ->where(
                        'status',
                        ParticipantStatus::Active->value,
                    )
                    ->count();

                $participantsBeforeRound =
                    $remainingCount +
                    $eliminatedThisQuestionCount;

                $noAnswerCount = max(
                    0,
                    $participantsBeforeRound -
                        $answeredCount,
                );

                $result = [
                    'round_result' =>
                    $roundResult,

                    'answered_count' =>
                    $answeredCount,

                    'correct_count' =>
                    $correctCount,

                    'wrong_count' =>
                    $wrongCount,

                    'no_answer_count' =>
                    $noAnswerCount,

                    'eliminated_count' =>
                    $eliminatedThisQuestionCount,

                    'remaining_count' =>
                    $remainingCount,

                    'correct_participants' =>
                    $correctAnswers
                        ->map(
                            fn($answer) => [
                                'id' =>
                                $answer
                                    ->participant
                                    ->id,

                                'name' =>
                                $answer
                                    ->participant
                                    ->name,

                                'code' =>
                                $answer
                                    ->participant
                                    ->code,
                            ],
                        )
                        ->values()
                        ->all(),
                ];
            }
        }

        return [
            'id' =>
            $room->id,

            'name' =>
            $room->name,

            'code' =>
            $room->code,

            'status' =>
            $room->status->value,

            'status_label' =>
            $room->status->label(),

            'game_phase' =>
            $room->game_phase?->value,

            'shuffle_questions' =>
            $room->shuffle_questions,

            'revision' =>
            $room->revision,

            'question_count' =>
            $room
                ->questions()
                ->count(),

            'participant_count' =>
            $participantCount,

            'active_participant_count' =>
            $activeParticipantCount,

            'eliminated_participant_count' =>
            $eliminatedParticipantCount,

            'winner_count' =>
            $winnerCount,

            'answered_count' =>
            $answeredCount,

            'waiting_answer_count' =>
            $waitingAnswerCount,

            'has_next_question' =>
            $hasNextQuestion,

            'result' =>
            $result,

            'current_question' =>
            $currentQuestion
                ? [
                    'id' =>
                    $currentQuestion->id,

                    'text' =>
                    $currentQuestion->text,

                    'game_order' =>
                    $currentQuestion->game_order,

                    'options' =>
                    $currentQuestion
                        ->options
                        ->sortBy('label')
                        ->values()
                        ->map(
                            fn($option) => [
                                'id' =>
                                $option->id,

                                'label' =>
                                $option->label,

                                'text' =>
                                $option->text,

                                'is_correct' =>
                                $option->is_correct,
                            ],
                        )
                        ->all(),
                ]
                : null,
        ];
    }
}