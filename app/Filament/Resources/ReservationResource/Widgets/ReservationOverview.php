<?php

namespace App\Filament\Resources\ReservationResource\Widgets;

use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Awaiting Confirmation Reservations', Reservation::query()->where('status','Awaiting Confirmation')->count()),
            Stat::make('Confirmed Reservations', Reservation::query()->where('status','Confirmed')->count()),
            Stat::make('Completed Reservations', Reservation::query()->where('status','Completed')->count()),
            Stat::make('Cancelled Reservations', Reservation::query()->where('status','Canceled')->count()),
            Stat::make('Total Reservations', Reservation::all()->count()),
            Stat::make('Total Price', '$' . number_format(Reservation::sum('price'), 2)),
        ];
    }
}
