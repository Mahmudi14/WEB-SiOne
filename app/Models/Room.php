<?php

namespace App\Models;

use App\Enums\RoomStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\GamePhase;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'shuffle_questions',
        'game_phase',
        'current_question_id',
        'revision',
        'started_at',
        'finished_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RoomStatus::class,
            'game_phase' => GamePhase::class,
            'shuffle_questions' => 'boolean',
            'revision' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)
            ->orderBy('position');
    }
    public function participants(): HasMany
    {
        return $this->hasMany(Participant::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function currentQuestion(): BelongsTo
    {
        return $this->belongsTo(
            Question::class,
            'current_question_id',
        );
    }
}
