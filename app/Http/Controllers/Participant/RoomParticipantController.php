<?php

namespace App\Http\Controllers\Participant;

use App\Enums\GamePhase;
use App\Enums\ParticipantStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\SubmitAnswerRequest;
use App\Models\Answer;
use App\Models\Participant;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class RoomParticipantController extends Controller
{
    public function index(): Response
    {
        return Inertia::render(
            'participant/FindRoom',
        );
    }

    public function find(
        Request $request,
    ): RedirectResponse {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
            ],
        ]);

        $code = strtoupper(
            trim($validated['code']),
        );

        $room = Room::query()
            ->where('code', $code)
            ->first();

        if (!$room) {
            return back()->withErrors([
                'code' =>
                'Kode room tidak ditemukan.',
            ]);
        }

        if (
            $room->status ===
            RoomStatus::Locked
        ) {
            return back()->withErrors([
                'code' =>
                'Room belum dibuka oleh admin.',
            ]);
        }

        if (
            $room->status ===
            RoomStatus::Running
        ) {
            return back()->withErrors([
                'code' =>
                'Game sudah dimulai. Peserta baru tidak dapat bergabung.',
            ]);
        }

        if (
            $room->status ===
            RoomStatus::Finished
        ) {
            return back()->withErrors([
                'code' =>
                'Game pada room ini sudah selesai.',
            ]);
        }

        return to_route(
            'participant.join',
            $room->code,
        );
    }

    public function joinForm(
        Request $request,
        string $code,
    ): Response|RedirectResponse {
        $room =
            $this->roomByCode($code);

        abort_unless(
            $room->status ===
                RoomStatus::Waiting,
            404,
        );

        $existingParticipant =
            $this->participantFromSession(
                $request,
                $room,
            );

        if ($existingParticipant) {
            return to_route(
                'participant.lobby',
                $room->code,
            );
        }

        return Inertia::render(
            'participant/JoinRoom',
            [
                'room' => [
                    'name' =>
                    $room->name,

                    'code' =>
                    $room->code,

                    'description' =>
                    $room->description,
                ],
            ],
        );
    }

    public function join(
        Request $request,
        string $code,
    ): RedirectResponse {
        $validated =
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:150',
                ],

                'participant_code' => [
                    'required',
                    'string',
                    'max:50',
                ],
            ]);

        $room =
            $this->roomByCode($code);

        $participant = DB::transaction(
            function () use (
                $room,
                $validated,
            ) {
                $room = Room::query()
                    ->whereKey($room->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    $room->status ===
                        RoomStatus::Waiting,
                    422,
                    'Room sudah tidak menerima peserta.',
                );

                $participantCode =
                    strtoupper(
                        trim(
                            $validated['participant_code'],
                        ),
                    );

                $exists = Participant::query()
                    ->where(
                        'room_id',
                        $room->id,
                    )
                    ->where(
                        'code',
                        $participantCode,
                    )
                    ->exists();

                abort_if(
                    $exists,
                    422,
                    'Kode peserta sudah digunakan.',
                );

                return Participant::create([
                    'room_id' =>
                    $room->id,

                    'name' =>
                    trim(
                        $validated['name'],
                    ),

                    'code' =>
                    $participantCode,

                    'status' =>
                    ParticipantStatus::Waiting,

                    'joined_at' =>
                    now(),
                ]);
            },
        );

        $request
            ->session()
            ->put(
                'participant',
                [
                    'id' =>
                    $participant->id,

                    'room_id' =>
                    $room->id,
                ],
            );

        return to_route(
            'participant.lobby',
            $room->code,
        );
    }

    public function lobby(
        Request $request,
        string $code,
    ): Response|RedirectResponse {
        $room =
            $this->roomByCode($code);

        $participant =
            $this->participantFromSession(
                $request,
                $room,
            );

        abort_unless(
            $participant,
            403,
            'Peserta belum bergabung ke room ini.',
        );

        if (
            $room->status ===
            RoomStatus::Running
        ) {
            return to_route(
                'participant.play',
                $room->code,
            );
        }

        return Inertia::render(
            'participant/Lobby',
            [
                'room' => [
                    'name' =>
                    $room->name,

                    'code' =>
                    $room->code,

                    'status' =>
                    $room
                        ->status
                        ->value,

                    'revision' =>
                    $room->revision,
                ],

                'participant' => [
                    'name' =>
                    $participant->name,

                    'code' =>
                    $participant->code,

                    'status' =>
                    $participant
                        ->status
                        ->value,
                ],
            ],
        );
    }

    public function play(
        Request $request,
        string $code,
    ): Response|RedirectResponse {
        $room =
            $this->roomByCode($code);

        $participant =
            $this->participantFromSession(
                $request,
                $room,
            );

        abort_unless(
            $participant,
            403,
            'Peserta belum bergabung ke room ini.',
        );

        if (
            $room->status ===
            RoomStatus::Waiting
        ) {
            return to_route(
                'participant.lobby',
                $room->code,
            );
        }

        $participantView =
            $this->resolveParticipantView(
                $room,
                $participant,
            );

        return Inertia::render(
            'participant/Game',
            [
                'room' => [
                    'name' =>
                    $room->name,

                    'code' =>
                    $room->code,

                    'status' =>
                    $room
                        ->status
                        ->value,

                    'game_phase' =>
                    $room
                        ->game_phase
                        ?->value,

                    'participant_view' =>
                    $participantView,

                    'revision' =>
                    $room->revision,
                ],

                'participant' => [
                    'name' =>
                    $participant->name,

                    'code' =>
                    $participant->code,

                    'status' =>
                    $participant
                        ->status
                        ->value,
                ],
            ],
        );
    }

    public function state(
        Request $request,
        string $code,
    ): JsonResponse {
        $room =
            $this->roomByCode($code);

        $participant =
            $this->participantFromSession(
                $request,
                $room,
            );

        abort_unless(
            $participant,
            403,
            'Peserta belum bergabung ke room ini.',
        );

        $participantView =
            $this->resolveParticipantView(
                $room,
                $participant,
            );

        $displayQuestion =
            $this->resolveDisplayQuestion(
                $room,
                $participant,
            );

        $currentAnswer = null;

        if ($displayQuestion) {
            $currentAnswer = Answer::query()
                ->where(
                    'participant_id',
                    $participant->id,
                )
                ->where(
                    'question_id',
                    $displayQuestion->id,
                )
                ->first();
        }

        $answerResult = null;
        $roundResult = null;

        if (
            $participant->status ===
            ParticipantStatus::Eliminated
        ) {
            $answerResult =
                $currentAnswer
                ? 'wrong'
                : 'no_answer';

            $roundResult =
                'normal';
        } elseif (
            $participantView ===
            'result' &&
            $displayQuestion
        ) {
            $correctCount =
                Answer::query()
                ->where(
                    'room_id',
                    $room->id,
                )
                ->where(
                    'question_id',
                    $displayQuestion->id,
                )
                ->where(
                    'is_correct',
                    true,
                )
                ->count();

            $roundResult =
                $correctCount === 0
                ? 'no_elimination'
                : 'normal';

            if (
                $participant->status ===
                ParticipantStatus::Active
            ) {
                if (!$currentAnswer) {
                    $answerResult =
                        'no_answer';
                } elseif (
                    $currentAnswer->is_correct
                ) {
                    $answerResult =
                        'correct';
                } else {
                    $answerResult =
                        'wrong';
                }
            }
        }

        $data = [
            'revision' =>
            $room->revision,

            'room_status' =>
            $room
                ->status
                ->value,

            'game_phase' =>
            $room
                ->game_phase
                ?->value,

            'participant_view' =>
            $participantView,

            'participant_status' =>
            $participant
                ->status
                ->value,

            'question' =>
            null,

            'answer' => [
                'submitted' =>
                $currentAnswer !== null,

                'selected_option_id' =>
                $currentAnswer
                    ?->question_option_id,

                'result' =>
                $answerResult,
            ],

            'round_result' =>
            $roundResult,
        ];

        if (
            $participantView ===
            'question' &&
            $room->status ===
            RoomStatus::Running &&
            $room->game_phase ===
            GamePhase::Open &&
            $participant->status ===
            ParticipantStatus::Active &&
            $room->current_question_id
        ) {
            $question = $room
                ->questions()
                ->with('options')
                ->whereKey(
                    $room->current_question_id,
                )
                ->firstOrFail();

            $data['question'] = [
                'id' =>
                $question->id,

                'number' =>
                $question->game_order,

                'text' =>
                $question->text,

                'options' =>
                $question
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
                        ],
                    )
                    ->all(),
            ];
        }

        return response()->json(
            $data,
        );
    }

    public function answer(
        SubmitAnswerRequest $request,
        string $code,
    ): RedirectResponse {
        $room =
            $this->roomByCode($code);

        $participant =
            $this->participantFromSession(
                $request,
                $room,
            );

        abort_unless(
            $participant,
            403,
            'Peserta belum bergabung ke room ini.',
        );

        DB::transaction(
            function () use (
                $request,
                $room,
                $participant,
            ) {
                $room = Room::query()
                    ->whereKey($room->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    $room->status ===
                        RoomStatus::Running,
                    422,
                    'Game tidak sedang berjalan.',
                );

                abort_unless(
                    $room->game_phase ===
                        GamePhase::Open,
                    422,
                    'Soal sedang tidak menerima jawaban.',
                );

                abort_unless(
                    $room->current_question_id,
                    422,
                    'Tidak ada soal aktif.',
                );

                $participant =
                    Participant::query()
                    ->whereKey(
                        $participant->id,
                    )
                    ->where(
                        'room_id',
                        $room->id,
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                abort_unless(
                    $participant->status ===
                        ParticipantStatus::Active,
                    422,
                    'Peserta sudah tidak aktif.',
                );

                $option =
                    QuestionOption::query()
                    ->whereKey(
                        $request->validated(
                            'question_option_id',
                        ),
                    )
                    ->where(
                        'question_id',
                        $room->current_question_id,
                    )
                    ->first();

                abort_unless(
                    $option,
                    422,
                    'Pilihan jawaban tidak valid untuk soal ini.',
                );

                $alreadyAnswered =
                    Answer::query()
                    ->where(
                        'participant_id',
                        $participant->id,
                    )
                    ->where(
                        'question_id',
                        $room->current_question_id,
                    )
                    ->exists();

                abort_if(
                    $alreadyAnswered,
                    422,
                    'Jawaban sudah dikirim dan tidak dapat diubah.',
                );

                Answer::create([
                    'room_id' =>
                    $room->id,

                    'participant_id' =>
                    $participant->id,

                    'question_id' =>
                    $room->current_question_id,

                    'question_option_id' =>
                    $option->id,

                    'is_correct' =>
                    $option->is_correct,

                    'submitted_at' =>
                    now(),
                ]);
            },
        );

        return back();
    }

    private function resolveParticipantView(
        Room $room,
        Participant $participant,
    ): string {
        if (
            $room->status ===
            RoomStatus::Finished
        ) {
            return 'finished';
        }

        if (
            $participant->status ===
            ParticipantStatus::Eliminated
        ) {
            return 'result';
        }

        if (
            $room->status !==
            RoomStatus::Running
        ) {
            return 'ready';
        }

        if (
            $room->game_phase ===
            GamePhase::Open
        ) {
            return 'question';
        }

        if (
            $room->game_phase ===
            GamePhase::Result
        ) {
            return 'result';
        }

        if (
            $room->game_phase ===
            GamePhase::Ready
        ) {
            $currentQuestion =
                $this->currentQuestion(
                    $room,
                );

            if (
                !$currentQuestion ||
                (int) $currentQuestion
                    ->game_order <= 1
            ) {
                return 'ready';
            }

            return 'result';
        }

        return 'ready';
    }

    private function resolveDisplayQuestion(
        Room $room,
        Participant $participant,
    ): ?Question {
        if (
            $participant->status ===
            ParticipantStatus::Eliminated &&
            $participant
            ->eliminated_question_id
        ) {
            return Question::query()
                ->whereKey(
                    $participant
                        ->eliminated_question_id,
                )
                ->where(
                    'room_id',
                    $room->id,
                )
                ->first();
        }

        if (
            $room->status !==
            RoomStatus::Running
        ) {
            return null;
        }

        $currentQuestion =
            $this->currentQuestion(
                $room,
            );

        if (!$currentQuestion) {
            return null;
        }

        if (
            $room->game_phase ===
            GamePhase::Open ||
            $room->game_phase ===
            GamePhase::Result
        ) {
            return $currentQuestion;
        }

        if (
            $room->game_phase ===
            GamePhase::Ready &&
            (int) $currentQuestion
                ->game_order > 1
        ) {
            return Question::query()
                ->where(
                    'room_id',
                    $room->id,
                )
                ->whereNotNull(
                    'closed_at',
                )
                ->where(
                    'game_order',
                    '<',
                    $currentQuestion
                        ->game_order,
                )
                ->orderByDesc(
                    'game_order',
                )
                ->first();
        }

        return null;
    }

    private function currentQuestion(
        Room $room,
    ): ?Question {
        if (
            !$room->current_question_id
        ) {
            return null;
        }

        return Question::query()
            ->whereKey(
                $room->current_question_id,
            )
            ->where(
                'room_id',
                $room->id,
            )
            ->first();
    }

    private function roomByCode(
        string $code,
    ): Room {
        return Room::query()
            ->where(
                'code',
                strtoupper(
                    trim($code),
                ),
            )
            ->firstOrFail();
    }

    private function participantFromSession(
        Request $request,
        Room $room,
    ): ?Participant {
        $sessionParticipant =
            $request
            ->session()
            ->get(
                'participant',
            );

        if (
            !is_array(
                $sessionParticipant,
            )
        ) {
            return null;
        }

        $participantId =
            data_get(
                $sessionParticipant,
                'id',
            );

        $roomId =
            data_get(
                $sessionParticipant,
                'room_id',
            );

        if (
            !$participantId ||
            (int) $roomId !==
            (int) $room->id
        ) {
            return null;
        }

        return Participant::query()
            ->whereKey(
                $participantId,
            )
            ->where(
                'room_id',
                $room->id,
            )
            ->first();
    }
}