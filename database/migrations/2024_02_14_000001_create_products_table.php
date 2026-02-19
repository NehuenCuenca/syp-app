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
            $table->string('code', 10)->unique()->nullable(); //$category_id + $id
            $table->string('name', 100);
            $table->integer('buy_price')->default(0);
            $table->integer('profit_percentage')->default(0);
            $table->integer('sale_price')->default(0);
            $table->integer('current_stock')->default(0);
            $table->integer('min_stock_alert')->default(5);
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
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