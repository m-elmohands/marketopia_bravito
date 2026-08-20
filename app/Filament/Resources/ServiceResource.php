<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation_groups.content_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.models.services');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.service');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.services');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.service_information'))
                    ->schema([
                        Forms\Components\Tabs::make('Translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_en')
                                            ->label(__('filament.fields.title') . ' (EN)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_en')
                                            ->label(__('filament.fields.description') . ' (EN)')
                                            ->required()
                                            ->rows(5),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ar')
                                            ->label(__('filament.fields.title') . ' (AR)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description_ar')
                                            ->label(__('filament.fields.description') . ' (AR)')
                                            ->required()
                                            ->rows(5),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('icon')
                                    ->label(__('filament.fields.icon'))
                                    ->placeholder('heroicon-o-sparkles or path')
                                    ->maxLength(255),
                                Forms\Components\FileUpload::make('image')
                                    ->label(__('filament.fields.image'))
                                    ->image()
                                    ->directory('services')
                                    ->maxSize(3072),
                            ]),
                        Forms\Components\Repeater::make('features')
                            ->label(__('filament.fields.features'))
                            ->schema([
                                Forms\Components\TextInput::make('en')
                                    ->label('Feature (EN)')
                                    ->required(),
                                Forms\Components\TextInput::make('ar')
                                    ->label('Feature (AR)')
                                    ->required(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label(__('filament.fields.order'))
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('filament.fields.is_active'))
                                    ->default(true)
                                    ->required(),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament.fields.id'))
                    ->sortable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('filament.fields.image')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('icon')
                    ->label(__('filament.fields.icon'))
                    ->limit(30),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('filament.fields.order'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament.fields.is_active')),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\Filter::make('active')
                    ->label(__('filament.filters.active_only'))
                    ->query(fn ($query) => $query->where('is_active', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order');
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}

