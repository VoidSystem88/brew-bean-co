<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'discount_type')) {
                $table->string('discount_type')->nullable()->after('discount_rate');
            }
            if (!Schema::hasColumn('sales', 'points_used')) {
                $table->integer('points_used')->default(0)->after('discount_type');
            }
            if (!Schema::hasColumn('sales', 'delivery_fee')) {
                $table->decimal('delivery_fee', 10, 2)->default(0)->after('points_used');
            }
            if (!Schema::hasColumn('sales', 'delivery_distance_km')) {
                $table->decimal('delivery_distance_km', 10, 2)->nullable()->after('delivery_fee');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'points_used', 'delivery_fee', 'delivery_distance_km']);
        });
    }
};
