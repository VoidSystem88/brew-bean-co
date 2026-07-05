<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support altering column constraints directly
        // We need to recreate the table
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Get the current table structure
            $tableName = 'users';
            $newTableName = 'users_new';
            
            // Create new table with updated constraint
            Schema::create($newTableName, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
                $table->enum('role', ['admin', 'manager', 'staff', 'delivery'])->default('staff');
                $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
                $table->boolean('is_active')->default(true);
            });
            
            // Copy data from old table
            DB::statement("INSERT INTO {$newTableName} (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role, branch_id, is_active) 
                          SELECT id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role, branch_id, is_active FROM {$tableName}");
            
            // Drop old table
            Schema::drop($tableName);
            
            // Rename new table
            Schema::rename($newTableName, $tableName);
            
            // Recreate indexes
            Schema::table($tableName, function (Blueprint $table) {
                $table->index(['email']);
                $table->index(['role']);
            });
        } else {
            // For MySQL/PostgreSQL, just modify the column
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'staff', 'delivery') DEFAULT 'staff'");
        }
    }

    public function down(): void
    {
        // Revert back to original
        if (DB::connection()->getDriverName() === 'sqlite') {
            // Would need to revert, but keeping simple for now
        } else {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'manager', 'staff') DEFAULT 'staff'");
        }
    }
};
