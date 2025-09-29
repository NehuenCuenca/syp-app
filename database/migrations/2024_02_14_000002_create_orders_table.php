<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contact');
            $table->unsignedBigInteger('id_user_creator');
            $table->text('code');
            $table->date('actual_delivery_date')->nullable();
            $table->enum('order_type', ['Compra', 'Venta']);
            $table->enum('order_status', ['Pendiente', 'Completado', 'Cancelado'])->default('Pendiente');
            $table->decimal('total_net', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Índices
            $table->index(['order_type', 'order_status']);
            $table->index('created_at');
            $table->index('actual_delivery_date');
            
            // Claves foráneas
            $table->foreign('id_contact')->references('id')->on('contacts')->onDelete('restrict');
            $table->foreign('id_user_creator')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};