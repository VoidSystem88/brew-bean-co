<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // For SQLite, we need to recreate the table
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Create new table with updated ENUM
            Schema::create('stock_movements_new', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->constrained()->onDelete('cascade');
                $table->integer('quantity_change');
                $table->enum('type', ['sale', 'restock', 'transfer', 'adjustment'])->default('restock');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();
            });

            // Copy data from old table
            DB::statement('INSERT INTO stock_movements_new (id, product_id, branch_id, quantity_change, type, reference_id, created_at, updated_at) SELECT id, product_id, branch_id, quantity_change, type, reference_id, created_at, updated_at FROM stock_movements');
            
            // Drop old table
            Schema::drop('stock_movements');
            
            // Rename new table
            Schema::rename('stock_movements_new', 'stock_movements');
        } else {
            // For MySQL/PostgreSQL
            DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('sale', 'restock', 'transfer', 'adjustment')");
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            Schema::create('stock_movements_old', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('branch_id')->constrained()->onDelete('cascade');
                $table->integer('quantity_change');
                $table->enum('type', ['sale', 'restock', 'transfer'])->default('restock');
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();
            });

            DB::statement('INSERT INTO stock_movements_old SELECT * FROM stock_movements');
            Schema::drop('stock_movements');
            Schema::rename('stock_movements_old', 'stock_movements');
        } else {
            DB::statement("ALTER TABLE stock_movements MODIFY COLUMN type ENUM('sale', 'restock', 'transfer')");
        }
    }
};