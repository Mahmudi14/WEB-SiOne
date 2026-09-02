<?php

namespace App\Enums;

enum GamePhase: string
{
    case Ready = 'ready';
    case Open = 'open';
    case Result = 'result';

    public function label(): string
    {
        return match ($this) {
            self::Ready => 'Siap',
            self::Open => 'Soal Dibuka',
            self::Result => 'Hasil',
        };
    }
}
