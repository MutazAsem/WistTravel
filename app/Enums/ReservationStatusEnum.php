<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReservationStatusEnum: string implements HasLabel
{
    case Booked = 'Booked';
    case live = 'live';
    case Canceled = 'Canceled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Booked => 'Booked',
            self::live => 'live',
            self::Canceled => 'Canceled',
            
        };
    }
}