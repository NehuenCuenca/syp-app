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
            $table->unsignedBigInteger('id_movement_type');
            $table->string('code')->unique()->nullable();
            $table->integer('adjustment_amount')->nullable();
            $table->integer('subtotal')->nullable();
            $table->integer('total_net')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            
            // Claves foráneas
            $table->foreign('id_contact')->references('id')->on('contacts')->onDelete('restrict');
            $table->foreign('id_movement_type')->references('id')->on('movement_types')->onDelete('restrict');
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