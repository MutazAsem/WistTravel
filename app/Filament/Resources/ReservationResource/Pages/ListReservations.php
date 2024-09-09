<?php

namespace App\Filament\Resources\ReservationResource\Pages;

use App\Filament\Resources\ReservationResource;
use App\Filament\Resources\ReservationResource\Widgets\ReservationOverview;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All')
            ->icon('heroicon-o-rectangle-stack'),
            'Awaiting Confirmation' => Tab::make()->query(fn ($query) => $query->where('status','Awaiting Confirmation'))
            ->icon('heroicon-o-clock'),
            'Confirmed' => Tab::make()->query(fn ($query) => $query->where('status','Confirmed'))
            ->icon('heroicon-o-check-badge'),
            'Completed' => Tab::make()->query(fn ($query) => $query->where('status','Completed'))
            ->icon('heroicon-o-check-circle'),
            'Canceled' => Tab::make()->query(fn ($query) => $query->where('status','Canceled'))
            ->icon('heroicon-o-x-circle'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ReservationOverview::class,
        ];
    }
}
