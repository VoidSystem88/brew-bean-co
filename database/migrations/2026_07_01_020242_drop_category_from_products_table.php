<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop foreign key constraint first
            if (Schema::hasColumn('products', 'category_id')) {
                try {
                    $table->dropForeign(['category_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                $table->dropColumn('category_id');
            }
            
            // Drop sku index first
            if (Schema::hasColumn('products', 'sku')) {
                try {
                    $table->dropUnique(['sku']);
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
                $table->dropColumn('sku');
            }
            
            // Drop other columns
            if (Schema::hasColumn('products', 'category')) {
                $table->dropColumn('category');
            }
            
            if (Schema::hasColumn('products', 'cost')) {
                $table->dropColumn('cost');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('sku')->nullable()->unique();
            $table->foreignId('category_id')->nullable();
        });
    }
};