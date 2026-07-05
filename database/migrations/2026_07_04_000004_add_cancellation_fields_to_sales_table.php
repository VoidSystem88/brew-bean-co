<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
            if (!Schema::hasColumn('sales', 'cancelled_by')) {
                $table->string('cancelled_by')->nullable();
            }
            if (!Schema::hasColumn('sales', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable();
            }
            if (!Schema::hasColumn('sales', 'refund_status')) {
                $table->string('refund_status')->default('none');
            }
            if (!Schema::hasColumn('sales', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('sales', 'refund_date')) {
                $table->timestamp('refund_date')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $columns = ['cancelled_at', 'cancelled_by', 'cancellation_reason', 'refund_status', 'refund_amount', 'refund_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
