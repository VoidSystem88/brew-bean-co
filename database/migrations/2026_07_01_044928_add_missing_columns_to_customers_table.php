<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Add customer_code column
            if (!Schema::hasColumn('customers', 'customer_code')) {
                $table->string('customer_code')->unique()->nullable()->after('id');
            }
            
            // Add is_active column
            if (!Schema::hasColumn('customers', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('loyalty_points');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['customer_code', 'is_active']);
        });
    }
};