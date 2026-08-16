<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedLanguages();
        $this->seedCurrencies();
        $this->seedGeography();
        $this->seedSettings();
        $this->call([
            PermissionSeeder::class,
            StaticPageSeeder::class,
            WarehouseSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            ProductSeeder::class,
            FlashSaleSeeder::class,
            BannerSeeder::class,
        ]);
        $this->seedAdmin();
    }

    private function seedLanguages(): void
    {
        DB::table('languages')->insert([
            ['code' => 'ar', 'name' => 'العربية', 'direction' => 'rtl', 'is_default' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'is_default' => false, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedCurrencies(): void
    {
        DB::table('currencies')->insert([
            'code' => 'EGP',
            'symbol' => 'ج.م',
            'name' => 'Egyptian Pound',
            'rate_to_egp' => 1,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function seedGeography(): void
    {
        $countryId = DB::table('countries')->insertGetId([
            'name_ar' => 'مصر',
            'name_en' => 'Egypt',
            'code' => 'EG',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $governorates = [
            ['name_ar' => 'القاهرة', 'name_en' => 'Cairo', 'cities' => [
                ['name_ar' => 'مدينة نصر', 'name_en' => 'Nasr City'],
                ['name_ar' => 'المعادي', 'name_en' => 'Maadi'],
            ]],
            ['name_ar' => 'الجيزة', 'name_en' => 'Giza', 'cities' => [
                ['name_ar' => '6 أكتوبر', 'name_en' => '6th of October'],
                ['name_ar' => 'الشيخ زايد', 'name_en' => 'Sheikh Zayed'],
            ]],
            ['name_ar' => 'الإسكندرية', 'name_en' => 'Alexandria', 'cities' => [
                ['name_ar' => 'سموحة', 'name_en' => 'Smouha'],
                ['name_ar' => 'المنتزه', 'name_en' => 'Montaza'],
            ]],
        ];

        foreach ($governorates as $governorate) {
            $governorateId = DB::table('governorates')->insertGetId([
                'country_id' => $countryId,
                'name_ar' => $governorate['name_ar'],
                'name_en' => $governorate['name_en'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($governorate['cities'] as $city) {
                DB::table('cities')->insert([
                    'governorate_id' => $governorateId,
                    'name_ar' => $city['name_ar'],
                    'name_en' => $city['name_en'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Brown Box', 'group' => 'general', 'type' => 'text', 'is_public' => true],
            ['key' => 'site_logo', 'value' => null, 'group' => 'general', 'type' => 'file', 'is_public' => true],
            ['key' => 'default_currency', 'value' => 'EGP', 'group' => 'general', 'type' => 'text', 'is_public' => true],
            ['key' => 'default_language', 'value' => 'ar', 'group' => 'general', 'type' => 'text', 'is_public' => true],
            ['key' => 'contact_email', 'value' => 'support@brownbox.test', 'group' => 'general', 'type' => 'text', 'is_public' => true],
            ['key' => 'contact_phone', 'value' => null, 'group' => 'general', 'type' => 'text', 'is_public' => true],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'general', 'type' => 'boolean', 'is_public' => false],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    private function seedAdmin(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);

        $adminId = DB::table('admins')->insertGetId([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('model_has_roles')->insert([
            'role_id' => $role->id,
            'model_type' => 'App\\Models\\Admin',
            'model_id' => $adminId,
        ]);
    }
}
