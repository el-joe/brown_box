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
                'image' => 'https://images.unsplash.com/photo-1498049794561-7780e7231661?w=800&q=80',
                'children' => [
                    'mobiles-tablets' => [
                        'name' => ['en' => 'Mobiles & Tablets', 'ar' => 'موبايلات وتابلت'],
                        'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&q=80',
                    ],
                    'laptops-computers' => [
                        'name' => ['en' => 'Laptops & Computers', 'ar' => 'لابتوبات وأجهزة كمبيوتر'],
                        'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=800&q=80',
                    ],
                    'audio-headphones' => [
                        'name' => ['en' => 'Audio & Headphones', 'ar' => 'صوتيات وسماعات'],
                        'image' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=800&q=80',
                    ],
                    'tvs-accessories' => [
                        'name' => ['en' => 'TVs & Accessories', 'ar' => 'شاشات وإكسسوارات'],
                        'image' => 'https://images.unsplash.com/photo-1593359677879-a4bb92f829d1?w=800&q=80',
                    ],
                ],
            ],
            'fashion' => [
                'name' => ['en' => 'Fashion', 'ar' => 'أزياء'],
                'icon' => 'hanger',
                'image' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&q=80',
                'children' => [
                    'mens-clothing' => [
                        'name' => ['en' => "Men's Clothing", 'ar' => 'ملابس رجالي'],
                        'image' => 'https://images.unsplash.com/photo-1516257984-b1b4d707412e?w=800&q=80',
                    ],
                    'womens-clothing' => [
                        'name' => ['en' => "Women's Clothing", 'ar' => 'ملابس حريمي'],
                        'image' => 'https://images.unsplash.com/photo-1483985988355-763728e1935b?w=800&q=80',
                    ],
                    'shoes' => [
                        'name' => ['en' => 'Shoes', 'ar' => 'أحذية'],
                        'image' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=800&q=80',
                    ],
                ],
            ],
            'home-kitchen' => [
                'name' => ['en' => 'Home & Kitchen', 'ar' => 'المنزل والمطبخ'],
                'icon' => 'home',
                'image' => 'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=800&q=80',
                'children' => [
                    'kitchen-appliances' => [
                        'name' => ['en' => 'Kitchen Appliances', 'ar' => 'أجهزة مطبخ'],
                        'image' => 'https://images.unsplash.com/photo-1584269600464-37b1b58a9fe7?w=800&q=80',
                    ],
                    'home-decor' => [
                        'name' => ['en' => 'Home Decor', 'ar' => 'ديكور منزلي'],
                        'image' => 'https://images.unsplash.com/photo-1493663284031-b7e3aefcae8e?w=800&q=80',
                    ],
                ],
            ],
            'beauty-personal-care' => [
                'name' => ['en' => 'Beauty & Personal Care', 'ar' => 'الجمال والعناية الشخصية'],
                'icon' => 'sparkles',
                'image' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=800&q=80',
                'children' => [],
            ],
            'sports-outdoors' => [
                'name' => ['en' => 'Sports & Outdoors', 'ar' => 'رياضة وأنشطة خارجية'],
                'icon' => 'basketball',
                'image' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=800&q=80',
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
                    'image' => $data['image'],
                    'is_active' => true,
                    'sort_order' => $sort++,
                ]
            );

            $childSort = 0;
            foreach ($data['children'] as $childSlug => $childData) {
                Category::firstOrCreate(
                    ['slug' => $childSlug],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childData['name'],
                        'slug' => $childSlug,
                        'image' => $childData['image'],
                        'is_active' => true,
                        'sort_order' => $childSort++,
                    ]
                );
            }
        }
    }
}
