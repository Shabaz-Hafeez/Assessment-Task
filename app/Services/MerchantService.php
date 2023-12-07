<?php

namespace App\Services;

use App\Jobs\PayoutOrderJob;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Support\Facades\DB as FacadesDB;

class MerchantService
{
    /**
     * Register a new user and associated merchant.
     * Hint: Use the password field to store the API key.
     * Hint: Be sure to set the correct user type according to the constants in the User model.
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return Merchant
     */
    public function register(array $data): Merchant
    {
        // Create a new user
        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['api_key']), // Store API key as the password
            'user_type' => 'merchant',
        ]);

        // Save the user
        $user->save();

        // Create a new merchant associated with the user
        $merchant = new Merchant([
            'domain' => $data['domain'],
        ]);

        // Save the merchant, associating it with the user
        $user->merchant()->save($merchant);

        return $merchant;
    }

    /**
     * Update the user
     *
     * @param array{domain: string, name: string, email: string, api_key: string} $data
     * @return void
     */
    public function updateMerchant(User $user, array $data)
    {
        // TODO: Complete this method

        try {
            // TODO: Implement the logic to update the user with the provided data

            // Retrieve the existing user data
            $existingData = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => bcrypt($data['api_key']),
                'user_type' => 'merchant',
            ];

            // Check if each field in $data is different from the existing data
            $updates = [];
            foreach ($data as $field => $value) {
                if ($existingData[$field] !== $value) {
                    $updates[$field] = $value;
                }
            }

            // Update the user if there are changes
            if (!empty($updates)) {
                $user->update($updates);

                // Log a success message using error_log
                error_log('User updated successfully.');

                return true; // or use another value to indicate success
            } else {

                // Log a message indicating no changes
                error_log('No changes detected for user update.');

                return false; 
            }
        } catch (\Exception $e) {
            // Log or handle the exception

            // Log an error message using error_log
            error_log('Error updating user: ' . $e->getMessage());

            return false; // or use another value to indicate failure
        }
    }
    /**
     * Find a merchant by their email.
     * Hint: You'll need to look up the user first.
     *
     * @param string $email
     * @return Merchant|null
     */
    public function findMerchantByEmail(string $email): ?Merchant
    {
        
        // Look up the user by email
        $user = User::where('email', $email)->first();

        // If the user is found, retrieve the associated merchant
        if ($user) {
            $merchant = $user->merchant;

            // Return the merchant if found, otherwise return null
            return $merchant;
        }

        // If the user is not found, return null
        return null;
    }

    

    /**
     * Pay out all of an affiliate's orders.
     * Hint: You'll need to dispatch the job for each unpaid order.
     *
     * @param Affiliate $affiliate
     * @return void
     */
    public function payout(Affiliate $affiliate)
    {
    // Get all unpaid orders associated with the affiliate
    $unpaidOrders = $affiliate->orders()->where('paid', false)->get();

    // Loop through each unpaid order and dispatch a job
    foreach ($unpaidOrders as $order) {
        // Dispatch the PayoutOrderJob for each order
        PayoutOrderJob::dispatch($order);
    }

    // Note: You might want to log or perform additional actions here
    }


    public function getOrderStatistics(string $fromDate, string $toDate): array
    {
        // Fetch total number of orders in the date range
        $orderCount = Order::whereBetween('created_at', [$fromDate, $toDate])->count();
        // dd($orderCount);
        // Fetch the sum of order subtotals in the date range
        $revenue = Order::whereBetween('created_at', [$fromDate, $toDate])->sum('subtotal');
        // dd($revenue);
        // Fetch the sum of unpaid commissions for orders with an affiliate in the date range
        // $commissionOwed = DB::table('orders')
        // ->join('affiliates', 'orders.affiliate_id', '=', 'affiliates.id')
        // ->whereBetween('orders.created_at', [$fromDate, $toDate])
        //     ->where('payout_status', 'unpaid') // Assuming 'unpaid' status indicates unpaid orders
        //     ->sum('commission_owed');
            // dd($commissionOwed);

            $commissionOwed = Order::where('payout_status' ,  'unpaid')->whereBetween('created_at', [$fromDate, $toDate])->sum('commission_owed');

        return [
            'count' => $orderCount,
            'commission_owed' => $commissionOwed,
            'revenue' => $revenue,
        ];
    }

    
}
