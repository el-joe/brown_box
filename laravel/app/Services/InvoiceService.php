<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Render the order invoice to PDF, store it, and persist the path on
     * the order. Returns the storage path of the generated file.
     */
    public function generate(Order $order): string
    {
        $order->loadMissing(['items', 'customer', 'shippingCompany']);

        $pdf = Pdf::loadView('invoices.order', ['order' => $order]);

        $path = "invoices/{$order->order_number}.pdf";

        Storage::disk('public')->put($path, $pdf->output());

        $order->update(['invoice_path' => $path]);

        return $path;
    }

    public function download(Order $order)
    {
        $order->loadMissing(['items', 'customer', 'shippingCompany']);

        $pdf = Pdf::loadView('invoices.order', ['order' => $order]);

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
