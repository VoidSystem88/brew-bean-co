<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RefundController extends Controller
{
    public function index()
    {
        $refunds = Sale::with(['customer', 'branch'])
            ->where('refund_requested', true)
            ->orderBy('refund_requested_at', 'desc')
            ->paginate(20);

        $pendingCount = Sale::where('refund_requested', true)
            ->where('refund_status', 'pending')
            ->count();

        $approvedCount = Sale::where('refund_requested', true)
            ->where('refund_status', 'approved')
            ->count();

        $deniedCount = Sale::where('refund_requested', true)
            ->where('refund_status', 'denied')
            ->count();

        $completedCount = Sale::where('refund_requested', true)
            ->where('refund_status', 'completed')
            ->count();

        return view('admin.refunds.index', compact(
            'refunds',
            'pendingCount',
            'approvedCount',
            'deniedCount',
            'completedCount'
        ));
    }

    // ===== ITO ANG GINAGAMIT NG MODAL =====
    // Returns HTML for the modal content (no layout)
    public function show($id)
    {
        $refund = Sale::with(['customer', 'branch', 'orders.product'])
            ->where('refund_requested', true)
            ->findOrFail($id);

        // Return raw HTML without layout
        return view('admin.refunds.modal-content', compact('refund'))->render();
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $refund = Sale::where('refund_requested', true)
                ->where('refund_status', 'pending')
                ->findOrFail($id);

            $refund->refund_status = 'approved';
            $refund->refund_processed_at = now();
            $refund->refund_notes = $request->notes ?? 'Approved by admin';
            $refund->save();

            DB::commit();

            Log::info('Refund approved', [
                'order_id' => $refund->id,
                'admin_id' => auth()->id(),
                'amount' => $refund->refund_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund approved successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund approval error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function deny(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $refund = Sale::where('refund_requested', true)
                ->whereIn('refund_status', ['pending', 'approved'])
                ->findOrFail($id);

            $refund->refund_status = 'denied';
            $refund->refund_processed_at = now();
            $refund->refund_notes = $request->notes ?? 'Denied by admin';
            $refund->save();

            DB::commit();

            Log::info('Refund denied', [
                'order_id' => $refund->id,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund denied successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund denial error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function complete($id)
    {
        try {
            DB::beginTransaction();

            $refund = Sale::where('refund_requested', true)
                ->where('refund_status', 'approved')
                ->findOrFail($id);

            $refund->refund_status = 'completed';
            $refund->refund_processed_at = now();
            $refund->refund_notes = 'Refund processed successfully';
            $refund->save();

            DB::commit();

            Log::info('Refund completed', [
                'order_id' => $refund->id,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refund completed successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund completion error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 400);
        }
    }
}