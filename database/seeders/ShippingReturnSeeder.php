<?php

namespace Database\Seeders;

use App\Models\ShippingReturn;
use Illuminate\Database\Seeder;

class ShippingReturnSeeder extends Seeder
{
    public function run(): void
    {
        ShippingReturn::create([
            'title_ar' => 'سياسة الشحن والإرجاع',
            'title_en' => 'Shipping and Returns Policy',
            'slug' => 'shipping-and-returns-policy',
            'content_ar' => '
## سياسة الشحن

### أوقات الشحن
- نقوم بمعالجة الطلبات خلال 24 ساعة
- التوصيل داخل القاهرة: 1-2 يوم عمل
- التوصيل للمحافظات: 3-5 أيام عمل

### تكلفة الشحن
- الشحن مجاني للطلبات فوق 2000 جنيه
- رسوم الشحن داخل القاهرة: 50 جنيه
- رسوم الشحن للمحافظات: 75 جنيه

## سياسة الإرجاع

### حق الإرجاع
- يمكنك إرجاع المنتج خلال 14 يوماً
- يجب أن يكون المنتج في حالته الأصلية
- يجب تقديم فاتورة الشراء

### الاستبدال
- استبدال مجاني للمنتجات المعيبة
- الاستبدال خلال 3-5 أيام عمل
',
            'content_en' => '
## Shipping Policy

### Shipping Times
- We process orders within 24 hours
- Delivery in Cairo: 1-2 business days
- Delivery to other cities: 3-5 business days

### Shipping Costs
- Free shipping for orders over 2000 EGP
- Cairo shipping fee: 50 EGP
- Other cities shipping fee: 75 EGP

## Returns Policy

### Right to Return
- You can return products within 14 days
- Product must be in original condition
- Purchase invoice required

### Exchange
- Free exchange for defective products
- Exchange within 3-5 business days
',
            'is_active' => true,
        ]);
    }
}
