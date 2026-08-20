<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserTableSeeder::class,
            ConfigTableSeeder::class,
            SettingTableSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            PaymentMethodSeeder::class,
            CitySeeder::class,

            // Content Seeders
            AboutSectionSeeder::class,
            BlogPostSeeder::class,
            FAQSeeder::class,
            ServiceSeeder::class,
            TestimonialSeeder::class,
            ContactMessageSeeder::class,
            NavigationMenuSeeder::class,
            ShippingReturnSeeder::class,
            PrivacyPolicySeeder::class,
            TermsOfServiceSeeder::class,
            SettingSeeder::class,
        ]);
    }
}
