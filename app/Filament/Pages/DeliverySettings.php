<?php

namespace App\Filament\Pages;

use Filament\Forms\Components;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use App\Models\Setting;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;

class DeliverySettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static string $view = 'filament.pages.delivery-settings';
    protected static ?int $navigationSort = 5;

    public function getTitle(): string
    {
        return __('message.DeliverySettings');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('message.Settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('message.DeliverySettings');
    }
    public static function getPluralModelLabel(): string
    {
        return __('message.DeliverySettings');
    }

    public static function getModelLabel(): string
    {
        return __('message.DeliverySetting');
    }
    public array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'deliveryman' => (bool) Setting::get('deliveryman'),
            'delivery_fee_type' => Setting::get('delivery_fee_type') ?: null,
            'delivery_fee_fixed' => Setting::get('delivery_fee_fixed') ?: null,
            'delivery_fee_per_km' => Setting::get('delivery_fee_per_km') ?: null,
            'latitude' => Setting::get('latitude') ?: null,
            'longitude' => Setting::get('longitude') ?: null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Section::make(__('message.delivery_settings'))
                    ->schema([
                        Components\Toggle::make('deliveryman')
                            ->label(__('message.enable_delivery'))
                            ->live()
                            ->onColor('success')
                            ->offColor('danger'),

                        Components\Select::make('delivery_fee_type')
                            ->label(__('message.delivery_fee_type'))
                            ->options([
                                'fixed' => __('message.fixed_price'),
                                'per_km' => __('message.per_km'),
                                'area' => __('message.by_area'),
                            ])
                            ->required()
                            ->live()
                            ->visible(fn($get) => $get('deliveryman')),

                        Components\TextInput::make('delivery_fee_fixed')
                            ->label(__('message.fixed_delivery_fee'))
                            ->minValue(0)
                            ->numeric()
                            ->required()
                            ->visible(fn($get) =>
                            $get('deliveryman') && $get('delivery_fee_type') === 'fixed'),

                        Components\TextInput::make('delivery_fee_per_km')
                            ->label(__('message.per_km_delivery_fee'))
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->visible(fn($get) =>
                            $get('deliveryman') && $get('delivery_fee_type') === 'per_km'),
                        Components\TextInput::make('latitude')
                            ->live()
                            ->hidden()
                            ->dehydrated(true),
                        Components\TextInput::make('longitude')
                            ->live()
                            ->hidden()
                            ->dehydrated(true),
                        Map::make('location')
                            ->label(__('message.location'))
                            ->mapControls([
                                'mapTypeControl'    => true,
                                'scaleControl'      => true,
                                'streetViewControl' => true,
                            ])
                            ->clickable()
                            ->defaultLocation([
                                31.1840033388827,
                                29.91577952653098
                            ])
                            ->required()
                            ->visible(fn($get) => $get('deliveryman') && $get('delivery_fee_type') === 'per_km')
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $set('latitude', $state['lat']);
                                $set('longitude', $state['lng']);
                            })
                            ->afterStateHydrated(function (callable $get, callable $set) {
                                $lat = $get('latitude');
                                $lng = $get('longitude');
                                if ($lat && $lng) {
                                    $set('location', ['lat' => (float) $lat, 'lng' => (float) $lng]);
                                }
                            })
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // تسجيل البيانات المستلمة للـ debugging
        \Log::info('Delivery Settings Data:', $data);
        
        if (isset($data['location']) && $data['location']) {
            $data['latitude'] = $data['location']['lat'];
            $data['longitude'] = $data['location']['lng'];
            unset($data['location']);
        }

        // حفظ حالة التوصيل
        $deliverymanValue = $data['deliveryman'] ? '1' : '0';
        \Log::info('Saving deliveryman:', ['value' => $deliverymanValue]);
        
        $saved = Setting::updateOrCreate(
            ['key' => 'deliveryman'],
            ['value' => $deliverymanValue, 'value_en' => $deliverymanValue, 'value_ar' => $deliverymanValue]
        );
        \Log::info('Deliveryman saved:', $saved->toArray());

        if ($data['deliveryman']) {
            // حفظ نوع رسوم التوصيل
            if (isset($data['delivery_fee_type'])) {
                \Log::info('Saving delivery_fee_type:', ['value' => $data['delivery_fee_type']]);
                
                $feeTypeSaved = Setting::updateOrCreate(
                    ['key' => 'delivery_fee_type'],
                    ['value' => $data['delivery_fee_type'], 'value_en' => $data['delivery_fee_type'], 'value_ar' => $data['delivery_fee_type']]
                );
                \Log::info('Fee type saved:', $feeTypeSaved->toArray());
            }

            // حفظ البيانات بناءً على نوع التوصيل
            if ($data['delivery_fee_type'] === 'fixed' && isset($data['delivery_fee_fixed'])) {
                Setting::updateOrCreate(
                    ['key' => 'delivery_fee_fixed'],
                    ['value' => $data['delivery_fee_fixed'], 'value_en' => $data['delivery_fee_fixed'], 'value_ar' => $data['delivery_fee_fixed']]
                );
                // مسح البيانات الأخرى
                Setting::whereIn('key', ['delivery_fee_per_km', 'latitude', 'longitude'])->delete();
            } elseif ($data['delivery_fee_type'] === 'per_km') {
                if (isset($data['delivery_fee_per_km'])) {
                    Setting::updateOrCreate(
                        ['key' => 'delivery_fee_per_km'],
                        ['value' => $data['delivery_fee_per_km'], 'value_en' => $data['delivery_fee_per_km'], 'value_ar' => $data['delivery_fee_per_km']]
                    );
                }
                if (isset($data['latitude'])) {
                    Setting::updateOrCreate(
                        ['key' => 'latitude'],
                        ['value' => $data['latitude'], 'value_en' => $data['latitude'], 'value_ar' => $data['latitude']]
                    );
                }
                if (isset($data['longitude'])) {
                    Setting::updateOrCreate(
                        ['key' => 'longitude'],
                        ['value' => $data['longitude'], 'value_en' => $data['longitude'], 'value_ar' => $data['longitude']]
                    );
                }
                // مسح البيانات الأخرى
                Setting::where('key', 'delivery_fee_fixed')->delete();
            } elseif ($data['delivery_fee_type'] === 'area') {
                \Log::info('Area delivery type selected - clearing other fee data');
                // في حالة التوصيل حسب المنطقة، نحتفظ فقط بالنوع
                Setting::whereIn('key', ['delivery_fee_fixed', 'delivery_fee_per_km', 'latitude', 'longitude'])->delete();
            }
        } else {
            // في حالة إيقاف التوصيل، نحذف فقط البيانات ولكن نحتفظ بالنوع المختار
            Setting::whereIn('key', [
                'delivery_fee_fixed',
                'delivery_fee_per_km',
                'latitude',
                'longitude',
            ])->delete();
        }

        Notification::make()
            ->title(__('message.settings_saved_successfully'))
            ->success()
            ->send();
        
        // مسح الكاش وإعادة التحميل
        cache()->flush();
        redirect()->to(DeliverySettings::getUrl());
    }
}
