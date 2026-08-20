<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('filament.models.settings');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.setting');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.setting_information'))
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label(__('filament.fields.key'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->disabled(),

                        Forms\Components\Select::make('type')
                            ->label(__('filament.fields.type'))
                            ->required()
                            ->options([
                                'text' => 'Text',
                                'textarea' => 'Textarea',
                                'email' => 'Email',
                                'url' => 'URL',
                                'number' => 'Number',
                                'boolean' => 'Boolean',
                                'image' => 'Image',
                            ])
                            ->default('text')
                            ->live()
                            ->columnSpanFull()
                            ->disabled(),

                        // Text/Email/URL/Number Fields
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('value_en_text')
                                    ->label(__('filament.fields.value') . ' (EN)')
                                    ->required()
                                    ->visible(fn (Get $get) => in_array($get('type'), ['text', 'email', 'url', 'number']))
                                    ->email(fn (Get $get) => $get('type') === 'email')
                                    ->url(fn (Get $get) => $get('type') === 'url')
                                    ->numeric(fn (Get $get) => $get('type') === 'number')
                                    ->dehydrateStateUsing(fn ($state) => $state)
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                        if ($record && in_array($record->type, ['text', 'email', 'url', 'number'])) {
                                            $component->state($record->value_en);
                                        }
                                    })
                                    ->statePath('value_en'),

                                Forms\Components\TextInput::make('value_ar_text')
                                    ->label(__('filament.fields.value') . ' (AR)')
                                    ->required()
                                    ->visible(fn (Get $get) => in_array($get('type'), ['text', 'email', 'url', 'number']))
                                    ->email(fn (Get $get) => $get('type') === 'email')
                                    ->url(fn (Get $get) => $get('type') === 'url')
                                    ->numeric(fn (Get $get) => $get('type') === 'number')
                                    ->dehydrateStateUsing(fn ($state) => $state)
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                        if ($record && in_array($record->type, ['text', 'email', 'url', 'number'])) {
                                            $component->state($record->value_ar);
                                        }
                                    })
                                    ->statePath('value_ar'),
                            ])
                            ->visible(fn (Get $get) => in_array($get('type'), ['text', 'email', 'url', 'number'])),

                        // Textarea Fields
                        Forms\Components\Textarea::make('value_en_textarea')
                            ->label(__('filament.fields.value') . ' (EN)')
                            ->required()
                            ->visible(fn (Get $get) => $get('type') === 'textarea')
                            ->rows(5)
                            ->columnSpanFull()
                            ->afterStateHydrated(function (Forms\Components\Textarea $component, $state, $record) {
                                if ($record && $record->type === 'textarea') {
                                    $component->state($record->value_en);
                                }
                            })
                            ->statePath('value_en'),

                        Forms\Components\Textarea::make('value_ar_textarea')
                            ->label(__('filament.fields.value') . ' (AR)')
                            ->required()
                            ->visible(fn (Get $get) => $get('type') === 'textarea')
                            ->rows(5)
                            ->columnSpanFull()
                            ->afterStateHydrated(function (Forms\Components\Textarea $component, $state, $record) {
                                if ($record && $record->type === 'textarea') {
                                    $component->state($record->value_ar);
                                }
                            })
                            ->statePath('value_ar'),

                        // Boolean Fields
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('value_en_boolean')
                                    ->label(__('filament.fields.value') . ' (EN)')
                                    ->visible(fn (Get $get) => $get('type') === 'boolean')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->afterStateHydrated(function (Forms\Components\Toggle $component, $state, $record) {
                                        if ($record && $record->type === 'boolean') {
                                            $component->state($record->value_en);
                                        }
                                    })
                                    ->statePath('value_en'),

                                Forms\Components\Toggle::make('value_ar_boolean')
                                    ->label(__('filament.fields.value') . ' (AR)')
                                    ->visible(fn (Get $get) => $get('type') === 'boolean')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->inline(false)
                                    ->afterStateHydrated(function (Forms\Components\Toggle $component, $state, $record) {
                                        if ($record && $record->type === 'boolean') {
                                            $component->state($record->value_ar);
                                        }
                                    })
                                    ->statePath('value_ar'),
                            ])
                            ->visible(fn (Get $get) => $get('type') === 'boolean'),

                        // Image Fields
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('value_en_image')
                                    ->label(__('filament.fields.value') . ' (EN)')
                                    ->visible(fn (Get $get) => $get('type') === 'image')
                                    ->image()
                                    ->directory('settings')
                                    ->maxSize(2048)
                                    ->afterStateHydrated(function (Forms\Components\FileUpload $component, $state, $record) {
                                        if ($record && $record->type === 'image') {
                                            $component->state($record->value_en);
                                        }
                                    })
                                    ->statePath('value_en'),

                                Forms\Components\FileUpload::make('value_ar_image')
                                    ->label(__('filament.fields.value') . ' (AR)')
                                    ->visible(fn (Get $get) => $get('type') === 'image')
                                    ->image()
                                    ->directory('settings')
                                    ->maxSize(2048)
                                    ->afterStateHydrated(function (Forms\Components\FileUpload $component, $state, $record) {
                                        if ($record && $record->type === 'image') {
                                            $component->state($record->value_ar);
                                        }
                                    })
                                    ->statePath('value_ar'),
                            ])
                            ->visible(fn (Get $get) => $get('type') === 'image'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament.fields.id'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('key')
                    ->label(__('filament.fields.key'))
                    ->sortable()
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament.fields.type'))
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'text' => 'gray',
                        'textarea' => 'info',
                        'email' => 'warning',
                        'url' => 'primary',
                        'number' => 'success',
                        'boolean' => 'danger',
                        'image' => 'purple',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('value')
                    ->label(__('filament.fields.value'))
                    ->limit(50)
                    ->getStateUsing(function ($record) {
                        $locale = app()->getLocale();
                        $value = $locale === 'ar' ? $record->value_ar : $record->value_en;
                        
                        if ($record->type === 'image' && $value) {
                            return $locale === 'ar' ? '🖼️ صورة' : '🖼️ Image';
                        }
                        if ($record->type === 'boolean') {
                            if ($locale === 'ar') {
                                return $value ? '✓ نعم' : '✗ لا';
                            }
                            return $value ? '✓ Yes' : '✗ No';
                        }
                        return $value;
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('filament.filters.type'))
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'email' => 'Email',
                        'url' => 'URL',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'image' => 'Image',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListSettings::route('/'),
            // 'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
            'view' => Pages\ViewSetting::route('/{record}'),
        ];
    }
}
