<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     * 
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // TODO: Complete this method

        try {
            // Extract necessary data from the incoming request
            $data = $request->all();
            // dd($data);
            

            // Pass the data to the processOrder method of OrderService
            dd($this->orderService->processOrder($data));

            // Return a success response
            return response()->json(['message' => 'Webhook processed successfully'], 200);
        } catch (\Exception $e) {
        

            // Log the exception
            Log::error('Error processing webhook: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Failed to process webhook'], 500);
        }
    }
    
}
