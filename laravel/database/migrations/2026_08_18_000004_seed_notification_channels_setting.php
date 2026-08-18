<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'notification_channels'],
            [
                'value' => json_encode([
                    'customer' => [
                        'order_placed'    => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'order_confirmed' => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'order_shipped'   => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'order_delivered' => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'order_cancelled' => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'order_status'    => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'refund_approved' => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'refund_rejected' => ['database' => true, 'mail' => false, 'whatsapp' => false],
                        'commission'      => ['database' => true, 'mail' => true,  'whatsapp' => false],
                        'payout'          => ['database' => true, 'mail' => true,  'whatsapp' => false],
                    ],
                    'admin' => [
                        'new_order'       => ['database' => true, 'mail' => true],
                        'new_refund'      => ['database' => true, 'mail' => true],
                        'low_stock'       => ['database' => true, 'mail' => false],
                        'payment_proof'   => ['database' => true, 'mail' => true],
                        'payout_request'  => ['database' => true, 'mail' => true],
                    ],
                ]),
                'group' => 'notifications',
                'type'  => 'json',
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'notification_channels')->delete();
    }
};
