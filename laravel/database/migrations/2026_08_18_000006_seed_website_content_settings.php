<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            ['key' => 'announcement_text_en', 'value' => 'Buy Now Pay Later Starting at 0% APR.', 'group' => 'general'],
            ['key' => 'announcement_text_ar', 'value' => 'اشترِ الآن وادفع لاحقاً بفائدة 0%.', 'group' => 'general'],
            ['key' => 'footer_about_en', 'value' => 'Your trusted online marketplace for quality products.', 'group' => 'general'],
            ['key' => 'footer_about_ar', 'value' => 'سوقك الإلكتروني الموثوق للمنتجات عالية الجودة.', 'group' => 'general'],
            ['key' => 'contact_address_en', 'value' => '', 'group' => 'general'],
            ['key' => 'contact_address_ar', 'value' => '', 'group' => 'general'],
            ['key' => 'contact_hours_en', 'value' => "Monday – Friday: 9am – 6pm\nSaturday: 10am – 4pm", 'group' => 'general'],
            ['key' => 'contact_hours_ar', 'value' => "الاثنين – الجمعة: 9 صباحاً – 6 مساءً\nالسبت: 10 صباحاً – 4 مساءً", 'group' => 'general'],
        ];

        foreach ($defaults as $row) {
            DB::table('settings')->updateOrInsert(
                ['key' => $row['key']],
                ['value' => $row['value'], 'group' => $row['group'], 'type' => 'text']
            );
        }
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'announcement_text_en', 'announcement_text_ar',
            'footer_about_en', 'footer_about_ar',
            'contact_address_en', 'contact_address_ar',
            'contact_hours_en', 'contact_hours_ar',
        ])->delete();
    }
};
