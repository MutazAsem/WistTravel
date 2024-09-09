<?php

namespace App\Filament\Widgets;

use App\Enums\ReservationStatusEnum;
use App\Filament\Resources\ReservationResource;
use App\Models\Reservation;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestReservation extends BaseWidget
{
    use HasWidgetShield;
    
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(ReservationResource::getEloquentQuery())
            ->defaultPaginationPageOption(10)
            ->defaultSort('created_at', 'desc')

            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('hotel.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('days')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('people_number')
                    ->label('people Number')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(ReservationStatusEnum $state): string => match ($state->value) {
                        'Awaiting Confirmation' => 'info',
                        'Confirmed' => 'warning',
                        'Completed' => 'success',
                        'Canceled' => 'danger',
                    })
                    ->icon(fn(ReservationStatusEnum $state): string => match ($state->value) {
                        'Awaiting Confirmation' => 'heroicon-o-clock',
                        'Confirmed' => 'heroicon-o-check-badge',
                        'Completed' => 'heroicon-o-check-circle',
                        'Canceled' => 'heroicon-o-x-circle',
                    }),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->actions([
                    Tables\Actions\EditAction::make()
                    ->url(fn (Reservation $record): string => ReservationResource::getUrl('edit', ['record' => $record])),
            ]);;
    }
}
