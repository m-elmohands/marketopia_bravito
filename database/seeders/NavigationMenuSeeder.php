<?php

namespace Database\Seeders;

use App\Models\NavigationMenu;
use Illuminate\Database\Seeder;

class NavigationMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'title_ar' => 'الرئيسية',
                'title_en' => 'Home',
                'url' => '/',
                'location' => 'header',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title_ar' => 'المنتجات',
                'title_en' => 'Products',
                'url' => '/products',
                'location' => 'header',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title_ar' => 'الخدمات',
                'title_en' => 'Services',
                'url' => '/services',
                'location' => 'header',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
                'url' => '/about',
                'location' => 'header',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title_ar' => 'اتصل بنا',
                'title_en' => 'Contact Us',
                'url' => '/contact',
                'location' => 'header',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'url' => '/privacy-policy',
                'location' => 'footer',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title_ar' => 'شروط الخدمة',
                'title_en' => 'Terms of Service',
                'url' => '/terms-of-service',
                'location' => 'footer',
                'order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($menus as $menu) {
            NavigationMenu::create($menu);
        }
    }
}
