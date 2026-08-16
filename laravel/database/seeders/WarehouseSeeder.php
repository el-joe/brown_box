<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::firstOrCreate(
            ['name' => 'Main Warehouse - Cairo'],
            [
                'address' => 'Industrial Zone, Nasr City, Cairo',
                'is_active' => true,
                'is_default' => true,
            ]
        );
    }
}
