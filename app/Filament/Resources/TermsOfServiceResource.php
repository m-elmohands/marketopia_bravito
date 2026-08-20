<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TermsOfServiceResource\Pages;
use App\Models\TermsOfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TermsOfServiceResource extends Resource
{
    protected static ?string $model = TermsOfService::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Website Management';
    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation_groups.website_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.models.terms_of_services');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.terms_of_service');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.terms_of_services');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.terms_of_service_information'))
                    ->schema([
                        Forms\Components\Tabs::make('Translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_en')
                                            ->label(__('filament.fields.title') . ' (EN)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make('content_en')
                                            ->label(__('filament.fields.content') . ' (EN)')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ar')
                                            ->label(__('filament.fields.title') . ' (AR)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\RichEditor::make('content_ar')
                                            ->label(__('filament.fields.content') . ' (AR)')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                                'h2',
                                                'h3',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament.fields.is_active'))
                            ->default(true)
                            ->columnSpanFull(),
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
                    ->limit(50),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament.fields.is_active')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
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
            'index' => Pages\ListTermsOfServices::route('/'),
            'create' => Pages\CreateTermsOfService::route('/create'),
            'edit' => Pages\EditTermsOfService::route('/{record}/edit'),
        ];
    }
}

