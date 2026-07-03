<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'original_amount')) {
                $table->decimal('original_amount', 10, 2)->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('sales', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('original_amount');
            }
            if (!Schema::hasColumn('sales', 'discount_rate')) {
                $table->decimal('discount_rate', 5, 2)->default(0)->after('discount_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['original_amount', 'discount_amount', 'discount_rate']);
        });
    }
};