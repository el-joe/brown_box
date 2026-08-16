<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            'electronics' => [
                'name' => ['en' => 'Electronics', 'ar' => 'إلكترونيات'],
                'icon' => 'device-mobile',
                'children' => [
                    'mobiles-tablets' => ['en' => 'Mobiles & Tablets', 'ar' => 'موبايلات وتابلت'],
                    'laptops-computers' => ['en' => 'Laptops & Computers', 'ar' => 'لابتوبات وأجهزة كمبيوتر'],
                    'audio-headphones' => ['en' => 'Audio & Headphones', 'ar' => 'صوتيات وسماعات'],
                    'tvs-accessories' => ['en' => 'TVs & Accessories', 'ar' => 'شاشات وإكسسوارات'],
                ],
            ],
            'fashion' => [
                'name' => ['en' => 'Fashion', 'ar' => 'أزياء'],
                'icon' => 'hanger',
                'children' => [
                    'mens-clothing' => ['en' => "Men's Clothing", 'ar' => 'ملابس رجالي'],
                    'womens-clothing' => ['en' => "Women's Clothing", 'ar' => 'ملابس حريمي'],
                    'shoes' => ['en' => 'Shoes', 'ar' => 'أحذية'],
                ],
            ],
            'home-kitchen' => [
                'name' => ['en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ'],
                'icon' => 'home',
                'children' => [
                    'kitchen-appliances' => ['en' => 'Kitchen Appliances', 'ar' => 'أجهزة مطبخ'],
                    'home-decor' => ['en' => 'Home Decor', 'ar' => 'ديكور منزلي'],
                ],
            ],
            'beauty-personal-care' => [
                'name' => ['en' => 'Beauty & Personal Care', 'ar' => 'الجمال والعناية الشخصية'],
                'icon' => 'sparkles',
                'children' => [],
            ],
            'sports-outdoors' => [
                'name' => ['en' => 'Sports & Outdoors', 'ar' => 'رياضة وأنشطة خارجية'],
                'icon' => 'basketball',
                'children' => [],
            ],
        ];

        $sort = 0;
        foreach ($tree as $slug => $data) {
            $parent = Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'slug' => $slug,
                    'icon' => $data['icon'],
                    'is_active' => true,
                    'sort_order' => $sort++,
                ]
            );

            $childSort = 0;
            foreach ($data['children'] as $childSlug => $childName) {
                Category::firstOrCreate(
                    ['slug' => $childSlug],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'slug' => $childSlug,
                        'is_active' => true,
                        'sort_order' => $childSort++,
                    ]
                );
            }
        }
    }
}
