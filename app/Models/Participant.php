<?php

namespace App\Models;

use App\Enums\ParticipantStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'name',
        'code',
        'status',
        'eliminated_question_id',
        'joined_at',
        'eliminated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ParticipantStatus::class,
            'joined_at' => 'datetime',
            'eliminated_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function eliminatedQuestion(): BelongsTo
    {
        return $this->belongsTo(
            Question::class,
            'eliminated_question_id',
        );
    }
}
