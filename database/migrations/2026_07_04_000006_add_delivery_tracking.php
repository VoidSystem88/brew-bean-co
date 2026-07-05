<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'delivery_person_id')) {
                $table->foreignId('delivery_person_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('sales', 'delivery_assigned_at')) {
                $table->timestamp('delivery_assigned_at')->nullable();
            }
            if (!Schema::hasColumn('sales', 'delivery_picked_up_at')) {
                $table->timestamp('delivery_picked_up_at')->nullable();
            }
            if (!Schema::hasColumn('sales', 'delivery_completed_at')) {
                $table->timestamp('delivery_completed_at')->nullable();
            }
            if (!Schema::hasColumn('sales', 'delivery_notes')) {
                $table->text('delivery_notes')->nullable();
            }
        });

        // Create delivery tracking history table
        Schema::create('delivery_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status'); // assigned, picked_up, in_transit, delivered, failed
            $table->text('notes')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_tracking');
        Schema::table('sales', function (Blueprint $table) {
            $columns = ['delivery_person_id', 'delivery_assigned_at', 'delivery_picked_up_at', 'delivery_completed_at', 'delivery_notes'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('sales', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
