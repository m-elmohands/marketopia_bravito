<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            [
                'name_en' => 'Cairo',
                'name_ar' => 'القاهرة',
                'min_price' => 500,
                'areas' => [
                    ['name_en' => 'Nasr City', 'name_ar' => 'مدينة نصر', 'price' => 65],
                    ['name_en' => 'Heliopolis', 'name_ar' => 'مصر الجديدة', 'price' => 65],
                    ['name_en' => 'Maadi', 'name_ar' => 'المعادي', 'price' => 75],
                    ['name_en' => 'New Cairo', 'name_ar' => 'القاهرة الجديدة', 'price' => 85],
                    ['name_en' => 'Downtown Cairo', 'name_ar' => 'وسط البلد', 'price' => 60],
                    ['name_en' => 'Shorouk City', 'name_ar' => 'مدينة الشروق', 'price' => 95],
                ],
            ],
            [
                'name_en' => 'Giza',
                'name_ar' => 'الجيزة',
                'min_price' => 500,
                'areas' => [
                    ['name_en' => 'Dokki', 'name_ar' => 'الدقي', 'price' => 65],
                    ['name_en' => 'Mohandessin', 'name_ar' => 'المهندسين', 'price' => 65],
                    ['name_en' => 'Haram', 'name_ar' => 'الهرم', 'price' => 75],
                    ['name_en' => 'Sheikh Zayed City', 'name_ar' => 'مدينة الشيخ زايد', 'price' => 90],
                    ['name_en' => '6th of October City', 'name_ar' => 'مدينة السادس من أكتوبر', 'price' => 95],
                ],
            ],
            [
                'name_en' => 'Alexandria',
                'name_ar' => 'الإسكندرية',
                'min_price' => 650,
                'areas' => [
                    ['name_en' => 'Smouha', 'name_ar' => 'سموحة', 'price' => 95],
                    ['name_en' => 'Sidi Gaber', 'name_ar' => 'سيدي جابر', 'price' => 95],
                    ['name_en' => 'Miami', 'name_ar' => 'ميامي', 'price' => 105],
                    ['name_en' => 'Borg El Arab', 'name_ar' => 'برج العرب', 'price' => 125],
                ],
            ],
            [
                'name_en' => 'Qalyubia',
                'name_ar' => 'القليوبية',
                'min_price' => 600,
                'areas' => [
                    ['name_en' => 'Shubra El Kheima', 'name_ar' => 'شبرا الخيمة', 'price' => 80],
                    ['name_en' => 'Banha', 'name_ar' => 'بنها', 'price' => 100],
                    ['name_en' => 'Obour City', 'name_ar' => 'مدينة العبور', 'price' => 90],
                ],
            ],
            [
                'name_en' => 'Dakahlia',
                'name_ar' => 'الدقهلية',
                'min_price' => 700,
                'areas' => [
                    ['name_en' => 'Mansoura', 'name_ar' => 'المنصورة', 'price' => 120],
                    ['name_en' => 'Talkha', 'name_ar' => 'طلخا', 'price' => 125],
                    ['name_en' => 'Mit Ghamr', 'name_ar' => 'ميت غمر', 'price' => 135],
                ],
            ],
            [
                'name_en' => 'Gharbia',
                'name_ar' => 'الغربية',
                'min_price' => 700,
                'areas' => [
                    ['name_en' => 'Tanta', 'name_ar' => 'طنطا', 'price' => 120],
                    ['name_en' => 'El Mahalla El Kubra', 'name_ar' => 'المحلة الكبرى', 'price' => 130],
                ],
            ],
            [
                'name_en' => 'Sharqia',
                'name_ar' => 'الشرقية',
                'min_price' => 700,
                'areas' => [
                    ['name_en' => 'Zagazig', 'name_ar' => 'الزقازيق', 'price' => 120],
                    ['name_en' => '10th of Ramadan City', 'name_ar' => 'مدينة العاشر من رمضان', 'price' => 105],
                ],
            ],
            [
                'name_en' => 'Suez',
                'name_ar' => 'السويس',
                'min_price' => 800,
                'areas' => [
                    ['name_en' => 'Suez District', 'name_ar' => 'حي السويس', 'price' => 150],
                    ['name_en' => 'Arbaeen', 'name_ar' => 'الأربعين', 'price' => 150],
                ],
            ],
        ];

        DB::transaction(function () use ($cities): void {
            foreach ($cities as $cityData) {
                $areas = $cityData['areas'];
                unset($cityData['areas']);

                $city = City::updateOrCreate(
                    ['name_en' => $cityData['name_en']],
                    $cityData,
                );

                foreach ($areas as $areaData) {
                    Area::updateOrCreate(
                        ['city_id' => $city->id, 'name_en' => $areaData['name_en']],
                        [...$areaData, 'city_id' => $city->id],
                    );
                }
            }
        });
    }
}
