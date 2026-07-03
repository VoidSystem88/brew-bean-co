<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add category_id column
            if (!Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null')->after('id');
            }
            
            // Add cost column if not exists
            if (!Schema::hasColumn('products', 'cost')) {
                $table->decimal('cost', 10, 2)->nullable()->after('price');
            }
            
            // Add sku column if not exists
            if (!Schema::hasColumn('products', 'sku')) {
                $table->string('sku')->nullable()->unique()->after('category_id');
            }
            
            // Add description column if not exists
            if (!Schema::hasColumn('products', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = ['category_id', 'cost', 'sku', 'description'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};