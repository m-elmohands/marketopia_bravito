<?php

namespace Database\Seeders;

use App\Models\TermsOfService;
use Illuminate\Database\Seeder;

class TermsOfServiceSeeder extends Seeder
{
    public function run(): void
    {
        TermsOfService::create([
            'title_ar' => 'شروط الخدمة',
            'title_en' => 'Terms of Service',
            'slug' => 'terms-of-service',
            'content_ar' => '
# شروط الخدمة

## القبول بالشروط
باستخدامك لموقع كوموسيونجي، فإنك توافق على الالتزام بهذه الشروط.

## التسجيل والحساب
- يجب أن تكون بالغاً (18 عاماً أو أكثر)
- يجب تقديم معلومات دقيقة
- أنت مسؤول عن سرية كلمة المرور

## المنتجات والطلبات
- جميع الطلبات تخضع للتوافر
- نحتفظ بحق رفض أي طلب
- الأسعار قابلة للتغيير

## الضمان والإرجاع
- ضمان لمدة سنتين على جميع المنتجات
- الضمان يغطي العيوب الصناعية فقط
- إرجاع خلال 14 يوماً

## المسؤولية
- نقدم الموقع والخدمات "كما هي"
- لا نضمن خلو الموقع من الأخطاء

## القانون الحاكم
تخضع هذه الشروط لقوانين جمهورية مصر العربية
',
            'content_en' => '
# Terms of Service

## Acceptance of Terms
By using Comosyongy website, you agree to these terms.

## Registration and Account
- Must be 18 years or older
- Must provide accurate information
- You are responsible for password security

## Products and Orders
- All orders subject to availability
- We reserve right to refuse orders
- Prices subject to change

## Warranty and Returns
- 2-year warranty on all products
- Warranty covers manufacturing defects only
- Returns within 14 days

## Liability
- We provide services "as is"
- We do not guarantee error-free service

## Governing Law
These terms are subject to Egyptian law
',
            'is_active' => true,
        ]);
    }
}
