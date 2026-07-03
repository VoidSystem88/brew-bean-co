<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'weight_per_unit')) {
                $table->decimal('weight_per_unit', 10, 2)->nullable()->after('unit')->comment('Weight in grams per unit');
            }
            if (!Schema::hasColumn('items', 'volume_per_unit')) {
                $table->decimal('volume_per_unit', 10, 2)->nullable()->after('weight_per_unit')->comment('Volume in ml per unit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['weight_per_unit', 'volume_per_unit']);
        });
    }
};