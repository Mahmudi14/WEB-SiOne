<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Locked = 'locked';
    case Waiting = 'waiting';
    case Running = 'running';
    case Finished = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::Locked => 'Terkunci',
            self::Waiting => 'Menunggu',
            self::Running => 'Berjalan',
            self::Finished => 'Selesai',
        };
    }
}
