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
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->date('estimated_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->enum('order_type', ['Compra_Entrante', 'Venta_Saliente']);
            $table->enum('order_status', ['Pendiente', 'Completado', 'Cancelado', 'Devuelto'])->default('Pendiente');
            $table->decimal('total_gross', 10, 2)->nullable();
            $table->decimal('total_taxes', 10, 2)->nullable();
            $table->decimal('total_net', 10, 2)->nullable();
            $table->text('notes')->nullable();
            
            // Índices
            $table->index(['order_type', 'order_status']);
            $table->index('created_at');
            $table->index('estimated_delivery_date');
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