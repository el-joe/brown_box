<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE accounting_entries
            MODIFY COLUMN category
            ENUM('sales','purchases','expenses','discounts','taxes','affiliate','owner','shipping')
            NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE accounting_entries
            MODIFY COLUMN category
            ENUM('sales','purchases','expenses','discounts','taxes','affiliate','owner')
            NOT NULL
        ");
    }
};
