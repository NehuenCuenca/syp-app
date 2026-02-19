<?php

namespace App\Observers;

use App\Models\MovementType;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $movementType = MovementType::find($order->movement_type_id)->name;
        $order->code = substr($movementType, 0, 1) . $order->id;
        $order->save();
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
        //
    }

    public function updating(Order $order)
    {
        //
    }
}

