<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'apple' => ['en' => 'Apple', 'ar' => 'أبل'],
            'samsung' => ['en' => 'Samsung', 'ar' => 'سامسونج'],
            'xiaomi' => ['en' => 'Xiaomi', 'ar' => 'شاومي'],
            'sony' => ['en' => 'Sony', 'ar' => 'سوني'],
            'dell' => ['en' => 'Dell', 'ar' => 'ديل'],
            'hp' => ['en' => 'HP', 'ar' => 'إتش بي'],
            'lg' => ['en' => 'LG', 'ar' => 'إل جي'],
            'nike' => ['en' => 'Nike', 'ar' => 'نايكي'],
            'adidas' => ['en' => 'Adidas', 'ar' => 'أديداس'],
            'puma' => ['en' => 'Puma', 'ar' => 'بوما'],
            'philips' => ['en' => 'Philips', 'ar' => 'فيليبس'],
            'brown-box-basics' => ['en' => 'Brown Box Basics', 'ar' => 'براون بوكس بيسكس'],
        ];

        foreach ($brands as $slug => $name) {
            Brand::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'slug' => $slug, 'is_active' => true]
            );
        }
    }
}
