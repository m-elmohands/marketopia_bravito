<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'client_name_ar' => 'أحمد محمد',
                'client_name_en' => 'Ahmed Mohamed',
                'rating' => 5,
                'testimonial_ar' => 'منتجات ممتازة وخدمة عملاء رائعة! ركبت نظام مراقبة كامل لمنزلي والنتيجة فاقت توقعاتي.',
                'testimonial_en' => 'Excellent products and great customer service! I installed a complete surveillance system.',
                'client_image' => 'testimonials/customer1.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'client_name_ar' => 'سارة عبدالله',
                'client_name_en' => 'Sarah Abdullah',
                'rating' => 5,
                'testimonial_ar' => 'أفضل متجر لأجهزة الأمن المنزلي! الأسعار معقولة والمنتجات أصلية 100%.',
                'testimonial_en' => 'The best home security devices store! Reasonable prices and 100% original products.',
                'client_image' => 'testimonials/customer2.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'client_name_ar' => 'محمود حسن',
                'client_name_en' => 'Mahmoud Hassan',
                'rating' => 4,
                'testimonial_ar' => 'تجربة رائعة من الطلب حتى التركيب. التطبيق سهل الاستخدام جداً.',
                'testimonial_en' => 'Great experience from ordering to installation. The app is very easy to use.',
                'client_image' => 'testimonials/customer3.jpg',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'client_name_ar' => 'فاطمة علي',
                'client_name_en' => 'Fatima Ali',
                'rating' => 5,
                'testimonial_ar' => 'ممتنة جداً لفريق كوموسيونجي. الآن أشعر بالأمان الكامل في منزلي.',
                'testimonial_en' => 'Very grateful to the Comosyongy team. Now I feel completely safe at home.',
                'client_image' => 'testimonials/customer4.jpg',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
