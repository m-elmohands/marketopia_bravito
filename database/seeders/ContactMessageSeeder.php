<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [
            [
                'name' => 'علي محمود',
                'email' => 'ali.mahmoud@example.com',
                'phone' => '+201012345678',
                'message' => 'السلام عليكم، أرغب في معرفة أسعار كاميرات المراقبة الخارجية المقاومة للماء.',
                'created_at' => now()->subDays(3),
            ],
            [
                'name' => 'نورا أحمد',
                'email' => 'nora.ahmed@example.com',
                'phone' => '+201098765432',
                'message' => 'مرحباً، لدي مشكلة في الاتصال بالكاميرات عبر التطبيق.',
                'created_at' => now()->subDay(),
            ],
            [
                'name' => 'كريم عبدالعزيز',
                'email' => 'karim.aziz@example.com',
                'phone' => '+201155554444',
                'message' => 'أحتاج إلى عرض سعر لتركيب نظام مراقبة كامل لفيلا.',
                'created_at' => now()->subHours(6),
            ],
        ];

        foreach ($messages as $message) {
            ContactMessage::create($message);
        }
    }
}
