<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Models\Reservation;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $recordTitleAttribute = 'id';

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'description'];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Add New Reservation')
                            ->schema([
                                Forms\Components\Select::make('client_id')
                                    ->relationship('client', 'name')
                                    ->required()
                                    ->markAsRequired(false),
                                Forms\Components\Select::make('hotel_id')
                                    ->relationship('hotel', 'name')
                                    ->label('Hotel Name')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->native(false),
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->maxLength(65535)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->numeric()
                                    // ->default(0)
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('people_number')
                                    ->label('Number of people')
                                    ->suffix('People')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->numeric()
                                    ->minValue(1),
                                Forms\Components\TimePicker::make('start_time')
                                    ->native(false)
                                    ->prefix('Booking starts at')
                                    ->label('Start Time'),
                                Forms\Components\TimePicker::make('end_time')
                                    ->native(false)
                                    ->prefix('Booking Ends at')
                                    ->label('End Time'),
                                Forms\Components\DatePicker::make('start_date')
                                    ->native(false)
                                    ->prefix('Booking starts from')
                                    ->label('Start Date')
                                    ->live(onBlur: true)
                                    ->reactive()
                                    ->minDate(Carbon::today())
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $start_date = $get('start_date');
                                        $end_date = $get('end_date');
                                        if ($start_date && $end_date) {
                                            $days = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
                                            $set('days', $days);
                                        }
                                    }),
                                Forms\Components\DatePicker::make('end_date')
                                    ->native(false)
                                    ->live(onBlur: true)
                                    ->reactive()
                                    ->prefix('Booking ends on')
                                    ->label('End Date')
                                    ->minDate(fn(callable $get) => $get('start_date'))
                                    ->afterStateUpdated(function (callable $set, callable $get) {
                                        $start_date = $get('start_date');
                                        $end_date = $get('end_date');
                                        if ($start_date && $end_date) {
                                            $days = Carbon::parse($start_date)->diffInDays(Carbon::parse($end_date));
                                            $set('days', $days);
                                        }
                                    }),
                                Forms\Components\TextInput::make('days')
                                    ->label('Number of days')
                                    ->prefix('days')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->numeric()
                                    ->minValue(1)
                                    ->readOnly(),
                                Forms\Components\TextInput::make('status')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->maxLength(255),
                            ])->columns(2)
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hotel.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('people_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
