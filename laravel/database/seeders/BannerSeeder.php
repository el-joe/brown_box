<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => ['en' => 'MacBook Pro 14" M3', 'ar' => 'ماك بوك برو 14 بوصة M3'],
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=1200&q=80',
                'type' => 'category',
                'url' => 'laptops-computers',
                'sort_order' => 1,
            ],
            [
                'title' => ['en' => 'HP LaserJet Pro Printer', 'ar' => 'طابعة إتش بي ليزر جيت برو'],
                'image' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eabd?w=1200&q=80',
                'type' => 'category',
                'url' => 'electronics',
                'sort_order' => 2,
            ],
            [
                'title' => ['en' => 'Samsung Galaxy Smartphones', 'ar' => 'هواتف سامسونج جالاكسي'],
                'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=1200&q=80',
                'type' => 'category',
                'url' => 'mobiles-tablets',
                'sort_order' => 3,
            ],
            [
                'title' => ['en' => 'Sony Wireless Headphones', 'ar' => 'سماعات سوني اللاسلكية'],
                'image' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=1200&q=80',
                'type' => 'category',
                'url' => 'audio-headphones',
                'sort_order' => 4,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['title->en' => $banner['title']['en']],
                [
                    'title' => $banner['title'],
                    'image' => $banner['image'],
                    'type' => $banner['type'],
                    'url' => $banner['url'],
                    'sort_order' => $banner['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
