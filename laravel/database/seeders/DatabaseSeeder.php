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
            ['ar' => 'القاهرة',         'en' => 'Cairo',           'cities' => [['ar'=>'مدينة نصر','en'=>'Nasr City'],['ar'=>'المعادي','en'=>'Maadi'],['ar'=>'مصر الجديدة','en'=>'Heliopolis'],['ar'=>'الزيتون','en'=>'Zeitoun'],['ar'=>'شبرا','en'=>'Shubra']]],
            ['ar' => 'الجيزة',          'en' => 'Giza',            'cities' => [['ar'=>'6 أكتوبر','en'=>'6th of October'],['ar'=>'الشيخ زايد','en'=>'Sheikh Zayed'],['ar'=>'الدقي','en'=>'Dokki'],['ar'=>'العجوزة','en'=>'Agouza'],['ar'=>'إمبابة','en'=>'Imbaba']]],
            ['ar' => 'الإسكندرية',      'en' => 'Alexandria',      'cities' => [['ar'=>'سموحة','en'=>'Smouha'],['ar'=>'المنتزه','en'=>'Montaza'],['ar'=>'العجمي','en'=>'Agami'],['ar'=>'الرمل','en'=>'Raml'],['ar'=>'الأنفوشي','en'=>'Anfushi']]],
            ['ar' => 'الدقهلية',        'en' => 'Dakahlia',        'cities' => [['ar'=>'المنصورة','en'=>'Mansoura'],['ar'=>'طلخا','en'=>'Talkha'],['ar'=>'ميت غمر','en'=>'Mit Ghamr'],['ar'=>'الزقازيق','en'=>'Zagazig']]],
            ['ar' => 'الشرقية',         'en' => 'Sharqia',         'cities' => [['ar'=>'الزقازيق','en'=>'Zagazig'],['ar'=>'بلبيس','en'=>'Bilbeis'],['ar'=>'أبو حماد','en'=>'Abu Hammad'],['ar'=>'العاشر من رمضان','en'=>'10th of Ramadan']]],
            ['ar' => 'القليوبية',       'en' => 'Qalyubia',        'cities' => [['ar'=>'بنها','en'=>'Banha'],['ar'=>'قليوب','en'=>'Qalyub'],['ar'=>'شبرا الخيمة','en'=>'Shubra El Kheima'],['ar'=>'الخانكة','en'=>'Khanka']]],
            ['ar' => 'الغربية',         'en' => 'Gharbia',         'cities' => [['ar'=>'طنطا','en'=>'Tanta'],['ar'=>'المحلة الكبرى','en'=>'Mahalla El Kubra'],['ar'=>'زفتى','en'=>'Zifta'],['ar'=>'سمنود','en'=>'Samannud']]],
            ['ar' => 'المنوفية',        'en' => 'Monufia',         'cities' => [['ar'=>'شبين الكوم','en'=>'Shebin El Kom'],['ar'=>'منوف','en'=>'Menouf'],['ar'=>'أشمون','en'=>'Ashmoun'],['ar'=>'قويسنا','en'=>'Quesna']]],
            ['ar' => 'البحيرة',         'en' => 'Beheira',         'cities' => [['ar'=>'دمنهور','en'=>'Damanhur'],['ar'=>'كفر الدوار','en'=>'Kafr El Dawar'],['ar'=>'الدلنجات','en'=>'Delengat'],['ar'=>'ايتاي البارود','en'=>'Itay El Baroud']]],
            ['ar' => 'الإسماعيلية',     'en' => 'Ismailia',        'cities' => [['ar'=>'الإسماعيلية','en'=>'Ismailia City'],['ar'=>'القنطرة','en'=>'Qantara'],['ar'=>'أبو صوير','en'=>'Abu Suweir']]],
            ['ar' => 'بورسعيد',         'en' => 'Port Said',       'cities' => [['ar'=>'بورسعيد','en'=>'Port Said City'],['ar'=>'العرب','en'=>'Arab'],['ar'=>'الجنوب','en'=>'South']]],
            ['ar' => 'السويس',          'en' => 'Suez',            'cities' => [['ar'=>'السويس','en'=>'Suez City'],['ar'=>'عتاقة','en'=>'Ataka'],['ar'=>'فيصل','en'=>'Faisal']]],
            ['ar' => 'دمياط',           'en' => 'Damietta',        'cities' => [['ar'=>'دمياط','en'=>'Damietta City'],['ar'=>'فارسكور','en'=>'Faraskour'],['ar'=>'الزرقا','en'=>'Zarqa']]],
            ['ar' => 'كفر الشيخ',       'en' => 'Kafr El Sheikh',  'cities' => [['ar'=>'كفر الشيخ','en'=>'Kafr El Sheikh City'],['ar'=>'دسوق','en'=>'Desouk'],['ar'=>'بيلا','en'=>'Bila']]],
            ['ar' => 'أسوان',           'en' => 'Aswan',           'cities' => [['ar'=>'أسوان','en'=>'Aswan City'],['ar'=>'كوم أمبو','en'=>'Kom Ombo'],['ar'=>'إدفو','en'=>'Edfu']]],
            ['ar' => 'أسيوط',           'en' => 'Asyut',           'cities' => [['ar'=>'أسيوط','en'=>'Asyut City'],['ar'=>'ديروط','en'=>'Dayrut'],['ar'=>'منفلوط','en'=>'Manfalut']]],
            ['ar' => 'الأقصر',          'en' => 'Luxor',           'cities' => [['ar'=>'الأقصر','en'=>'Luxor City'],['ar'=>'الأرمنت','en'=>'Armant'],['ar'=>'إسنا','en'=>'Esna']]],
            ['ar' => 'سوهاج',           'en' => 'Sohag',           'cities' => [['ar'=>'سوهاج','en'=>'Sohag City'],['ar'=>'طهطا','en'=>'Tahta'],['ar'=>'جرجا','en'=>'Girga']]],
            ['ar' => 'قنا',             'en' => 'Qena',            'cities' => [['ar'=>'قنا','en'=>'Qena City'],['ar'=>'نجع حمادي','en'=>'Nag Hammadi'],['ar'=>'دشنا','en'=>'Dishna']]],
            ['ar' => 'المنيا',          'en' => 'Minya',           'cities' => [['ar'=>'المنيا','en'=>'Minya City'],['ar'=>'مغاغة','en'=>'Maghagha'],['ar'=>'ملوي','en'=>'Mallawi']]],
            ['ar' => 'بني سويف',        'en' => 'Beni Suef',       'cities' => [['ar'=>'بني سويف','en'=>'Beni Suef City'],['ar'=>'الواسطي','en'=>'Wasta'],['ar'=>'ناصر','en'=>'Nasser']]],
            ['ar' => 'الفيوم',          'en' => 'Fayoum',          'cities' => [['ar'=>'الفيوم','en'=>'Fayoum City'],['ar'=>'سنورس','en'=>'Sinnuris'],['ar'=>'يوسف الصديق','en'=>'Yusuf al-Siddiq']]],
            ['ar' => 'شمال سيناء',      'en' => 'North Sinai',     'cities' => [['ar'=>'العريش','en'=>'Arish'],['ar'=>'الشيخ زويد','en'=>'Sheikh Zuweid'],['ar'=>'بئر العبد','en'=>'Bir al-Abd']]],
            ['ar' => 'جنوب سيناء',      'en' => 'South Sinai',     'cities' => [['ar'=>'شرم الشيخ','en'=>'Sharm El Sheikh'],['ar'=>'طابا','en'=>'Taba'],['ar'=>'دهب','en'=>'Dahab']]],
            ['ar' => 'البحر الأحمر',    'en' => 'Red Sea',         'cities' => [['ar'=>'الغردقة','en'=>'Hurghada'],['ar'=>'سفاجا','en'=>'Safaga'],['ar'=>'القصير','en'=>'Quseer']]],
            ['ar' => 'الوادي الجديد',   'en' => 'New Valley',      'cities' => [['ar'=>'الخارجة','en'=>'Kharga'],['ar'=>'الداخلة','en'=>'Dakhla'],['ar'=>'الفرافرة','en'=>'Farafra']]],
            ['ar' => 'مطروح',           'en' => 'Matrouh',         'cities' => [['ar'=>'مرسى مطروح','en'=>'Mersa Matruh'],['ar'=>'سيوة','en'=>'Siwa'],['ar'=>'الحمام','en'=>'El Hammam']]],
        ];

        foreach ($governorates as $gov) {
            $govId = DB::table('governorates')->insertGetId([
                'country_id' => $countryId,
                'name_ar' => $gov['ar'],
                'name_en' => $gov['en'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($gov['cities'] as $city) {
                DB::table('cities')->insert([
                    'governorate_id' => $govId,
                    'name_ar' => $city['ar'],
                    'name_en' => $city['en'],
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
