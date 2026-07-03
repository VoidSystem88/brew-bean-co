<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            if (!Schema::hasColumn('transfers', 'status')) {
                $table->string('status')->default('pending')->after('quantity');
            }
            if (!Schema::hasColumn('transfers', 'received_at')) {
                $table->timestamp('received_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('transfers', 'received_by')) {
                $table->foreignId('received_by')->nullable()->after('received_at')->constrained('users')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transfers', function (Blueprint $table) {
            $table->dropColumn(['status', 'received_at', 'received_by']);
        });
    }
};