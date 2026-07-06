<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'refund_requested')) {
                $table->boolean('refund_requested')->default(false);
            }
            if (!Schema::hasColumn('sales', 'refund_status')) {
                $table->string('refund_status')->nullable()->default('none');
            }
            if (!Schema::hasColumn('sales', 'refund_reason')) {
                $table->string('refund_reason')->nullable();
            }
            if (!Schema::hasColumn('sales', 'refund_description')) {
                $table->text('refund_description')->nullable();
            }
            if (!Schema::hasColumn('sales', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('sales', 'refund_requested_at')) {
                $table->timestamp('refund_requested_at')->nullable();
            }
            if (!Schema::hasColumn('sales', 'refund_processed_at')) {
                $table->timestamp('refund_processed_at')->nullable();
            }
            if (!Schema::hasColumn('sales', 'refund_notes')) {
                $table->text('refund_notes')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $columns = [
                'refund_requested', 
                'refund_status', 
                'refund_reason', 
                'refund_description', 
                'refund_amount', 
                'refund_requested_at',
                'refund_processed_at',
                'refund_notes'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};