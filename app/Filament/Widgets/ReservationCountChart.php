<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ReservationCountChart extends ChartWidget
{
    use HasWidgetShield;
    
    protected static ?string $heading = 'Reservations by Date';

    protected static ?int $sort = 0;

    protected function getData(): array
    {
        $data = $this->getReservationsPerMonth();
        return [
            'datasets' => [
                [
                    'label' => 'Reservations',
                    'data' => $data['reservationsPerMonth']
                ]
            ],
            'labels' =>  $data['months']
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getReservationsPerMonth(): array
    {
        $now = Carbon::now();
        $ordersPerMonth = [];
        $months = collect(range(1, 12))->map(function ($month) use ($now, &$reservationsPerMonth) {
            $count = Reservation::whereMonth('created_at', $month)->count();
            $reservationsPerMonth[] = $count;
            return $now->month($month)->format('M');
        })->toArray();

        return [
            'reservationsPerMonth' => $reservationsPerMonth,
            'months' => $months
        ];
    }
}
