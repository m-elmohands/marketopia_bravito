<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'title_ar' => 'تركيب أنظمة المراقبة',
                'title_en' => 'Surveillance Systems Installation',
                'description_ar' => 'نوفر خدمة تركيب احترافية لجميع أنواع أنظمة المراقبة، مع ضمان التركيب الصحيح.',
                'description_en' => 'We provide professional installation service for all types of surveillance systems.',
                'icon' => 'heroicon-o-video-camera',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title_ar' => 'الاستشارات الأمنية',
                'title_en' => 'Security Consultations',
                'description_ar' => 'فريق من الخبراء لتقييم احتياجاتك الأمنية وتقديم الحلول المناسبة.',
                'description_en' => 'A team of experts to assess your security needs and provide solutions.',
                'icon' => 'heroicon-o-shield-check',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title_ar' => 'الصيانة الدورية',
                'title_en' => 'Regular Maintenance',
                'description_ar' => 'خدمة صيانة دورية لضمان عمل أجهزتك بكفاءة عالية على مدار العام.',
                'description_en' => 'Regular maintenance service to ensure your devices work efficiently.',
                'icon' => 'heroicon-o-wrench-screwdriver',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title_ar' => 'المراقبة عن بُعد',
                'title_en' => 'Remote Monitoring',
                'description_ar' => 'إعداد أنظمة المراقبة عن بُعد عبر الهاتف المحمول من أي مكان في العالم.',
                'description_en' => 'Setup remote monitoring systems via mobile from anywhere in the world.',
                'icon' => 'heroicon-o-device-phone-mobile',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title_ar' => 'الدعم الفني 24/7',
                'title_en' => '24/7 Technical Support',
                'description_ar' => 'فريق دعم فني متاح على مدار الساعة لحل أي مشكلة.',
                'description_en' => 'Technical support team available around the clock.',
                'icon' => 'heroicon-o-phone',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
