<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum ReservationStatusEnum: string implements HasLabel
{
    case Awaiting_Confirmation = 'Awaiting Confirmation';
    case Confirmed = 'Confirmed';
    case Completed = 'Completed';
    case Canceled = 'Canceled';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Awaiting_Confirmation => 'Awaiting Confirmation',
            self::Confirmed => 'Confirmed',
            self::Completed => 'Completed',
            self::Canceled => 'Canceled',
            
        };
    }
}