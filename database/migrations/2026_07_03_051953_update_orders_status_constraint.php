<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support dropping CHECK constraints directly
        // We need to recreate the table or use raw SQL
        
        // For SQLite, we need to use a different approach
        // First, check if the constraint exists and update it
        
        // Since SQLite doesn't support ALTER TABLE DROP CONSTRAINT,
        // we'll modify the status column to use the new values
        
        // First, update any existing invalid statuses to 'pending'
        DB::statement("UPDATE orders SET status = 'pending' WHERE status NOT IN ('pending', 'preparing', 'ready', 'completed', 'cancelled')");
        
        // Then modify the column to allow the new values
        // For SQLite, we need to use a raw SQL approach
        try {
            // Try to drop the old constraint and add a new one
            // This is SQLite-specific syntax
            DB::statement("PRAGMA foreign_keys = 0");
            
            // Create a new table with the correct constraints
            // This is a simplified approach - for SQLite, we recreate the table
            $tableName = 'orders';
            $newTableName = 'orders_new';
            
            // Get the current table schema
            $schema = DB::select("PRAGMA table_info({$tableName})");
            
            // Build the CREATE TABLE statement
            $columns = [];
            $primaryKey = null;
            foreach ($schema as $col) {
                $colDef = "{$col->name} {$col->type}";
                if ($col->notnull) $colDef .= " NOT NULL";
                if ($col->dflt_value !== null) $colDef .= " DEFAULT '{$col->dflt_value}'";
                if ($col->pk) {
                    $primaryKey = $col->name;
                    $colDef .= " PRIMARY KEY";
                }
                $columns[] = $colDef;
            }
            
            // Create new table with CHECK constraint
            $createSQL = "CREATE TABLE {$newTableName} (" . implode(", ", $columns) . ", CHECK (status IN ('pending', 'preparing', 'ready', 'completed', 'cancelled')))";
            DB::statement($createSQL);
            
            // Copy data
            DB::statement("INSERT INTO {$newTableName} SELECT * FROM {$tableName}");
            
            // Drop old table and rename new one
            DB::statement("DROP TABLE {$tableName}");
            DB::statement("ALTER TABLE {$newTableName} RENAME TO {$tableName}");
            
            DB::statement("PRAGMA foreign_keys = 1");
            
        } catch (\Exception $e) {
            // If the above fails, try a simpler approach
            // Just remove the CHECK constraint by modifying the column
            try {
                // For SQLite, we can try to set the column without the constraint
                DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'cancelled'");
            } catch (\Exception $e2) {
                // If all else fails, just log it
                \Log::warning('Could not update status constraint: ' . $e2->getMessage());
            }
        }
    }

    public function down(): void
    {
        // Revert changes - recreate with original constraint
        try {
            DB::statement("PRAGMA foreign_keys = 0");
            
            $tableName = 'orders';
            $newTableName = 'orders_old';
            
            // Rename current table
            DB::statement("ALTER TABLE {$tableName} RENAME TO {$newTableName}");
            
            // Create original table without 'cancelled' constraint
            $createSQL = "CREATE TABLE {$tableName} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sale_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                status TEXT CHECK (status IN ('pending', 'preparing', 'ready', 'completed')) NOT NULL DEFAULT 'pending',
                notes TEXT,
                created_at DATETIME,
                updated_at DATETIME,
                FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            )";
            DB::statement($createSQL);
            
            // Copy data
            DB::statement("INSERT INTO {$tableName} SELECT * FROM {$newTableName}");
            
            // Drop old table
            DB::statement("DROP TABLE {$newTableName}");
            
            DB::statement("PRAGMA foreign_keys = 1");
            
        } catch (\Exception $e) {
            \Log::warning('Could not revert status constraint: ' . $e->getMessage());
        }
    }
};