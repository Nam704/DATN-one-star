<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\OrderServiceManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AutoProcessCancelRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $orders = Order::whereHas('orderStatus', function ($query) {
            $query->where('name', 'Cancel Requested');
        })->where('updated_at', '<', now()->subHours(48))->get();
        Log::info('AutoProcessCancelRequest running', ['orders_count' => $orders->count()]);
        foreach ($orders as $order) {
            app(OrderServiceManager::class)->processCancelRequest($order->id, 'approve', 'Tự động phê duyệt do quá thời gian xử lý');
        }
    }
}
