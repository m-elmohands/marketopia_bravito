<?php

namespace Database\Seeders;

use App\Models\AboutSection;
use Illuminate\Database\Seeder;

class AboutSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'title_ar' => 'من نحن',
                'title_en' => 'About Us',
                'description_ar' => 'كوموسيونجي هي الوجهة الرائدة في مجال أجهزة الحماية المنزلية والأمن المنزلي. نقدم أحدث التقنيات في أنظمة المراقبة، أجهزة الإنذار، وحلول الأمن الذكية لحماية منزلك وعائلتك.',
                'description_en' => 'Comosyongy is the leading destination for home security and protection devices. We provide the latest technologies in surveillance systems, alarm devices, and smart security solutions to protect your home and family.',
                'image' => 'about/about-us.jpg',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title_ar' => 'رؤيتنا',
                'title_en' => 'Our Vision',
                'description_ar' => 'نسعى لأن نكون الخيار الأول في منطقة الشرق الأوسط لتوفير حلول الحماية المنزلية المتكاملة، مع التركيز على الجودة العالية والتكنولوجيا المتطورة وخدمة العملاء المتميزة.',
                'description_en' => 'We strive to be the first choice in the Middle East for comprehensive home security solutions, focusing on high quality, advanced technology, and excellent customer service.',
                'image' => 'about/vision.jpg',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title_ar' => 'مهمتنا',
                'title_en' => 'Our Mission',
                'description_ar' => 'توفير أفضل أجهزة الحماية المنزلية بأسعار تنافسية، مع ضمان سهولة التركيب والاستخدام. نلتزم بتقديم منتجات موثوقة وخدمة ما بعد البيع المتميزة لضمان راحة البال لعملائنا.',
                'description_en' => 'Providing the best home security devices at competitive prices, while ensuring easy installation and use. We are committed to delivering reliable products and excellent after-sales service to ensure peace of mind for our customers.',
                'image' => 'about/mission.jpg',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title_ar' => 'لماذا نحن',
                'title_en' => 'Why Choose Us',
                'description_ar' => 'نتميز بخبرة تزيد عن 10 سنوات في مجال الأمن المنزلي، فريق متخصص من الخبراء، منتجات معتمدة عالمياً، ضمان شامل على جميع المنتجات، وخدمة عملاء متاحة على مدار الساعة.',
                'description_en' => 'We stand out with over 10 years of experience in home security, a specialized team of experts, internationally certified products, comprehensive warranty on all products, and 24/7 customer service.',
                'image' => 'about/why-us.jpg',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($sections as $section) {
            AboutSection::create($section);
        }
    }
}
