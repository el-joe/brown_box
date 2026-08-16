<?php

namespace Database\Seeders;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class FlashSaleSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedActiveSale();
        $this->seedUpcomingSale();
        $this->seedExpiredSale();
    }

    private function seedActiveSale(): void
    {
        $sale = FlashSale::firstOrCreate(
            ['name->en' => 'Weekend Flash Sale'],
            [
                'name' => ['en' => 'Weekend Flash Sale', 'ar' => 'تخفيضات نهاية الأسبوع'],
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addDays(2),
                'is_active' => true,
            ]
        );

        $items = [
            ['sku' => 'MBP14-M3-512', 'discount_type' => 'percentage', 'discount_value' => 10, 'max_qty' => 5],
            ['sku' => 'SONY-WH1000XM5', 'discount_type' => 'percentage', 'discount_value' => 15, 'max_qty' => 10],
            ['sku' => 'PHILIPS-AF-XXL', 'discount_type' => 'fixed', 'discount_value' => 500, 'max_qty' => null],
            ['sku' => 'IPH15PRO-BLACK-128GB', 'discount_type' => 'percentage', 'discount_value' => 8, 'max_qty' => 3, 'variant' => true],
        ];

        $this->seedItems($sale, $items);
    }

    private function seedUpcomingSale(): void
    {
        $sale = FlashSale::firstOrCreate(
            ['name->en' => 'Back to School Sale'],
            [
                'name' => ['en' => 'Back to School Sale', 'ar' => 'تخفيضات العودة للمدارس'],
                'starts_at' => now()->addWeek(),
                'ends_at' => now()->addWeek()->addDays(3),
                'is_active' => true,
            ]
        );

        $items = [
            ['sku' => 'DELL-XPS13-I7', 'discount_type' => 'percentage', 'discount_value' => 12, 'max_qty' => 8],
            ['sku' => 'XIAOMI-RB4', 'discount_type' => 'fixed', 'discount_value' => 200, 'max_qty' => null],
        ];

        $this->seedItems($sale, $items);
    }

    private function seedExpiredSale(): void
    {
        $sale = FlashSale::firstOrCreate(
            ['name->en' => 'Summer Clearance'],
            [
                'name' => ['en' => 'Summer Clearance', 'ar' => 'تخفيضات نهاية الصيف'],
                'starts_at' => now()->subWeeks(2),
                'ends_at' => now()->subWeek(),
                'is_active' => false,
            ]
        );

        $items = [
            ['sku' => 'NIKE-AM90', 'discount_type' => 'percentage', 'discount_value' => 20, 'max_qty' => null],
            ['sku' => 'ADIDAS-TEE01', 'discount_type' => 'fixed', 'discount_value' => 150, 'max_qty' => null],
        ];

        $this->seedItems($sale, $items);
    }

    private function seedItems(FlashSale $sale, array $items): void
    {
        foreach ($items as $data) {
            if ($data['variant'] ?? false) {
                $variant = ProductVariant::where('sku', $data['sku'])->first();

                if (! $variant) {
                    continue;
                }

                FlashSaleItem::firstOrCreate(
                    ['flash_sale_id' => $sale->id, 'variant_id' => $variant->id],
                    [
                        'product_id' => $variant->product_id,
                        'discount_type' => $data['discount_type'],
                        'discount_value' => $data['discount_value'],
                        'max_qty' => $data['max_qty'],
                    ]
                );

                continue;
            }

            $product = Product::where('sku', $data['sku'])->first();

            if (! $product) {
                continue;
            }

            FlashSaleItem::firstOrCreate(
                ['flash_sale_id' => $sale->id, 'product_id' => $product->id, 'variant_id' => null],
                [
                    'discount_type' => $data['discount_type'],
                    'discount_value' => $data['discount_value'],
                    'max_qty' => $data['max_qty'],
                ]
            );
        }
    }
}
