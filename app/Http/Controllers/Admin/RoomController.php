<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Room\DuplicateRoomAction;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoomRequest;
use App\Http\Requests\Admin\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    */

    public function index(
        Request $request,
    ): Response {
        $filter = $request->route(
            'filter',
            'all',
        );

        /*
         * Pastikan hanya filter yang dikenal
         * yang dapat digunakan.
         */
        if (
            !in_array(
                $filter,
                [
                    'all',
                    'active',
                    'finished',
                ],
                true,
            )
        ) {
            $filter = 'all';
        }

        $query = Room::query()
            ->latest();

        /*
         * Room Aktif:
         *
         * - Locked
         * - Waiting
         * - Running
         */
        if ($filter === 'active') {
            $query->whereIn(
                'status',
                [
                    RoomStatus::Locked->value,
                    RoomStatus::Waiting->value,
                    RoomStatus::Running->value,
                ],
            );
        }

        /*
         * Room Selesai
         */
        if ($filter === 'finished') {
            $query->where(
                'status',
                RoomStatus::Finished->value,
            );
        }

        $rooms = $query
            ->get()
            ->map(
                fn(Room $room) => [
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

                    'shuffle_questions' =>
                    $room->shuffle_questions,

                    'created_at' =>
                    $room->created_at,
                ],
            )
            ->values()
            ->all();

        return Inertia::render(
            'admin/rooms/Index',
            [
                'rooms' =>
                $rooms,

                'filter' =>
                $filter,
            ],
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create(): Response
    {
        return Inertia::render(
            'admin/rooms/Create',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreRoomRequest $request,
    ): RedirectResponse {
        $data =
            $request->validated();

        $room = Room::create([
            'name' =>
            $data['name'],

            'code' =>
            strtoupper(
                $data['code'],
            ),

            'description' =>
            $data['description'] ?? null,

            'status' =>
            RoomStatus::Locked,

            'shuffle_questions' =>
            $data['shuffle_questions'] ?? false,

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

        return to_route(
            'admin.rooms.edit',
            $room,
        )->with(
            'success',
            'Room berhasil dibuat.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    |
    | Halaman tetap boleh dibuka untuk melihat konfigurasi,
    | tetapi hanya LOCKED yang boleh diubah.
    |
    */

    public function edit(
        Request $request,
        Room $room,
    ): Response {
        /*
    |--------------------------------------------------------------------------
    | Permission
    |--------------------------------------------------------------------------
    */

        $canEdit =
            $room->status ===
            RoomStatus::Locked;

        /*
    |--------------------------------------------------------------------------
    | Question Search
    |--------------------------------------------------------------------------
    */

        $search = trim(
            (string) $request->query(
                'q',
                '',
            ),
        );

        /*
    |--------------------------------------------------------------------------
    | Total Questions
    |--------------------------------------------------------------------------
    |
    | Ini total seluruh soal dalam room,
    | tidak terpengaruh pencarian.
    |
    */

        $questionCount =
            $room->questions()
            ->count();

        /*
    |--------------------------------------------------------------------------
    | Paginated Questions
    |--------------------------------------------------------------------------
    */

        $questions = $room
            ->questions()
            ->with([
                'options' =>
                fn($query) =>
                $query->orderBy(
                    'label',
                ),
            ])
            ->when(
                $search !== '',
                fn($query) =>
                $query->where(
                    'text',
                    'like',
                    '%' . $search . '%',
                ),
            )
            ->orderBy(
                'position',
            )
            ->paginate(
                perPage: 10,
                pageName: 'question_page',
            )
            ->withQueryString();

        /*
    |--------------------------------------------------------------------------
    | Transform Questions
    |--------------------------------------------------------------------------
    */

        $questions->through(
            fn($question) => [
                'id' =>
                $question->id,

                'text' =>
                $question->text,

                'position' =>
                $question->position,

                'options' =>
                $question->options
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
                    ->values()
                    ->all(),
            ],
        );

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return Inertia::render(
            'admin/rooms/Edit',
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

                    'shuffle_questions' =>
                    $room->shuffle_questions,

                    'can_edit' =>
                    $canEdit,

                    'can_edit_questions' =>
                    $canEdit,

                    /*
                 * Total seluruh soal,
                 * tidak berubah saat search.
                 */
                    'question_count' =>
                    $questionCount,

                    /*
                 * Data pagination.
                 */
                    'questions' =>
                    $questions,

                    /*
                 * Filter aktif.
                 */
                    'question_filters' => [
                        'search' =>
                        $search,
                    ],
                ],
            ],
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateRoomRequest $request,
        Room $room,
    ): RedirectResponse {
        $this->ensureConfigurationEditable(
            $room,
        );

        $data =
            $request->validated();

        $room->update([
            'name' =>
            $data['name'],

            'code' =>
            strtoupper(
                $data['code'],
            ),

            'description' =>
            $data['description'] ?? null,

            'shuffle_questions' =>
            $data['shuffle_questions'] ?? false,
        ]);

        return to_route(
            'admin.rooms.edit',
            $room,
        )->with(
            'success',
            'Room berhasil diperbarui.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    /*
|--------------------------------------------------------------------------
| Destroy
|--------------------------------------------------------------------------
*/

    public function destroy(
        Room $room,
    ): RedirectResponse {
        abort_if(
            in_array(
                $room->status,
                [
                    RoomStatus::Waiting,
                    RoomStatus::Running,
                ],
                true,
            ),
            422,
            'Room yang sedang menunggu peserta atau sedang berjalan tidak dapat dihapus.',
        );

        $room->delete();

        return back()->with(
            'success',
            'Room berhasil dihapus.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate
    |--------------------------------------------------------------------------
    */

    public function duplicate(
        Room $room,
        DuplicateRoomAction $action,
    ): RedirectResponse {
        /*
         * Duplikasi boleh dilakukan dari
         * status apa pun.
         */
        $newRoom =
            $action->execute(
                $room,
            );

        return to_route(
            'admin.rooms.edit',
            $newRoom,
        )->with(
            'success',
            'Room berhasil diduplikat. Room baru berada dalam status terkunci.',
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Guard
    |--------------------------------------------------------------------------
    */

    private function ensureConfigurationEditable(
        Room $room,
    ): void {
        abort_unless(
            $room->status ===
                RoomStatus::Locked,
            422,
            'Konfigurasi room hanya dapat diubah ketika room masih terkunci.',
        );
    }
}