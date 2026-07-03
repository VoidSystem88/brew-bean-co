<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('branches', 'address')) {
                $table->text('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('branches', 'phone')) {
                $table->string('phone', 20)->nullable()->after('address');
            }
            if (!Schema::hasColumn('branches', 'email')) {
                $table->string('email', 255)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['address', 'phone', 'email']);
        });
    }
};