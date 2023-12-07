<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method
        try {
            
            if (Order::where('id', $data['order_id'])->exists()) {
                'Duplicate order detected. Order ID: ' . $data['order_id'];
                return;
            }
          
            // Create or find the customer's affiliate based on the customer_email
            $affiliate = $this->affiliateService->findOrCreateAffiliateByEmail($data['customer_email']);
            
        
            $commission = $this->calculateCommission($data['subtotal_price'], $data['discount_code']);
            dd($commission);
            // Log the commission or perform other actions
            Log::info('Commission logged for order ' . $data['order_id'] . ': $' . $commission);

            // Save the order details to the database
            Order::create([
                'id' => $data['order_id'],
                'subtotal_price' => $data['subtotal_price'],
                'merchant_domain' => $data['merchant_domain'],
                'discount_code' => $data['discount_code'],
                'customer_email' => $data['customer_email'],
                'customer_name' => $data['customer_name'],
                'commission' => $commission,
            ]);
        } catch (\Exception $e) {
            
            'Error processing order: ' . $e->getMessage();
        }
    }

    /**
     * Calculate commission based on order details.
     *
     * @param  float $subtotalPrice
     * @param  string $discountCode
     * @return float
     */
    private function calculateCommission(float $subtotalPrice, string $discountCode): float
    {
        // Your commission calculation logic based on the provided parameters
        // This is a placeholder, replace it with your actual commission calculation
        return $subtotalPrice * 0.1; // 10% commission for example
    }


    
}
