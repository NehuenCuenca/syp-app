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
                  ->constrained()
                  ->onDelete('restrict');
            $table->foreignId('order_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('restrict');
            $table->foreignId('order_detail_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('restrict');
            $table->foreignId('movement_type_id')
                  ->constrained()
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