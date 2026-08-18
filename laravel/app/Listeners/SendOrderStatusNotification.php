<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\SendCustomerNotification;
use App\Jobs\SendOrderStatusEmail;
use App\Mail\OrderStatusMail;
use App\Notifications\OrderConfirmed;
use App\Notifications\OrderDelivered;
use App\Notifications\OrderRejected;
use App\Notifications\OrderShipped;
use App\Notifications\OrderStatusChanged as OrderStatusChangedNotification;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusNotification
{
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order->fresh(['customer']);
        $newStatus = $event->newStatus;

        $notification = match ($newStatus) {
            'confirmed' => new OrderConfirmed($order),
            'shipped' => new OrderShipped($order),
            'delivered' => new OrderDelivered($order),
            'cancelled' => new OrderRejected($order),
            default => new OrderStatusChangedNotification($order, $newStatus),
        };

        if ($order->customer) {
            SendCustomerNotification::dispatch($order->customer, $notification);
        } else {
            SendOrderStatusEmail::dispatch(
                $order,
                __('Order Status Updated'),
                __('Your order status has changed to :status.', ['status' => $newStatus]),
            );
        }

        $notificationEmail = setting('notification_email');

        if ($notificationEmail && $newStatus === 'pending') {
            Mail::to($notificationEmail)->queue(
                new OrderStatusMail(
                    $order,
                    __('New Order: :number', ['number' => $order->order_number]),
                    __('A new order has been placed and requires attention.'),
                ),
            );
        }
    }
}
