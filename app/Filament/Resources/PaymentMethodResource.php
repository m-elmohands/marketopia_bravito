<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): string
    {
        return __('message.Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('message.Payment Methods');
    }

    public static function getModelLabel(): string
    {
        return __('message.Payment Method');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Payment Methods');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('message.Name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('icon')
                    ->label(__('message.Icon'))
                    ->image()
                    ->imagePreviewHeight('80')
                    ->directory('payment-methods')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->label(__('message.Description'))
                    ->maxLength(1000)
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('message.Active'))
                    ->default(true)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('message.Name'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('icon')
                    ->label(__('message.Icon'))
                    ->defaultImageUrl(null),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('message.Description'))
                    ->limit(50)
                    ->default('-'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('message.Active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit'   => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
