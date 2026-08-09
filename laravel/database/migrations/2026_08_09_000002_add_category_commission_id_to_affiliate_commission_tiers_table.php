<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('affiliate_commission_tiers', function (Blueprint $table) {
            $table->foreignId('affiliate_category_commission_id')->nullable()->after('affiliate_id')
                ->constrained('affiliate_category_commissions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('affiliate_commission_tiers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('affiliate_category_commission_id');
        });
    }
};
