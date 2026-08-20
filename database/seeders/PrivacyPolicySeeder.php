<?php

namespace Database\Seeders;

use App\Models\PrivacyPolicy;
use Illuminate\Database\Seeder;

class PrivacyPolicySeeder extends Seeder
{
    public function run(): void
    {
        PrivacyPolicy::create([
            'title_ar' => 'سياسة الخصوصية',
            'title_en' => 'Privacy Policy',
            'slug' => 'privacy-policy',
            'content_ar' => '
# سياسة الخصوصية

## مقدمة
نحن في كوموسيونجي نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية.

## المعلومات التي نجمعها
- الاسم الكامل
- عنوان البريد الإلكتروني
- رقم الهاتف
- العنوان البريدي

## كيفية استخدام المعلومات
- معالجة الطلبات والمدفوعات
- التواصل معك بشأن طلباتك
- تحسين خدماتنا ومنتجاتنا

## أمن المعلومات
- تشفير SSL لجميع البيانات
- خوادم آمنة ومحمية
- وصول محدود للموظفين المصرح لهم

## حقوقك
- الوصول إلى بياناتك
- تصحيح المعلومات
- حذف بياناتك
',
            'content_en' => '
# Privacy Policy

## Introduction
At Comosyongy, we respect your privacy and protect your personal data.

## Information We Collect
- Full name
- Email address
- Phone number
- Postal address

## How We Use Information
- Process orders and payments
- Communicate about orders
- Improve our services

## Information Security
- SSL encryption for all data
- Secure servers
- Limited staff access

## Your Rights
- Access your data
- Correct information
- Delete your data
',
            'is_active' => true,
        ]);
    }
}
