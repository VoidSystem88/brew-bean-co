<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Item;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Recipe;
use App\Models\Sale;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'items' => Item::count(),
            'branches' => Branch::count(),
            'customers' => Customer::count(),
            'suppliers' => Supplier::count(),
            'recipes' => Recipe::count(),
            'sales' => Sale::count(),
            'orders' => Order::count(),
        ];

        return view('admin.database', compact('stats'));
    }

    public function exportBackup()
    {
        try {
            $databasePath = database_path('database.sqlite');
            
            if (!file_exists($databasePath)) {
                return redirect()->back()->with('error', 'Database file not found.');
            }

            return response()->download($databasePath, 'backup_' . date('Y-m-d_H-i-s') . '.sqlite', [
                'Content-Type' => 'application/octet-stream',
            ]);

        } catch (\Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error exporting database: ' . $e->getMessage());
        }
    }

    public function importBackup(Request $request)
    {
        try {
            // Validate with custom rules
            $request->validate([
                'file' => 'required|file|max:102400', // 100MB max
            ]);

            $file = $request->file('file');
            
            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'Invalid file uploaded.');
            }

            // Check file extension
            $extension = $file->getClientOriginalExtension();
            $allowedExtensions = ['sqlite', 'sql', 'db'];
            
            if (!in_array(strtolower($extension), $allowedExtensions)) {
                return redirect()->back()->with('error', 'Please upload a SQLite file (.sqlite, .sql, or .db)');
            }

            // Get current database path
            $currentDb = database_path('database.sqlite');
            
            // Create backup of current database
            if (file_exists($currentDb)) {
                $backupPath = database_path('backup_' . date('Y-m-d_H-i-s') . '.sqlite');
                copy($currentDb, $backupPath);
            }

            // Read the uploaded file content
            $uploadedContent = file_get_contents($file->getRealPath());
            
            if (empty($uploadedContent)) {
                return redirect()->back()->with('error', 'Uploaded file is empty.');
            }

            // Write to database file
            file_put_contents($currentDb, $uploadedContent);

            // Clear all caches
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('view:clear');
            \Artisan::call('route:clear');

            // Reconnect to database
            DB::reconnect();

            return redirect()->back()->with('success', 'Database imported successfully!');

        } catch (\Exception $e) {
            Log::error('Import error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error importing database: ' . $e->getMessage());
        }
    }

    public function downloadProductTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="product_template.csv"',
        ];

        $columns = ['name', 'description', 'price', 'category'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Sample Product', 'Description here', '99.99', 'Coffee']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function downloadItemTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="item_template.csv"',
        ];

        $columns = ['name', 'category', 'unit', 'supplier_id'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Sample Item', 'Raw Materials', 'kg', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}