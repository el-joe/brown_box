<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('accounting_entries')
            ->where('type', 'income')
            ->update(['type' => 'credit']);

        DB::table('accounting_entries')
            ->where('type', 'expense')
            ->update(['type' => 'debit']);
    }

    public function down(): void
    {
        // Not safely reversible without tracking which rows were changed —
        // leave as-is on rollback.
    }
};
