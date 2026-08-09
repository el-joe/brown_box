<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->boolean('is_active')->default(false);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        foreach (['paymob', 'bank_transfer', 'vodafone_cash', 'instapay'] as $code) {
            \App\Models\Gateway::query()->create(['code' => $code]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('gateways');
    }
};
