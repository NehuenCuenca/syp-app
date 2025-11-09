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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable(); //$id_category + $id
            $table->string('name');
            $table->integer('buy_price');
            $table->integer('profit_percentage');
            $table->integer('sale_price')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->unsignedBigInteger('id_category');
            $table->foreign('id_category')->references('id')->on('categories')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};