<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Setting;
use App\Models\User;
use App\Enums\UserType;
use App\Enums\OrderStatus;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string
    {
        return __('message.Store Management');
    }
    public static function getNavigationLabel(): string
    {
        return __('message.Orders');
    }

    public static function getModelLabel(): string
    {
        return __('message.Order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('message.Orders');
    }
    public static function getNavigationBadge(): ?string
    {
        $pendingOrders = static::getModel()::where('status', OrderStatus::PENDING)->count();
        return $pendingOrders > 0 ? $pendingOrders  . ' ' . __('message.Pending') : null;
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderByDesc('id');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('message.Customer'))
                            ->options(function () {
                                return User::where('user_type', UserType::USER)
                                    ->get()
                                    ->mapWithKeys(fn($user) => [
                                        $user->id => $user->name . ' - ' . $user->phone,
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('address_id')
                            ->label(__('message.Address'))
                            ->options(function (callable $get) {
                                $userId = $get('user_id');
                                if (!$userId) {
                                    return [];
                                }
                                return \App\Models\Address::where('user_id', $userId)
                                    ->get()
                                    ->mapWithKeys(fn($address) => [
                                        $address->id => $address->address,
                                    ]);
                            })
                            ->searchable()
                            ->reactive()
                            ->required()
                            ->columnSpan(1),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label(__('message.Status'))
                            ->options(function () {
                                $statuses = [
                                    OrderStatus::PENDING->value => __('message.Pending'),
                                    OrderStatus::CONFIRMED->value => __('message.Confirmed'),
                                    OrderStatus::PREPARING->value => __('message.Preparing'),
                                    OrderStatus::READY->value => __('message.Ready'),
                                ];
                                if (Setting::get('deliveryman') === '1') {
                                    $statuses += [
                                        OrderStatus::ONDELIVERY->value => __('message.On Delivery'),
                                        OrderStatus::DELIVERED->value => __('message.Delivered'),
                                    ];
                                }
                                $statuses += [
                                    OrderStatus::CANCELED->value => __('message.Canceled'),
                                ];
                                return $statuses;
                            })
                            ->required()
                            ->default(OrderStatus::PENDING->value)
                            ->columnSpan(1),

                        Forms\Components\Select::make('payment_status')
                            ->label(__('message.Payment Status'))
                            ->options([
                                'paid' => __('message.Paid'),
                                'unpaid' => __('message.Unpaid'),
                            ])
                            ->required()
                            ->default('unpaid')
                            ->columnSpan(1),
                    ]),

                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('payment_method_id')
                            ->label(__('message.Payment Method'))
                            ->options(\App\Models\PaymentMethod::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Select::make('order_type')
                            ->label(__('message.Order Type'))
                            ->options([
                                'online' => __('message.Online'),
                                'pos' => __('message.POS'),
                            ])
                            ->required()
                            ->default('pos')
                            ->columnSpan(1),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label(__('message.Sub Total'))
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $subtotal = (float) $state;
                                $deliveryFee = (float) $get('delivery_fee');
                                $discount = (float) $get('discount');
                                $total = $subtotal + $deliveryFee - $discount;
                                $set('total', $total);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('delivery_fee')
                            ->label(__('message.Delivery Fee'))
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $subtotal = (float) $get('subtotal');
                                $deliveryFee = (float) $state;
                                $discount = (float) $get('discount');
                                $total = $subtotal + $deliveryFee - $discount;
                                $set('total', $total);
                            })
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('discount')
                            ->label(__('message.Discount'))
                            ->numeric()
                            ->step(0.01)
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $subtotal = (float) $get('subtotal');
                                $deliveryFee = (float) $get('delivery_fee');
                                $discount = (float) $state;
                                $total = $subtotal + $deliveryFee - $discount;
                                $set('total', $total);
                            })
                            ->columnSpan(1),
                    ]),

                Forms\Components\TextInput::make('total')
                    ->label(__('message.Total Price'))
                    ->numeric()
                    ->required()
                    ->step(0.01)
                    ->disabled()
                    ->dehydrated()
                    ->columnSpanFull(),

                Forms\Components\Select::make('deliveryman_id')
                    ->label(__('message.Deliveryman'))
                    ->options(function () {
                        return User::where('user_type', UserType::DELIVERYMAN)
                            ->get()
                            ->mapWithKeys(fn($user) => [
                                $user->id => $user->name . ' - ' . $user->phone,
                            ]);
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->visible(fn() => Setting::get('deliveryman') === '1')
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $allowedStatuses = [
                            OrderStatus::READY->value,
                            OrderStatus::ONDELIVERY->value,
                            OrderStatus::DELIVERED->value,
                            OrderStatus::CANCELED->value,
                        ];

                        $currentStatus = $get('status');

                        if (!in_array($currentStatus, $allowedStatuses)) {
                            \Filament\Notifications\Notification::make()
                                ->warning()
                                ->title(__('message.Deliveryman can only be assigned when order status is Ready, On Delivery, Delivered or Canceled'))
                                ->send();

                            $set('deliveryman_id', null);
                        }
                    })
                    ->default(null)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('notes')
                    ->label(__('message.Notes'))
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('receipt_image')
                    ->label(__('message.Receipt Image'))
                    ->image()
                    ->directory('receipts')
                    ->disk('public')
                    ->disabled()
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && $record->receipt_image),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('message.#ID'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('message.User Name'))
                    ->sortable()
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.phone')
                    ->label(__('message.User Phone'))
                    ->sortable()
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('deliveryman.name')
                    ->visible(fn() => Setting::get('deliveryman') === '1')
                    ->label(__('message.Deliveryman'))
                    ->sortable()
                    ->default('-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('message.Status'))

                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'preparing' => 'primary',
                        'ready' => 'success',
                        'on_delivery' => 'purple',
                        'delivered' => 'success',
                        'canceled' => 'danger',
                        default => 'gray'
                    })
                    ->formatStateUsing(function (string $state) {
                        return __('message.' . $state);
                    })
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label(__('message.Payment Method'))
                    ->color(fn(string $state): string => match ($state) {
                        'cash' => 'info',
                        'visa' => 'success',
                        'wallet' => 'warning',
                        default => 'gray'
                    })
                    ->formatStateUsing(function (string $state) {
                        return __('message.' . Str::ucfirst($state));
                    })
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->label(__('message.Payment Status'))
                    ->color(fn(string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'danger',
                        default => 'gray'
                    })
                    ->formatStateUsing(function (string $state) {
                        return __('message.' . Str::ucfirst($state));
                    })
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('order_type')
                    ->label(__('message.Order Type'))
                    ->color(fn(string $state): string => match ($state) {
                        'online' => 'success',
                        'pos' => 'primary',
                        default => 'gray'
                    })
                    ->formatStateUsing(function (string $state) {
                        return __('message.' . Str::ucfirst($state));
                    })
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address.address')
                    ->label(__('message.Address'))
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.coupon.code')
                    ->label(__('message.Coupon'))
                    ->default('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->label(__('message.Discount'))
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state) => $state . ' ' . __('message.Currency')),
                Tables\Columns\TextColumn::make('subtotal')
                    ->label(__('message.Sub Total'))
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state) => $state . ' ' . __('message.Currency')),
                Tables\Columns\TextColumn::make('delivery_fee')
                    ->label(__('message.Delivery Fee'))
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state) => $state . ' ' . __('message.Currency')),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('message.Total Price'))
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn(string $state) => $state . ' ' . __('message.Currency')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make(app()->getLocale() == 'ar' ? 'address.city.name_ar' : 'address.city.name_en')
                    ->label(__('message.City'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make(app()->getLocale() == 'ar' ? 'area.name_ar' : 'area.name_en')
                    ->label(__('message.Area'))
                    ->visible(fn() => Setting::get('deliveryman') === '1' && Setting::get('delivery_fee_type') === 'by_area')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('message.Status'))
                    ->options(function () {
                        $statuses = [
                            OrderStatus::PENDING->value => __('message.Pending'),
                            OrderStatus::CONFIRMED->value => __('message.Confirmed'),
                            OrderStatus::PREPARING->value => __('message.Preparing'),
                            OrderStatus::READY->value => __('message.Ready'),
                        ];
                        if (Setting::get('deliveryman') === '1') {
                            $statuses += [
                                OrderStatus::ONDELIVERY->value => __('message.On Delivery'),
                                OrderStatus::DELIVERED->value => __('message.Delivered'),
                            ];
                        }
                        $statuses += [
                            OrderStatus::CANCELED->value => __('message.Canceled'),
                        ];
                        return $statuses;
                    }),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label(__('message.Payment Status'))
                    ->options([
                        'paid' => __('message.Paid'),
                        'unpaid' => __('message.Unpaid'),
                    ]),
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label(__('message.Payment Method'))
                    ->options([
                        'cash' => __('message.Cash'),
                        'wallet' => __('message.Wallet'),
                    ]),
                Tables\Filters\SelectFilter::make('order_type')
                    ->label(__('message.Order Type'))
                    ->options([
                        'online' => __('message.Online'),
                        'pos' => __('message.POS'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label(__('message.Print Invoice'))
                    ->color('secondary')
                    ->icon('heroicon-o-printer')
                    ->url(fn(Order $record) => route('admin.orders.invoice', $record))
                    ->openUrlInNewTab(),

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
            RelationManagers\OrderDetailsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
    public static function canCreate(): bool
    {
        return true;
    }
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
