<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // Validate or sanitize input if needed

        // Create a new affiliate instance
        $affiliate = new Affiliate([
            'email' => $email,
            'name' => $name,
            'commission_rate' => $commissionRate,
        ]);
        dd($affiliate);
        // Associate the affiliate with the merchant
        $merchant->affiliates()->save($affiliate);

        // Optionally, interact with the ApiService
        // $this->apiService->createAffiliateOnRemote($affiliate);

        // You might want to perform additional actions, logging, etc.

        return $affiliate;
    }

    public function findOrCreateAffiliateByEmail(string $email): Affiliate
    {
        dd('hiii');
        // Attempt to find the affiliate by email
        $affiliate = Affiliate::where('email', $email)->first();
        dd($affiliate);

        // If the affiliate does not exist, create a new one
        if (!$affiliate) {
            $affiliate = Affiliate::create([
                'email' => $email,
                // You can set other attributes as needed
            ]);

            // Additional logic for new affiliate creation, if any
        }

        return $affiliate;
    }


}
