<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name_ar', 'value_ar' => 'كوموسيونجي', 'value_en' => 'Comosyongy', 'type' => 'text'],
            ['key' => 'site_description_ar', 'value_ar' => 'الوجهة الرائدة في مجال أجهزة الحماية المنزلية', 'value_en' => 'The leading destination for home security devices', 'type' => 'text'],
            
            // Contact Information
            ['key' => 'contact_email', 'value_ar' => 'info@comosyongy.com', 'value_en' => 'info@comosyongy.com', 'type' => 'email'],
            ['key' => 'contact_phone', 'value_ar' => '+20 100 123 4567', 'value_en' => '+20 100 123 4567', 'type' => 'text'],
            ['key' => 'contact_whatsapp', 'value_ar' => '+20 100 123 4567', 'value_en' => '+20 100 123 4567', 'type' => 'text'],
            ['key' => 'contact_address_ar', 'value_ar' => 'القاهرة، مصر', 'value_en' => 'Cairo, Egypt', 'type' => 'text'],
            
            // Social Media
            ['key' => 'social_facebook', 'value_ar' => 'https://facebook.com/comosyongy', 'value_en' => 'https://facebook.com/comosyongy', 'type' => 'url'],
            ['key' => 'social_instagram', 'value_ar' => 'https://instagram.com/comosyongy', 'value_en' => 'https://instagram.com/comosyongy', 'type' => 'url'],
            ['key' => 'social_twitter', 'value_ar' => 'https://twitter.com/comosyongy', 'value_en' => 'https://twitter.com/comosyongy', 'type' => 'url'],
            
            // Shipping Settings
            ['key' => 'free_shipping_threshold', 'value_ar' => '2000', 'value_en' => '2000', 'type' => 'number'],
            ['key' => 'cairo_shipping_fee', 'value_ar' => '50', 'value_en' => '50', 'type' => 'number'],
            ['key' => 'other_cities_shipping_fee', 'value_ar' => '75', 'value_en' => '75', 'type' => 'number'],
            
            // Currency Settings
            ['key' => 'currency_code', 'value_ar' => 'EGP', 'value_en' => 'EGP', 'type' => 'text'],
            ['key' => 'currency_symbol', 'value_ar' => 'ج.م', 'value_en' => 'EGP', 'type' => 'text'],
            
            // Warranty Settings
            ['key' => 'warranty_period_years', 'value_ar' => '2', 'value_en' => '2', 'type' => 'number'],
            ['key' => 'return_period_days', 'value_ar' => '14', 'value_en' => '14', 'type' => 'number'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
