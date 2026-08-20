<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation_groups.content_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.models.testimonials');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.testimonial');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.testimonials');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.testimonial_information'))
                    ->schema([
                        Forms\Components\Tabs::make('Translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('client_name_en')
                                            ->label(__('filament.fields.client_name') . ' (EN)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('client_position_en')
                                            ->label(__('filament.fields.client_position') . ' (EN)')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('client_company_en')
                                            ->label(__('filament.fields.client_company') . ' (EN)')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('testimonial_en')
                                            ->label(__('filament.fields.testimonial') . ' (EN)')
                                            ->required()
                                            ->rows(5),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('client_name_ar')
                                            ->label(__('filament.fields.client_name') . ' (AR)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('client_position_ar')
                                            ->label(__('filament.fields.client_position') . ' (AR)')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('client_company_ar')
                                            ->label(__('filament.fields.client_company') . ' (AR)')
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('testimonial_ar')
                                            ->label(__('filament.fields.testimonial') . ' (AR)')
                                            ->required()
                                            ->rows(5),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('client_image')
                            ->label(__('filament.fields.client_image'))
                            ->image()
                            ->directory('testimonials')
                            ->maxSize(2048)
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('rating')
                            ->label(__('filament.fields.rating'))
                            ->required()
                            ->options([
                                5 => '⭐⭐⭐⭐⭐ (5)',
                                4 => '⭐⭐⭐⭐ (4)',
                                3 => '⭐⭐⭐ (3)',
                                2 => '⭐⭐ (2)',
                                1 => '⭐ (1)',
                            ])
                            ->default(5)
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
                Tables\Columns\ImageColumn::make('client_image')
                    ->label(__('filament.fields.client_image'))
                    ->circular(),
                Tables\Columns\TextColumn::make('client_name')
                    ->label(__('filament.fields.client_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('client_company')
                    ->label(__('filament.fields.client_company'))
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('rating')
                    ->label(__('filament.fields.rating'))
                    ->formatStateUsing(fn ($state) => str_repeat('⭐', $state))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament.fields.is_active')),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('filament.fields.order'))
                    ->sortable(),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label(__('filament.filters.rating'))
                    ->options([
                        5 => '5 Stars',
                        4 => '4 Stars',
                        3 => '3 Stars',
                        2 => '2 Stars',
                        1 => '1 Star',
                    ]),
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}

