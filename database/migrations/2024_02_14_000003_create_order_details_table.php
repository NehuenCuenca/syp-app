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
            $table->foreignId('order_id')
                  ->constrained('orders', 'id')
                  ->onDelete('cascade');
            $table->foreignId('product_id')
                  ->constrained('products', 'id')
                  ->onDelete('restrict');
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->integer('percentage_applied');
            $table->integer('line_subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};