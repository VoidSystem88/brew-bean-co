<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            if (!Schema::hasColumn('recipes', 'batch_size')) {
                $table->integer('batch_size')->default(1)->after('unit')->comment('Number of servings per batch');
            }
            if (!Schema::hasColumn('recipes', 'is_batch')) {
                $table->boolean('is_batch')->default(false)->after('batch_size')->comment('True if recipe is for batch production');
            }
        });
    }

    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropColumn(['batch_size', 'is_batch']);
        });
    }
};