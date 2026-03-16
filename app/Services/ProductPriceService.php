<?php

namespace App\Services;

use App\Http\Requests\UpdateProductPricesRequest;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductPriceService
{
    /**
     * Actualiza en lote el precio de compra (buy_price) de los productos.
     *
     * @param  array  $data  Datos validados desde UpdateProductPricesRequest
     * @return \Illuminate\Support\Collection  Colección de productos actualizados
     *
     * @throws \Throwable
     */
    public function bulkUpdatePurchasePrices(array $data): Collection
    {
        return DB::transaction(function () use ($data) {
            $products = Product::withTrashed()
                ->whereIn('id', $data['products_ids'])
                ->lockForUpdate()
                ->get();

            $mode      = $data['mode'];
            $direction = $data['direction'];
            $value     = (int) $data['value'];

            foreach ($products as $product) {
                $currentPrice = (int) $product->buy_price;

                // Calcular delta según modo
                if ($mode === UpdateProductPricesRequest::PERCENTAGE_MODE) {
                    // value viene de 1..500, representa %
                    $delta = (int) round($currentPrice * ($value / 100));
                } else {
                    // ABSOLUTE_PRICE_MODE -> value es el monto ARS (1..50000)
                    $delta = $value;
                }

                // Aplicar signo según dirección
                if ($direction === UpdateProductPricesRequest::UPGRADE_PRICE_DIRECTION) {
                    $newPrice = $currentPrice + $delta;
                } else {
                    // DOWNGRADE_PRICE_DIRECTION
                    // Nos aseguramos que no baje de 1
                    $newPrice = max(1, $currentPrice - $delta);
                }

                $product->update([
                    'buy_price' => $newPrice,
                ]);

                Log::info('Precio de compra actualizado', [
                    'product_id'   => $product->id,
                    'old_buy_price'=> $currentPrice,
                    'new_buy_price'=> $newPrice,
                    'mode'         => $mode,
                    'direction'    => $direction,
                    'value'        => $value,
                ]);
            }

            // Devolvemos los productos frescos (para mostrar en el frontend)
            $products->fresh();
            return $products->load('category');
        });
    }
}