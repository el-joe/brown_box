<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\ProductVariantAttribute;
use App\Models\Stock;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    private array $attributeValues = [];

    public function run(): void
    {
        $warehouse = Warehouse::where('is_default', true)->first() ?? Warehouse::first();

        $this->seedAttributes();
        $this->seedSimpleProducts($warehouse);
        $this->seedVariantProducts($warehouse);
    }

    private function seedAttributes(): void
    {
        $definitions = [
            'color' => [
                'name' => ['en' => 'Color', 'ar' => 'اللون'],
                'values' => [
                    'black' => ['en' => 'Black', 'ar' => 'أسود'],
                    'white' => ['en' => 'White', 'ar' => 'أبيض'],
                    'blue' => ['en' => 'Blue', 'ar' => 'أزرق'],
                    'red' => ['en' => 'Red', 'ar' => 'أحمر'],
                    'silver' => ['en' => 'Silver', 'ar' => 'فضي'],
                ],
            ],
            'storage' => [
                'name' => ['en' => 'Storage', 'ar' => 'سعة التخزين'],
                'values' => [
                    '128gb' => ['en' => '128GB', 'ar' => '128 جيجا', 'extra_price' => 0],
                    '256gb' => ['en' => '256GB', 'ar' => '256 جيجا', 'extra_price' => 1500],
                    '512gb' => ['en' => '512GB', 'ar' => '512 جيجا', 'extra_price' => 3500],
                ],
            ],
            'size' => [
                'name' => ['en' => 'Size', 'ar' => 'المقاس'],
                'values' => [
                    's' => ['en' => 'S', 'ar' => 'سمول'],
                    'm' => ['en' => 'M', 'ar' => 'ميديم'],
                    'l' => ['en' => 'L', 'ar' => 'لارج'],
                    'xl' => ['en' => 'XL', 'ar' => 'إكس لارج'],
                    '40' => ['en' => '40', 'ar' => '40'],
                    '41' => ['en' => '41', 'ar' => '41'],
                    '42' => ['en' => '42', 'ar' => '42'],
                    '43' => ['en' => '43', 'ar' => '43'],
                ],
            ],
        ];

        foreach ($definitions as $attrKey => $definition) {
            $attribute = ProductAttribute::firstOrCreate(
                ['name->en' => $definition['name']['en']],
                ['name' => $definition['name']]
            );

            foreach ($definition['values'] as $valueKey => $value) {
                $extraPrice = $value['extra_price'] ?? 0;
                unset($value['extra_price']);

                $attributeValue = ProductAttributeValue::firstOrCreate(
                    [
                        'product_attribute_id' => $attribute->id,
                        'value->en' => $value['en'],
                    ],
                    [
                        'value' => $value,
                        'extra_price' => $extraPrice,
                    ]
                );

                $this->attributeValues["{$attrKey}.{$valueKey}"] = $attributeValue;
            }

            $this->attributeValues["attribute.{$attrKey}"] = $attribute;
        }
    }

    private function seedSimpleProducts(?Warehouse $warehouse): void
    {
        $products = [
            [
                'category' => 'laptops-computers', 'brand' => 'apple',
                'name' => ['en' => 'MacBook Pro 14" M3', 'ar' => 'ماك بوك برو 14 بوصة M3'],
                'description' => ['en' => 'Apple MacBook Pro 14-inch with M3 chip, 16GB RAM, 512GB SSD.', 'ar' => 'ماك بوك برو 14 بوصة بمعالج M3 وذاكرة 16 جيجا وتخزين 512 جيجا.'],
                'sku' => 'MBP14-M3-512', 'price' => 89999, 'compare_price' => 94999, 'qty' => 15,
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=800&q=80',
            ],
            [
                'category' => 'laptops-computers', 'brand' => 'dell',
                'name' => ['en' => 'Dell XPS 13', 'ar' => 'ديل XPS 13'],
                'description' => ['en' => 'Dell XPS 13 ultrabook, Intel Core i7, 16GB RAM, 512GB SSD.', 'ar' => 'لابتوب ديل XPS 13 بمعالج i7 وذاكرة 16 جيجا وتخزين 512 جيجا.'],
                'sku' => 'DELL-XPS13-I7', 'price' => 54999, 'compare_price' => null, 'qty' => 20,
                'image' => 'https://images.unsplash.com/photo-1593642702821-c8da6771f0c6?w=800&q=80',
            ],
            [
                'category' => 'laptops-computers', 'brand' => 'hp',
                'name' => ['en' => 'HP LaserJet Pro Printer', 'ar' => 'طابعة إتش بي ليزر جيت برو'],
                'description' => ['en' => 'Compact monochrome laser printer for home and office.', 'ar' => 'طابعة ليزر أبيض وأسود مدمجة للمنزل والمكتب.'],
                'sku' => 'HP-LJ-PRO-M15', 'price' => 4999, 'compare_price' => null, 'qty' => 30,
                'image' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?w=800&q=80',
            ],
            [
                'category' => 'audio-headphones', 'brand' => 'sony',
                'name' => ['en' => 'Sony WH-1000XM5 Headphones', 'ar' => 'سماعة سوني WH-1000XM5'],
                'description' => ['en' => 'Industry-leading noise-cancelling wireless headphones.', 'ar' => 'سماعة لاسلكية بخاصية إلغاء الضوضاء الرائدة في مجالها.'],
                'sku' => 'SONY-WH1000XM5', 'price' => 15999, 'compare_price' => 17999, 'qty' => 25,
                'image' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=800&q=80',
            ],
            [
                'category' => 'audio-headphones', 'brand' => 'xiaomi',
                'name' => ['en' => 'Xiaomi Redmi Buds 4', 'ar' => 'سماعة شاومي ريدمي بدز 4'],
                'description' => ['en' => 'True wireless earbuds with active noise cancellation.', 'ar' => 'سماعة لاسلكية بالكامل مع خاصية إلغاء الضوضاء.'],
                'sku' => 'XIAOMI-RB4', 'price' => 1899, 'compare_price' => null, 'qty' => 60,
                'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=800&q=80',
            ],
            [
                'category' => 'tvs-accessories', 'brand' => 'lg',
                'name' => ['en' => 'LG 55" 4K UHD Smart TV', 'ar' => 'شاشة إل جي 55 بوصة 4K الذكية'],
                'description' => ['en' => '55-inch 4K UHD Smart TV with WebOS.', 'ar' => 'شاشة ذكية 55 بوصة دقة 4K مع نظام WebOS.'],
                'sku' => 'LG-55UQ-4K', 'price' => 27999, 'compare_price' => 31999, 'qty' => 12,
                'image' => 'https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=800&q=80',
            ],
            [
                'category' => 'kitchen-appliances', 'brand' => 'philips',
                'name' => ['en' => 'Philips Air Fryer XXL', 'ar' => 'قلاية هوائية فيليبس XXL'],
                'description' => ['en' => 'Large capacity air fryer for healthier cooking.', 'ar' => 'قلاية هوائية سعة كبيرة لطهي صحي.'],
                'sku' => 'PHILIPS-AF-XXL', 'price' => 6499, 'compare_price' => null, 'qty' => 18,
                'image' => 'https://images.unsplash.com/photo-1626200419199-391ae4be7a41?w=800&q=80',
            ],
            [
                'category' => 'home-decor', 'brand' => 'brown-box-basics',
                'name' => ['en' => 'Modern Ceramic Table Lamp', 'ar' => 'لمبة طاولة سيراميك عصرية'],
                'description' => ['en' => 'Minimalist ceramic table lamp for living rooms and bedrooms.', 'ar' => 'لمبة طاولة سيراميك بتصميم بسيط لغرف المعيشة والنوم.'],
                'sku' => 'BB-LAMP-CER01', 'price' => 899, 'compare_price' => null, 'qty' => 40,
                'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=800&q=80',
            ],
            [
                'category' => 'beauty-personal-care', 'brand' => 'philips',
                'name' => ['en' => 'Philips Hair Dryer 2100W', 'ar' => 'مجفف شعر فيليبس 2100 وات'],
                'description' => ['en' => 'Powerful hair dryer with ThermoProtect technology.', 'ar' => 'مجفف شعر قوي بتقنية حماية حرارية.'],
                'sku' => 'PHILIPS-HD2100', 'price' => 1299, 'compare_price' => null, 'qty' => 35,
                'image' => 'https://images.unsplash.com/photo-1522338242992-e1a54906a8da?w=800&q=80',
            ],
            [
                'category' => 'sports-outdoors', 'brand' => 'brown-box-basics',
                'name' => ['en' => 'Non-Slip Yoga Mat', 'ar' => 'مرتبة يوجا مانعة للانزلاق'],
                'description' => ['en' => 'Eco-friendly non-slip yoga mat, 6mm thickness.', 'ar' => 'مرتبة يوجا صديقة للبيئة مانعة للانزلاق بسمك 6 مم.'],
                'sku' => 'BB-YOGA-MAT01', 'price' => 649, 'compare_price' => null, 'qty' => 50,
                'image' => 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=800&q=80',
            ],
        ];

        foreach ($products as $data) {
            $product = Product::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => Category::where('slug', $data['category'])->value('id'),
                    'brand_id' => Brand::where('slug', $data['brand'])->value('id'),
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']['en']),
                    'description' => $data['description'],
                    'short_description' => ['en' => $data['name']['en'], 'ar' => $data['name']['ar']],
                    'price' => $data['price'],
                    'compare_price' => $data['compare_price'],
                    'cost_price' => round($data['price'] * 0.7, 2),
                    'has_variants' => false,
                    'is_active' => true,
                    'is_featured' => (bool) random_int(0, 1),
                ]
            );

            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                ['path' => $data['image'], 'type' => 'image', 'sort_order' => 0]
            );

            $this->seedExtraGalleryMedia($product);

            if ($warehouse) {
                Stock::firstOrCreate(
                    ['product_id' => $product->id, 'variant_id' => null, 'warehouse_id' => $warehouse->id],
                    ['qty' => $data['qty'], 'min_qty_alert' => 5]
                );
            }
        }
    }

    private function seedVariantProducts(?Warehouse $warehouse): void
    {
        $products = [
            [
                'category' => 'mobiles-tablets', 'brand' => 'apple',
                'name' => ['en' => 'iPhone 15 Pro', 'ar' => 'آيفون 15 برو'],
                'description' => ['en' => 'Apple iPhone 15 Pro with A17 Pro chip and titanium design.', 'ar' => 'آيفون 15 برو بمعالج A17 برو وتصميم تيتانيوم.'],
                'sku' => 'IPH15PRO', 'price' => 54999,
                'image' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&q=80',
                'variants' => [
                    ['color' => 'black', 'storage' => '128gb', 'qty' => 10],
                    ['color' => 'white', 'storage' => '256gb', 'qty' => 8],
                    ['color' => 'blue', 'storage' => '512gb', 'qty' => 5],
                ],
            ],
            [
                'category' => 'mobiles-tablets', 'brand' => 'samsung',
                'name' => ['en' => 'Samsung Galaxy S24', 'ar' => 'سامسونج جالاكسي S24'],
                'description' => ['en' => 'Samsung Galaxy S24 with AI-powered camera and display.', 'ar' => 'سامسونج جالاكسي S24 بكاميرا وشاشة مدعومة بالذكاء الاصطناعي.'],
                'sku' => 'SGS24', 'price' => 39999,
                'image' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&q=80',
                'variants' => [
                    ['color' => 'black', 'storage' => '128gb', 'qty' => 12],
                    ['color' => 'silver', 'storage' => '256gb', 'qty' => 10],
                    ['color' => 'red', 'storage' => '512gb', 'qty' => 6],
                ],
            ],
            [
                'category' => 'shoes', 'brand' => 'nike',
                'name' => ['en' => 'Nike Air Max 90', 'ar' => 'نايكي إير ماكس 90'],
                'description' => ['en' => 'Classic Nike Air Max 90 sneakers.', 'ar' => 'حذاء نايكي إير ماكس 90 الكلاسيكي.'],
                'sku' => 'NIKE-AM90', 'price' => 4999,
                'image' => 'https://images.unsplash.com/photo-1600185365483-26d7a4cc7519?w=800&q=80',
                'variants' => [
                    ['size' => '40', 'qty' => 15],
                    ['size' => '41', 'qty' => 20],
                    ['size' => '42', 'qty' => 18],
                    ['size' => '43', 'qty' => 10],
                ],
            ],
            [
                'category' => 'shoes', 'brand' => 'puma',
                'name' => ['en' => 'Puma Running Shoes', 'ar' => 'حذاء بوما للجري'],
                'description' => ['en' => 'Lightweight Puma running shoes for daily training.', 'ar' => 'حذاء بوما خفيف الوزن للجري اليومي.'],
                'sku' => 'PUMA-RUN01', 'price' => 3299,
                'image' => 'https://images.unsplash.com/photo-1608231387042-66d1773070a5?w=800&q=80',
                'variants' => [
                    ['size' => '40', 'qty' => 14],
                    ['size' => '41', 'qty' => 16],
                    ['size' => '42', 'qty' => 12],
                ],
            ],
            [
                'category' => 'mens-clothing', 'brand' => 'adidas',
                'name' => ['en' => 'Adidas Essentials T-Shirt', 'ar' => 'تيشيرت أديداس إسنشيالز'],
                'description' => ['en' => 'Cotton crew-neck t-shirt for everyday comfort.', 'ar' => 'تيشيرت قطن برقبة دائرية مريح للاستخدام اليومي.'],
                'sku' => 'ADIDAS-TEE01', 'price' => 899,
                'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=80',
                'variants' => [
                    ['color' => 'black', 'size' => 's', 'qty' => 20],
                    ['color' => 'black', 'size' => 'm', 'qty' => 25],
                    ['color' => 'white', 'size' => 'l', 'qty' => 18],
                    ['color' => 'white', 'size' => 'xl', 'qty' => 12],
                ],
            ],
        ];

        foreach ($products as $data) {
            $product = Product::firstOrCreate(
                ['sku' => $data['sku']],
                [
                    'category_id' => Category::where('slug', $data['category'])->value('id'),
                    'brand_id' => Brand::where('slug', $data['brand'])->value('id'),
                    'name' => $data['name'],
                    'slug' => Str::slug($data['name']['en']),
                    'description' => $data['description'],
                    'short_description' => ['en' => $data['name']['en'], 'ar' => $data['name']['ar']],
                    'price' => $data['price'],
                    'compare_price' => null,
                    'cost_price' => round($data['price'] * 0.7, 2),
                    'has_variants' => true,
                    'is_active' => true,
                    'is_featured' => (bool) random_int(0, 1),
                ]
            );

            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'is_primary' => true],
                ['path' => $data['image'], 'type' => 'image', 'sort_order' => 0]
            );

            $this->seedExtraGalleryMedia($product);

            foreach ($data['variants'] as $i => $variantData) {
                $qty = $variantData['qty'];
                unset($variantData['qty']);

                $skuParts = array_map('strtoupper', $variantData);
                $variantSku = $data['sku'].'-'.implode('-', $skuParts);

                $extraPrice = 0;
                foreach ($variantData as $attrKey => $valueKey) {
                    $attributeValue = $this->attributeValues["{$attrKey}.{$valueKey}"];
                    $extraPrice += (float) $attributeValue->extra_price;
                }

                $variant = ProductVariant::firstOrCreate(
                    ['sku' => $variantSku],
                    [
                        'product_id' => $product->id,
                        'price' => $data['price'] + $extraPrice,
                        'is_active' => true,
                        'sort_order' => $i,
                    ]
                );

                foreach ($variantData as $attrKey => $valueKey) {
                    $attribute = $this->attributeValues["attribute.{$attrKey}"];
                    $attributeValue = $this->attributeValues["{$attrKey}.{$valueKey}"];

                    ProductVariantAttribute::firstOrCreate([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $attribute->id,
                        'product_attribute_value_id' => $attributeValue->id,
                    ]);
                }

                if ($warehouse) {
                    Stock::firstOrCreate(
                        ['product_id' => $product->id, 'variant_id' => $variant->id, 'warehouse_id' => $warehouse->id],
                        ['qty' => $qty, 'min_qty_alert' => 3]
                    );
                }
            }
        }
    }

    /**
     * Seed a couple of extra gallery images plus a sample video, so the
     * product page slider has more than one slide to show.
     */
    private function seedExtraGalleryMedia(Product $product): void
    {
        $extraImages = [
            'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&q=80',
            'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80',
        ];

        foreach ($extraImages as $index => $url) {
            ProductImage::firstOrCreate(
                ['product_id' => $product->id, 'path' => $url],
                ['type' => 'image', 'is_primary' => false, 'sort_order' => $index + 1]
            );
        }

        ProductImage::firstOrCreate(
            ['product_id' => $product->id, 'type' => 'video'],
            [
                'path' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'is_primary' => false,
                'sort_order' => count($extraImages) + 1,
            ]
        );
    }
}
