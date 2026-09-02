<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\RoomGameController;
use App\Http\Controllers\Admin\RoomQuestionController;
use App\Http\Controllers\Admin\RoomResultController;
use App\Http\Controllers\Participant\RoomParticipantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::inertia('/', 'Welcome')
    ->name('home');

/*
|--------------------------------------------------------------------------
| Participant
|--------------------------------------------------------------------------
*/

Route::prefix('participant')
    ->name('participant.')
    ->controller(RoomParticipantController::class)
    ->group(function () {
        Route::get('/', 'index')
            ->name('index');

        Route::post('/room/find', 'find')
            ->name('find');

        Route::get('/room/{code}/join', 'joinForm')
            ->name('join');

        Route::post('/room/{code}/join', 'join')
            ->name('join.store');

        Route::get('/room/{code}/lobby', 'lobby')
            ->name('lobby');

        Route::get('/room/{code}/play', 'play')
            ->name('play');

        Route::get('/room/{code}/state', 'state')
            ->name('state');

        Route::post('/room/{code}/answer', 'answer')
            ->name('answer');
    });

/*
|--------------------------------------------------------------------------
| Starter Dashboard Redirect
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'verified',
])->get('/dashboard', function () {
    return redirect()
        ->route('admin.dashboard');
})->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/',
            [DashboardController::class, 'index'],
        )->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Room CRUD
        |--------------------------------------------------------------------------
        */

        Route::get(
            'rooms/active',
            [RoomController::class, 'index'],
        )
            ->defaults('filter', 'active')
            ->name('rooms.active');

        Route::get(
            'rooms/finished',
            [RoomController::class, 'index'],
        )
            ->defaults('filter', 'finished')
            ->name('rooms.finished');

        Route::resource(
            'rooms',
            RoomController::class,
        )->except('show');

        /*
        |--------------------------------------------------------------------------
        | Room Actions
        |--------------------------------------------------------------------------
        */

        Route::prefix('rooms/{room}')
            ->name('rooms.')
            ->group(function () {
                Route::post(
                    '/duplicate',
                    [RoomController::class, 'duplicate'],
                )->name('duplicate');

                /*
                |--------------------------------------------------------------------------
                | Game Control
                |--------------------------------------------------------------------------
                */

                Route::controller(RoomGameController::class)
                    ->group(function () {
                        Route::get('/play', 'show')
                            ->name('play');

                        Route::get('/state', 'state')
                            ->name('state');

                        Route::post('/open', 'open')
                            ->name('open');

                        Route::post('/start', 'start')
                            ->name('start');

                        Route::post(
                            '/question/open',
                            'openQuestion',
                        )->name('question.open');

                        Route::post(
                            '/question/close',
                            'closeQuestion',
                        )->name('question.close');

                        Route::post(
                            '/question/next',
                            'nextQuestion',
                        )->name('question.next');

                        Route::post('/finish', 'finish')
                            ->name('finish');
                    });

                /*
                |--------------------------------------------------------------------------
                | Result
                |--------------------------------------------------------------------------
                */

                Route::get(
                    '/result',
                    [RoomResultController::class, 'show'],
                )->name('result');

                /*
                |--------------------------------------------------------------------------
                | Question CRUD
                |--------------------------------------------------------------------------
                */

                Route::prefix('questions')
                    ->name('questions.')
                    ->controller(RoomQuestionController::class)
                    ->group(function () {
                        Route::post('/', 'store')
                            ->name('store');

                        Route::put(
                            '/{question}',
                            'update',
                        )->name('update');

                        Route::delete(
                            '/{question}',
                            'destroy',
                        )->name('destroy');
                    });
            });
    });

/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/

require __DIR__ . '/settings.php';