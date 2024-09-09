<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfferResource\Pages;
use App\Filament\Resources\OfferResource\RelationManagers;
use App\Models\Offer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OfferResource extends Resource
{
    protected static ?string $model = Offer::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'description'];
    }

    protected static int $globalSearchResultsLimit = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Add New Offer')
                            ->schema([
                                Forms\Components\Select::make('hotel_id')
                                    ->relationship('hotel', 'name')
                                    ->label('Hotel Name')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->native(false),
                                Forms\Components\TextInput::make('name')
                                    ->label('Offer Name')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->maxLength(255),
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
                                Forms\Components\Toggle::make('available')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->default(true),
                            ])->columns(2)
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('hotel.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('available')
                    ->boolean(),
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
                Tables\Filters\SelectFilter::make('Hotel')
                    ->relationship('hotel', 'name')->native(false),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('start_date')->label('Start Date')->native(false)->live()->reactive(),
                        Forms\Components\DatePicker::make('end_date')->label('End Date')->native(false)->live()->reactive(),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['start_date'], fn($query, $date) => $query->whereDate('start_date', '>=', $date))
                            ->when($data['end_date'], fn($query, $date) => $query->whereDate('end_date', '<=', $date));
                    }),
            ])->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
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
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
