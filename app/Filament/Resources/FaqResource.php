<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;
    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation_groups.content_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.models.faqs');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.faq');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.faqs');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.faq_information'))
                    ->schema([
                        Forms\Components\Tabs::make('Translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('question_en')
                                            ->label(__('filament.fields.question') . ' (EN)')
                                            ->required()
                                            ->maxLength(500),
                                        Forms\Components\Textarea::make('answer_en')
                                            ->label(__('filament.fields.answer') . ' (EN)')
                                            ->required()
                                            ->rows(5),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('question_ar')
                                            ->label(__('filament.fields.question') . ' (AR)')
                                            ->required()
                                            ->maxLength(500),
                                        Forms\Components\Textarea::make('answer_ar')
                                            ->label(__('filament.fields.answer') . ' (AR)')
                                            ->required()
                                            ->rows(5),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\Select::make('category_id')
                            ->label(__('filament.fields.category'))
                            ->relationship('category', app()->getLocale() == 'ar' ? 'name_ar' : 'name_en')
                            ->searchable()
                            ->preload()
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
                Tables\Columns\TextColumn::make('question')
                    ->label(__('filament.fields.question'))
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('category.' . (app()->getLocale() == 'ar' ? 'name_ar' : 'name_en'))
                    ->label(__('filament.fields.category'))
                    ->searchable()
                    ->default('-'),
                Tables\Columns\TextColumn::make('order')
                    ->label(__('filament.fields.order'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament.fields.is_active')),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('filament.filters.category'))
                    ->relationship('category', app()->getLocale() == 'ar' ? 'name_ar' : 'name_en'),
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}

