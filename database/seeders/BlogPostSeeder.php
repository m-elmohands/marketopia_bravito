<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title_ar' => 'أفضل 5 أنظمة مراقبة منزلية لعام 2024',
                'title_en' => 'Top 5 Home Surveillance Systems for 2024',
                'slug' => 'top-5-home-surveillance-systems-2024',
                'content_ar' => 'في عالم يزداد فيه الوعي بأهمية الأمن المنزلي، أصبحت أنظمة المراقبة ضرورة وليست رفاهية. نستعرض في هذا المقال أفضل 5 أنظمة مراقبة منزلية متوفرة حالياً، مع مقارنة شاملة للمميزات والأسعار.',
                'content_en' => 'In a world where awareness of home security importance is growing, surveillance systems have become a necessity, not a luxury. In this article, we review the top 5 home surveillance systems currently available.',
                'excerpt_ar' => 'دليلك الشامل لاختيار أفضل نظام مراقبة منزلي يناسب احتياجاتك وميزانيتك',
                'excerpt_en' => 'Your comprehensive guide to choosing the best home surveillance system',
                'featured_image' => 'blog/surveillance-systems.jpg',
                'user_id' => 1,
                'published_at' => now()->subDays(5),
                'is_active' => true,
                'views_count' => 250,
            ],
            [
                'title_ar' => 'كيف تؤمن منزلك بالكامل: دليل المبتدئين',
                'title_en' => 'How to Fully Secure Your Home: Beginner\'s Guide',
                'slug' => 'how-to-fully-secure-your-home-beginners-guide',
                'content_ar' => 'تأمين المنزل ليس مجرد تركيب كاميرا على الباب. إنه نظام متكامل يشمل عدة طبقات من الحماية.',
                'content_en' => 'Securing your home is not just about installing a camera on the door. It\'s a comprehensive system.',
                'excerpt_ar' => 'خطوات عملية وبسيطة لتحويل منزلك إلى قلعة آمنة',
                'excerpt_en' => 'Practical and simple steps to turn your home into a secure fortress',
                'featured_image' => 'blog/home-security-guide.jpg',
                'user_id' => 1,
                'published_at' => now()->subDays(12),
                'is_active' => true,
                'views_count' => 380,
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::create($post);
        }
    }
}
