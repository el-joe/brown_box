<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'apple' => ['name' => ['en' => 'Apple', 'ar' => 'أبل'], 'logo' => 'https://images.unsplash.com/photo-1611186871348-b1ce696e52c9?w=400&q=80'],
            'samsung' => ['name' => ['en' => 'Samsung', 'ar' => 'سامسونج'], 'logo' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=400&q=80'],
            'xiaomi' => ['name' => ['en' => 'Xiaomi', 'ar' => 'شاومي'], 'logo' => 'https://images.unsplash.com/photo-1592286927505-1def25115481?w=400&q=80'],
            'sony' => ['name' => ['en' => 'Sony', 'ar' => 'سوني'], 'logo' => 'https://images.unsplash.com/photo-1587033411391-5d9e51cce126?w=400&q=80'],
            'dell' => ['name' => ['en' => 'Dell', 'ar' => 'ديل'], 'logo' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?w=400&q=80'],
            'hp' => ['name' => ['en' => 'HP', 'ar' => 'إتش بي'], 'logo' => 'https://images.unsplash.com/photo-1517430816045-df4b7de11d1d?w=400&q=80'],
            'lg' => ['name' => ['en' => 'LG', 'ar' => 'إل جي'], 'logo' => 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=400&q=80'],
            'nike' => ['name' => ['en' => 'Nike', 'ar' => 'نايكي'], 'logo' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=400&q=80'],
            'adidas' => ['name' => ['en' => 'Adidas', 'ar' => 'أديداس'], 'logo' => 'https://images.unsplash.com/photo-1518002171953-a080ee817e1f?w=400&q=80'],
            'puma' => ['name' => ['en' => 'Puma', 'ar' => 'بوما'], 'logo' => 'https://images.unsplash.com/photo-1595950653106-6c9ebd614d3a?w=400&q=80'],
            'philips' => ['name' => ['en' => 'Philips', 'ar' => 'فيليبس'], 'logo' => 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=400&q=80'],
            'brown-box-basics' => ['name' => ['en' => 'Brown Box Basics', 'ar' => 'براون بوكس بيسكس'], 'logo' => 'https://images.unsplash.com/photo-1560393464-5c69a73c5770?w=400&q=80'],
        ];

        foreach ($brands as $slug => $data) {
            Brand::updateOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'slug' => $slug, 'logo' => $data['logo'], 'is_active' => true]
            );
        }
    }
}
