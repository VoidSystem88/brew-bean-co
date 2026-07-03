<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'delivery_address')) {
                $table->text('delivery_address')->nullable()->after('total_amount');
            }
            if (!Schema::hasColumn('sales', 'delivery_status')) {
                $table->string('delivery_status')->default('pending')->after('delivery_address');
            }
            if (!Schema::hasColumn('sales', 'order_notes')) {
                $table->text('order_notes')->nullable()->after('delivery_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['delivery_address', 'delivery_status', 'order_notes']);
        });
    }
};