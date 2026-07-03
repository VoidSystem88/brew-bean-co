<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Warehouse stock (central storage)
        Schema::create('warehouse_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->integer('low_stock_threshold')->default(10);
            $table->integer('reorder_point')->default(20);
            $table->integer('reorder_quantity')->default(50);
            $table->timestamps();
        });

        // Transfer requests
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignId('to_branch_id')->constrained('branches')->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->enum('type', ['warehouse_to_branch', 'branch_to_branch']);
            $table->enum('status', ['pending', 'approved', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
        Schema::dropIfExists('warehouse_stock');
    }
};