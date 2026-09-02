<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'total_rooms' => Room::query()
                ->count(),

            'waiting_rooms' => Room::query()
                ->where('status', 'waiting')
                ->count(),

            'running_rooms' => Room::query()
                ->where('status', 'running')
                ->count(),

            'finished_rooms' => Room::query()
                ->where('status', 'finished')
                ->count(),
        ];

        return Inertia::render(
            'admin/dashboard/Index',
            [
                'stats' => $stats,
            ],
        );
    }
}