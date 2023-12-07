<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayoutOrderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public Order $order
    ) {}

    /**
     * Use the API service to send a payout of the correct amount.
     * Note: The order status must be paid if the payout is successful, or remain unpaid in the event of an exception.
     *
     * @return void
     */
    public function handle(ApiService $apiService)
    {
        // TODO: Complete this method

        try {
            // Calculate the payout amount based on your business logic
            $payoutAmount = $this->calculatePayoutAmount($this->order);

            // Use the ApiService to send a payout
            $apiService->sendPayout($this->order,
                $payoutAmount
            );

            // If the payout is successful, update the order status to paid
            $this->order->update(['status' => 'paid']);
        } catch (\Exception $e) {

            // Log the exception
            Log::error('Error processing payout for order ' . $this->order->id . ': ' . $e->getMessage());

            // If an exception occurs, the order status remains unpaid
        }
    }

    private function calculatePayoutAmount(Order $order): float
    {
        
        // This is a placeholder, replace it with your actual payout calculation
        return $order->subtotal_price * 0.9; // 90% of the subtotal as an example
    }
}
