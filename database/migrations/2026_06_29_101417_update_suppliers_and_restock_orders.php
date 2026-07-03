<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->string('logo')->nullable();
        });

        Schema::table('restock_orders', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable();
            $table->string('approved_token')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'approved', 'delivered', 'rejected'])->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['website', 'notes', 'logo']);
        });

        Schema::table('restock_orders', function (Blueprint $table) {
            $table->dropColumn(['approved_at', 'approved_token', 'delivered_at']);
            $table->enum('status', ['pending', 'sent', 'delivered', 'cancelled'])->default('pending')->change();
        });
    }
};
