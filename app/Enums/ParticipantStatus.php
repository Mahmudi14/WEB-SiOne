<?php

namespace App\Enums;

enum ParticipantStatus: string
{
    case Waiting = 'waiting';
    case Active = 'active';
    case Eliminated = 'eliminated';
    case Winner = 'winner';

    public function label(): string
    {
        return match ($this) {
            self::Waiting => 'Menunggu',
            self::Active => 'Aktif',
            self::Eliminated => 'Tereliminasi',
            self::Winner => 'Pemenang',
        };
    }
}
