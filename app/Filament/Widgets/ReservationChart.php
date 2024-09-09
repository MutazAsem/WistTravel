<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatusEnum;
use App\Models\Reservation;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ReservationChart extends ChartWidget
{
    use HasWidgetShield;

    protected static ?string $heading = 'Reservation Status Overview';

    protected static ?int $sort = 1;

    protected function getData(): array
    {
        $data = Reservation::select('status', DB::raw('count(*) as  count'))
        ->groupBy('status')
        ->pluck('count', 'status')
        ->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'Reservations Status',
                    'data' => array_values($data)
                ]
            ],
            'labels' => ReservationStatusEnum::cases(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
