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
            $table->foreignId('id_product')
                  ->constrained('products', 'id')
                  ->onDelete('restrict');
            $table->foreignId('id_order')
                  ->nullable()
                  ->constrained('orders', 'id')
                  ->onDelete('restrict');
            $table->foreignId('id_user_responsible')
                  ->constrained('users', 'id')
                  ->onDelete('restrict');
            $table->foreignId('id_movement_type')
                  ->constrained('movement_types', 'id')
                  ->onDelete('restrict');
            $table->integer('quantity_moved');
            $table->timestamp('movement_date')
                  ->useCurrent();
            $table->string('external_reference')
                  ->nullable();
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