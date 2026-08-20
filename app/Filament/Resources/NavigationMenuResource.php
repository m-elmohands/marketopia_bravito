<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationMenuResource\Pages;
use App\Models\NavigationMenu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NavigationMenuResource extends Resource
{
    protected static ?string $model = NavigationMenu::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'Website Management';
    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = false;


    public static function getNavigationGroup(): string
    {
        return __('filament.navigation_groups.website_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.models.navigation_menus');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.navigation_menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.navigation_menus');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.menu_information'))
                    ->schema([
                        Forms\Components\Tabs::make('Translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_en')
                                            ->label(__('filament.fields.title') . ' (EN)')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ar')
                                            ->label(__('filament.fields.title') . ' (AR)')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Select::make('parent_id')
                            ->label(__('filament.fields.parent_menu'))
                            ->relationship('parent', app()->getLocale() == 'ar' ? 'title_ar' : 'title_en')
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('url')
                                    ->label(__('filament.fields.url'))
                                    ->url(),
                                Forms\Components\TextInput::make('route_name')
                                    ->label(__('filament.fields.route_name'))
                                    ->placeholder('home')
                                    ->helperText('Laravel route name'),
                            ]),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('target')
                                    ->label(__('filament.fields.target'))
                                    ->required()
                                    ->options([
                                        '_self' => 'Same Window',
                                        '_blank' => 'New Window',
                                    ])
                                    ->default('_self'),
                                Forms\Components\Select::make('location')
                                    ->label(__('filament.fields.location'))
                                    ->required()
                                    ->options([
                                        'header' => 'Header',
                                        'footer' => 'Footer',
                                        'sidebar' => 'Sidebar',
                                    ])
                                    ->default('header'),
                            ]),
                        Forms\Components\TextInput::make('icon')
                            ->label(__('filament.fields.icon'))
                            ->placeholder('heroicon-o-home')
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('order')
                                    ->label(__('filament.fields.order'))
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('filament.fields.is_active'))
                                    ->default(true),
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
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable()
                    ->description(fn ($record) => $record->parent ? 'Parent: ' . $record->parent->title : null),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('filament.fields.location'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'header' => 'success',
                        'footer' => 'warning',
                        'sidebar' => 'info',
                    }),
                Tables\Columns\TextColumn::make('url')
                    ->label(__('filament.fields.url'))
                    ->limit(30)
                    ->default('-'),
                Tables\Columns\TextColumn::make('target')
                    ->label(__('filament.fields.target')),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('filament.fields.order'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament.fields.is_active')),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('location')
                    ->label(__('filament.filters.location'))
                    ->options([
                        'header' => 'Header',
                        'footer' => 'Footer',
                        'sidebar' => 'Sidebar',
                    ]),
                Tables\Filters\SelectFilter::make('parent_id')
                    ->label(__('filament.filters.parent_menu'))
                    ->relationship('parent', app()->getLocale() == 'ar' ? 'title_ar' : 'title_en'),
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
            'index' => Pages\ListNavigationMenus::route('/'),
            'create' => Pages\CreateNavigationMenu::route('/create'),
            'edit' => Pages\EditNavigationMenu::route('/{record}/edit'),
        ];
    }
}

