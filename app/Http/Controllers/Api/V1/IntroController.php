<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\IntroResource;
use App\Models\Intro;
use Illuminate\Http\JsonResponse;

class IntroController extends Controller
{
    /**
     * List all active intros.
     */
    public function index(): JsonResponse
    {
        $intros = Intro::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => IntroResource::collection($intros),
        ]);
    }
}
