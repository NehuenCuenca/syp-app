<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order')
                  ->constrained('orders', 'id')
                  ->onDelete('cascade');
            $table->foreignId('id_product')
                  ->constrained('products', 'id')
                  ->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price_at_order', 10, 2);
            $table->decimal('discount_percentage_by_unit', 10, 2);
            $table->decimal('line_subtotal', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};