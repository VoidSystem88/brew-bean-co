<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('unit');
            $table->decimal('cost_per_unit', 10, 2)->nullable();
            $table->integer('min_stock_alert')->default(5);
            $table->timestamps();
        });

        Schema::create('item_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->timestamps();
        });

        Schema::create('branch_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->integer('low_stock_threshold')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_item');
        Schema::dropIfExists('item_product');
        Schema::dropIfExists('items');
    }
};
