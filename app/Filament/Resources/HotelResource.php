<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HotelResource\Pages;
use App\Filament\Resources\HotelResource\RelationManagers;
use App\Models\Hotel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HotelResource extends Resource
{
    protected static ?string $model = Hotel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

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
                        Forms\Components\Section::make('Add New Hotel')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(65535)
                                    ->autosize(),
                                Forms\Components\Textarea::make('services')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->maxLength(65535)
                                    ->autosize(),
                                Forms\Components\Textarea::make('advantages')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->maxLength(65535)
                                    ->autosize(),
                                Forms\Components\TextInput::make('stars')
                                    ->required()
                                    ->markAsRequired(false)
                                    ->numeric()
                                    ->minValue(1)
                                    ->maxValue(7)
                                    // ->default(1)
                                    ->live(onBlur:true)
                                    ->helperText('Enter a Number between 1 and 7.'),
                                Forms\Components\Select::make('city_id')
                                    ->relationship('city', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->markAsRequired(false),
                                Forms\Components\TextInput::make('address_link')
                                    ->maxLength(255)
                                    ->label('Google Maps Link')
                                    ->url()
                                    ->suffixIcon('heroicon-m-globe-alt')
                                    ->suffixIconColor('success'),
                                Forms\Components\Textarea::make('address_description')
                                    ->maxLength(65535),
                                Forms\Components\FileUpload::make('images')
                                    ->image()
                                    ->multiple()
                                    ->directory('hotel-images')
                                    ->maxFiles(5)
                                    ->reorderable()
                                    ->imageEditor()
                                    ->appendFiles()
                                    ->helperText('Select 5 Images.')
                                    ->columnSpanFull(),
                            ])->columns(2)
                    ])->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stars')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address_link')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ImageColumn::make('images')
                    ->circular()
                    ->stacked()
                    ->limit(2)
                    ->limitedRemainingText(isSeparate: true),
            ])
            ->filters([
                  Tables\Filters\SelectFilter::make('City')
                    ->relationship('city', 'name')->native(false),
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
            'index' => Pages\ListHotels::route('/'),
            'create' => Pages\CreateHotel::route('/create'),
            'edit' => Pages\EditHotel::route('/{record}/edit'),
        ];
    }
}
