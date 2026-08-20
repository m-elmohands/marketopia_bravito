<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_en' => 'Surveillance Cameras',
                'name_ar' => 'كاميرات المراقبة',
                'slug' => 'surveillance-cameras',
                'image' => 'categories/01KMZ8ZZCE6NDG3G805TYT1X4G.jpeg',
                'popular' => true,
                'children' => [
                    ['name_en' => 'Indoor Cameras', 'name_ar' => 'كاميرات داخلية', 'slug' => 'indoor-cameras', 'image' => 'categories/01KMZ91K0QHCR18YSVV75N54CX.jpeg'],
                    ['name_en' => 'Outdoor Cameras', 'name_ar' => 'كاميرات خارجية', 'slug' => 'outdoor-cameras', 'image' => 'categories/01KMZ93SBBZKT7M2ZQ80GAJ1EV.jpeg'],
                ],
            ],
            [
                'name_en' => 'Alarm Systems',
                'name_ar' => 'أنظمة الإنذار',
                'slug' => 'alarm-systems',
                'image' => 'categories/01KMZ95QXW0YWBQGMNRZYW8C3E.jpeg',
                'popular' => true,
                'children' => [
                    ['name_en' => 'Sensors', 'name_ar' => 'أجهزة الاستشعار', 'slug' => 'sensors', 'image' => 'categories/01KMZ98EX5R21QXVAM7PH2QZJ9.jpeg'],
                ],
            ],
            [
                'name_en' => 'Access Control',
                'name_ar' => 'أنظمة التحكم في الدخول',
                'slug' => 'access-control',
                'image' => 'categories/01KMZBJNMAC8GR32AF0845QT26.jpg',
                'popular' => true,
                'children' => [
                    ['name_en' => 'Smart Locks', 'name_ar' => 'الأقفال الذكية', 'slug' => 'smart-locks', 'image' => 'categories/01KMZBKN888R50Q31RXVQT0VQ9.jpeg'],
                ],
            ],
            [
                'name_en' => 'Networking',
                'name_ar' => 'حلول الشبكات',
                'slug' => 'networking',
                'image' => 'categories/01KMZBMK2HW5TX5YWR952GN6MS.jpeg',
                'popular' => true,
                'children' => [],
            ],
            [
                'name_en' => 'Smart Home',
                'name_ar' => 'المنزل الذكي',
                'slug' => 'smart-home',
                'image' => 'categories/01KMZBN9SP51VKVGWMN6NGAQFN.jpg',
                'popular' => true,
                'children' => [],
            ],
        ];

        DB::transaction(function () use ($categories): void {
            foreach ($categories as $categoryData) {
                $children = $categoryData['children'];
                unset($categoryData['children']);

                $parent = Category::updateOrCreate(
                    ['slug' => $categoryData['slug']],
                    [...$categoryData, 'parent_id' => null],
                );

                foreach ($children as $childData) {
                    Category::updateOrCreate(
                        ['slug' => $childData['slug']],
                        [...$childData, 'popular' => false, 'parent_id' => $parent->id],
                    );
                }
            }
        });
    }
}
