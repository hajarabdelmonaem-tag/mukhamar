<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\OrderEnquiryRequest;
use App\Services\OrderEnquiryService;
use Illuminate\Http\JsonResponse;

class OrderEnquiryController extends Controller
{
    /**
     * Provide a shipping and financial breakdown for the cart's products
     * based on the selected delivery address.
     */
    public function __invoke(OrderEnquiryRequest $request, OrderEnquiryService $service): JsonResponse
    {
        $enquiry = $service->calculate($request->user(), $request->integer('address_id'));

        return response()->json([
            'data' => $enquiry,
        ]);
    }
}
