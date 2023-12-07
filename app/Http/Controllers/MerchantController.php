<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MerchantController extends Controller
{
    protected MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // dd("Hiiiiii");
        // TODO: Complete this method

        try {
        //    dd('hiiiii');
            // Extract 'from' and 'to' dates from the request
            $fromDate = $request->input('from', now()->subMonth()->toDateString());
            $toDate = $request->input('to' , now()->addDays(2)->toDateString());
            // dd($toDate);
            // Fetch order statistics from the MerchantService
            $orderStats = $this->merchantService->getOrderStatistics($fromDate, $toDate);
            // dd($orderStats);
            // Return the formatted JSON response
            return response()->json([
                'count' => $orderStats['count'],
                'commission_owed' => $orderStats['commission_owed'],
                'revenue' => $orderStats['revenue'],
            ], 200);
        } catch (\Exception $e) {
            // Handle exceptions, log errors, or perform other error-handling actions

            // Log the exception
            Log::error('Error fetching order statistics: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Failed to fetch order statistics' . $e->getMessage()] , 500);
        }
    }


}
