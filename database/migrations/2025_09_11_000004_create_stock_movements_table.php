<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products', 'id')
                  ->onDelete('restrict');
            $table->foreignId('id_order')
                  ->nullable()
                  ->constrained('orders', 'id')
                  ->onDelete('restrict');
            $table->foreignId('order_detail_id')
                  ->nullable()
                  ->constrained('order_details', 'id')
                  ->onDelete('restrict');
            $table->foreignId('movement_type_id')
                  ->constrained('movement_types', 'id')
                  ->onDelete('restrict');
            $table->integer('quantity_moved');
            $table->text('notes')
                  ->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};