<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $code = "{$product->id_category}{$product->id}";         
        $product->code = $code;
        $product->save();
    } 

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $code = "{$product->id_category}{$product->id}";         
        $product->code = $code;
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }

    public function creating(Product $product)
    {
    }

    public function updating(Product $product)
    {
        // if product buy_price or profit_percentage are updated, update sell_price
        if ($product->isDirty('buy_price') || $product->isDirty('profit_percentage')) {
            $product->sale_price = $product->calculateSellPrice();
        }

        // if order_status is 'Completedo' and order_type is 'Compra', update product buy_price
        if ($product->isDirty('id_category')) {
            $product->code = "{$product->id_category}{$product->id}";
        }
    }

}

