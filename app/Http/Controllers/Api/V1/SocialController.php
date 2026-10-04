<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SocialResource;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SocialController extends Controller
{
    /**
     * List the social links stored in settings.
     */
    public function index(): JsonResponse
    {
        $socials = Setting::query()->where('key', 'socials')->value('value') ?? [];

        return response()->json([
            'data' => SocialResource::collection($socials),
        ]);
    }
}
