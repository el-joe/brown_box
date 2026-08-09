<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Order $order)
    {
    }

    public function build(): self
    {
        return $this->subject(__('Invoice for Order :number', ['number' => $this->order->order_number]))
            ->view('emails.invoice')
            ->when($this->order->invoice_path, fn ($mail) => $mail->attach(
                Storage::disk('public')->path($this->order->invoice_path),
                ['as' => "invoice-{$this->order->order_number}.pdf", 'mime' => 'application/pdf'],
            ));
    }
}
