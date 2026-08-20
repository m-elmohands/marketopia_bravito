<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::updateOrCreate(
            ['name_en' => 'Bravito'],
            ['name_ar' => 'برافـيتو', 'image' => 'brands/01KMWNN7DV1YGT0HRH8G1R082D.jpg'],
        );

        $products = [
            ['category' => 'indoor-cameras', 'name_en' => '2K Smart Indoor Camera', 'name_ar' => 'كاميرا داخلية ذكية 2K', 'slug' => '2k-smart-indoor-camera', 'description' => 'Compact 2K indoor camera with night vision, motion alerts, two-way audio, and secure mobile access.', 'price' => 2499, 'discount_price' => 2199, 'quantity' => 35, 'max_order_quantity' => 4, 'images' => ['products/01KMZ9HWCAH1Y5PMZF4MBBS7NK.jpeg', 'products/01KMZ9WAWE2GCFSQJ8A2D1V7WS.jpg']],
            ['category' => 'outdoor-cameras', 'name_en' => '4MP Outdoor Bullet Camera', 'name_ar' => 'كاميرا مراقبة خارجية 4 ميجابكسل', 'slug' => '4mp-outdoor-bullet-camera', 'description' => 'Weather-resistant outdoor camera with clear 4MP imaging, infrared night vision, and reliable perimeter monitoring.', 'price' => 3299, 'discount_price' => null, 'quantity' => 28, 'max_order_quantity' => 6, 'images' => ['products/01KMZA7NM2H6QFPZ64R1EGKVES.jpeg', 'products/01KMZA7NMGY0MJMP853XZ1GPEM.jpeg']],
            ['category' => 'surveillance-cameras', 'name_en' => '8-Channel NVR Recorder', 'name_ar' => 'مسجل شبكي NVR ثماني القنوات', 'slug' => '8-channel-nvr-recorder', 'description' => 'Eight-channel network video recorder with remote playback, smart search, and expandable surveillance storage.', 'price' => 6499, 'discount_price' => 5999, 'quantity' => 16, 'max_order_quantity' => 2, 'images' => ['products/01KMZAMA6CCE0KAZCNVRM2JS6J.jpeg']],
            ['category' => 'sensors', 'name_en' => 'Wireless Door Sensor', 'name_ar' => 'حساس باب لاسلكي', 'slug' => 'wireless-door-sensor', 'description' => 'Low-profile wireless sensor that sends instant notifications whenever a protected door or window opens.', 'price' => 699, 'discount_price' => null, 'quantity' => 70, 'max_order_quantity' => 10, 'images' => ['products/01KMZB2W4BNDVRWNF29VQ52NXW.jpeg']],
            ['category' => 'alarm-systems', 'name_en' => 'Smart Alarm Starter Kit', 'name_ar' => 'مجموعة إنذار ذكية متكاملة', 'slug' => 'smart-alarm-starter-kit', 'description' => 'Complete starter kit with alarm hub, motion detector, door sensor, siren, and app-based control.', 'price' => 7499, 'discount_price' => 6799, 'quantity' => 14, 'max_order_quantity' => 2, 'images' => ['products/01KMZC29930PYE96V1QKGVK7YN.jpeg', 'products/01KMZC2994KJCVM8AY8Z7GFK69.jpeg']],
            ['category' => 'smart-locks', 'name_en' => 'Fingerprint Smart Door Lock', 'name_ar' => 'قفل باب ذكي بالبصمة', 'slug' => 'fingerprint-smart-door-lock', 'description' => 'Keyless smart lock with fingerprint, PIN, card, and emergency key access for homes and offices.', 'price' => 8999, 'discount_price' => 8199, 'quantity' => 20, 'max_order_quantity' => 3, 'images' => ['products/01KMZCA5N01HCH1GSZTPGPMWNS.jpeg']],
            ['category' => 'access-control', 'name_en' => 'RFID Access Control Terminal', 'name_ar' => 'جهاز تحكم في الدخول بتقنية RFID', 'slug' => 'rfid-access-control-terminal', 'description' => 'Standalone RFID terminal for secure employee and visitor access with simple enrollment and audit support.', 'price' => 4599, 'discount_price' => null, 'quantity' => 18, 'max_order_quantity' => 4, 'images' => ['products/01KMZCGSX64BXHMH1RTFF3VAVK.jpeg']],
            ['category' => 'networking', 'name_en' => '8-Port Gigabit PoE Switch', 'name_ar' => 'محول شبكة PoE جيجابت 8 منافذ', 'slug' => '8-port-gigabit-poe-switch', 'description' => 'Managed eight-port Gigabit PoE switch built to power cameras, access points, and connected security devices.', 'price' => 4199, 'discount_price' => 3899, 'quantity' => 25, 'max_order_quantity' => 5, 'images' => ['products/01KMZD0PM7Y8MDZN323TQ5VWWB.jpeg']],
            ['category' => 'smart-home', 'name_en' => 'Smart Video Doorbell', 'name_ar' => 'جرس باب ذكي بكاميرا', 'slug' => 'smart-video-doorbell', 'description' => 'Connected video doorbell with HD video, visitor alerts, two-way talk, and night monitoring.', 'price' => 3799, 'discount_price' => 3399, 'quantity' => 32, 'max_order_quantity' => 3, 'images' => ['products/01KMZD8BC1DHC640S6EWZYA4K4.jpeg', 'products/01KMZD8BC3NK7ET96SCBGW024Z.jpeg']],
            ['category' => 'smart-home', 'name_en' => 'Wi-Fi Smart Plug', 'name_ar' => 'مقبس كهربائي ذكي واي فاي', 'slug' => 'wifi-smart-plug', 'description' => 'Compact smart plug with schedules, remote power control, and energy monitoring through the mobile app.', 'price' => 799, 'discount_price' => 699, 'quantity' => 80, 'max_order_quantity' => 8, 'images' => ['products/01KMZDHZ2JW7AEMBQMBPRYNPS4.jpeg']],
        ];

        DB::transaction(function () use ($brand, $products): void {
            foreach ($products as $productData) {
                $category = Category::where('slug', $productData['category'])->first();
                if (! $category) {
                    throw new RuntimeException("Category [{$productData['category']}] is missing. Run CategorySeeder first.");
                }

                $images = $productData['images'];
                unset($productData['category'], $productData['images']);

                $product = Product::updateOrCreate(
                    ['slug' => $productData['slug']],
                    [...$productData, 'status' => '1', 'category_id' => $category->id, 'brand_id' => $brand->id],
                );

                $product->images()->delete();
                $product->images()->createMany(array_map(
                    fn (string $image): array => ['image' => $image],
                    $images,
                ));
            }
        });
    }
}
