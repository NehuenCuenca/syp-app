<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }

    public function creating(Order $order)
    {
        $order->code = $this->generateCode($order);
    }

    public function updating(Order $order)
    {
        //
    }

    private function generateCode(Order $order)
    {
        $todayTimestamp = now()->timestamp;
        return strtoupper(substr($order->order_type, 0, 3)) . '-' . $todayTimestamp;
    }
}

