<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('id_movement');
            $table->foreignId('id_product')
                  ->constrained('products', 'id')
                  ->onDelete('restrict');
            $table->foreignId('id_order')
                  ->nullable()
                  ->constrained('orders', 'id_order')
                  ->onDelete('restrict');
            $table->foreignId('id_user_responsible')
                  ->constrained('users', 'id')
                  ->onDelete('restrict');
            $table->enum('movement_type', [
                'Purchase_In',
                'Sale_Out',
                'Positive_Adjustment',
                'Negative_Adjustment',
                'Client_Return',
                'Supplier_Return'
            ]);
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