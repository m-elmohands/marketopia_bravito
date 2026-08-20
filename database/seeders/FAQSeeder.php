<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question_ar' => 'ما هي المدة الزمنية للضمان على المنتجات؟',
                'question_en' => 'What is the warranty period for products?',
                'answer_ar' => 'جميع منتجاتنا تأتي بضمان لمدة سنتين من تاريخ الشراء. الضمان يشمل العيوب الصناعية وقطع الغيار الأصلية.',
                'answer_en' => 'All our products come with a 2-year warranty from the date of purchase.',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'question_ar' => 'هل يمكنني تركيب أجهزة المراقبة بنفسي؟',
                'question_en' => 'Can I install the surveillance devices myself?',
                'answer_ar' => 'نعم، معظم أجهزتنا سهلة التركيب وتأتي مع دليل تعليمات مفصل بالعربية.',
                'answer_en' => 'Yes, most of our devices are easy to install and come with detailed instructions.',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'question_ar' => 'كم من الوقت تستغرق عملية الشحن؟',
                'question_en' => 'How long does shipping take?',
                'answer_ar' => 'نقوم بالشحن خلال 24 ساعة. التوصيل داخل القاهرة 1-2 يوم، والمحافظات 3-5 أيام.',
                'answer_en' => 'We ship within 24 hours. Delivery in Cairo takes 1-2 days, other cities 3-5 days.',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'question_ar' => 'هل تعمل الكاميرات في الظلام؟',
                'question_en' => 'Do the cameras work in the dark?',
                'answer_ar' => 'نعم، جميع كاميراتنا مزودة بتقنية الرؤية الليلية بمدى يصل إلى 30 متر.',
                'answer_en' => 'Yes, all our cameras have night vision up to 30 meters range.',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'question_ar' => 'ما هي طرق الدفع المتاحة؟',
                'question_en' => 'What payment methods are available?',
                'answer_ar' => 'نقبل الدفع النقدي عند الاستلام، البطاقات الائتمانية، والمحافظ الإلكترونية.',
                'answer_en' => 'We accept cash on delivery, credit cards, and electronic wallets.',
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}
