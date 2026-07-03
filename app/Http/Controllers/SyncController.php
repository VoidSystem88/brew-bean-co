<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncController extends Controller
{
    public function index()
    {
        $offlineMode = session('offline_mode', false);
        $pendingSync = Sale::where('sync_status', 'pending')->count();
        
        return view('sync.index', compact('offlineMode', 'pendingSync'));
    }

    public function syncNow()
    {
        try {
            $user = Auth::user();
            $branchId = $user->branch_id;
            
            // Get pending sales for this branch
            $pendingSales = Sale::where('branch_id', $branchId)
                ->where('sync_status', 'pending')
                ->get();
            
            if ($pendingSales->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending orders to sync.'
                ]);
            }

            DB::beginTransaction();

            $syncedCount = 0;
            $errors = [];

            foreach ($pendingSales as $sale) {
                try {
                    // Check if sale already exists in online database
                    $exists = Sale::where('id', $sale->id)
                        ->where('sync_status', 'synced')
                        ->exists();
                    
                    if ($exists) {
                        $sale->sync_status = 'synced';
                        $sale->save();
                        $syncedCount++;
                        continue;
                    }

                    // Update sync status
                    $sale->sync_status = 'synced';
                    $sale->save();
                    $syncedCount++;

                } catch (\Exception $e) {
                    $errors[] = 'Order #' . $sale->id . ': ' . $e->getMessage();
                }
            }

            DB::commit();

            $message = '✅ ' . $syncedCount . ' offline order(s) synced successfully!';
            if (!empty($errors)) {
                $message .= ' Errors: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'synced_count' => $syncedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error syncing orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function simulateOffline()
    {
        session(['offline_mode' => true]);
        return redirect()->back()->with('success', '📡 Offline mode activated!');
    }

    public function simulateOnline()
    {
        session(['offline_mode' => false]);
        return redirect()->back()->with('success', '🌐 Online mode restored!');
    }

    public function checkStatus()
    {
        $offlineMode = session('offline_mode', false);
        $pendingSync = Sale::where('sync_status', 'pending')->count();
        
        return response()->json([
            'offline_mode' => $offlineMode,
            'pending_sync' => $pendingSync
        ]);
    }
}