<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->enum('status', ['pending', 'preparing', 'ready', 'served'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['sale_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};