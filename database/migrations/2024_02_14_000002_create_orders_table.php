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
            $table->id('id_order');
            $table->foreignId('id_contact')->constrained('contacts');
            $table->foreignId('id_user_creator')->constrained('users');
            $table->date('estimated_delivery_date');
            $table->date('actual_delivery_date')->nullable();
            $table->enum('order_type', ['Purchase_In', 'Sale_Out']);
            $table->enum('order_status', ['Pending', 'Processing', 'Completed', 'Cancelled', 'Returned'])->default('Pending');
            $table->decimal('total_gross', 10, 2);
            $table->decimal('total_taxes', 10, 2);
            $table->decimal('total_net', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
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