<?php

namespace App\Console\Commands;

use App\Models\Branch;
use App\Models\Item;
use App\Mail\LowStockAlert;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check';
    protected $description = 'Check for low stock items and send alerts';

    public function handle()
    {
        $branches = Branch::all();
        
        foreach ($branches as $branch) {
            $lowStockItems = DB::table('branch_item')
                ->join('items', 'branch_item.item_id', '=', 'items.id')
                ->where('branch_item.branch_id', $branch->id)
                ->whereColumn('branch_item.stock_quantity', '<=', 'branch_item.low_stock_threshold')
                ->select('items.*', 'branch_item.stock_quantity as stock', 'branch_item.low_stock_threshold as threshold')
                ->get();
            
            if ($lowStockItems->count() > 0) {
                // Send email alert
                try {
                    Mail::to(env('MAIL_FROM_ADDRESS'))->send(new LowStockAlert($lowStockItems, $branch));
                    $this->info('Alert sent for ' . $branch->name . ' - ' . $lowStockItems->count() . ' items low');
                } catch (\Exception $e) {
                    $this->error('Failed to send alert: ' . $e->getMessage());
                }
            }
        }
        
        $this->info('Stock check completed!');
        return 0;
    }
}
