<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Requests\Api\V1\ResetPasswordRequest;
use App\Http\Requests\Api\V1\VerifyOtpRequest;
use App\Http\Requests\Api\V1\VerifyPhoneRequest;
use App\Http\Resources\Api\V1\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::query()->create($request->validated());

        $code = str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $user->update(['phone_confirmation_code' => $code]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => __('api.auth.registered'),
            'token' => $token,
            'user' => new UserResource($user),
            'debug_phone_code' => $code,
        ], 201);
    }

    /**
     * Log the user in using email or phone.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $identifier = $request->phone;

        $user = User::query()
            ->where('phone', $identifier)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => [__('api.auth.login_failed')],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'identifier' => [__('api.auth.account_inactive')],
            ]);
        }

        $user->update(['phone_verified_at' => now()]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => __('api.auth.otp_verified'),
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Revoke the current access token.
     */
    public function logout(): JsonResponse
    {
        $request = request();
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => __('api.auth.logged_out'),
        ]);
    }

    /**
     * Send an OTP reset code to the user's phone.
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => [__('api.auth.phone_not_found')],
            ]);
        }

        $otp = str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->phone],
            ['token' => bcrypt($otp), 'created_at' => now()]
        );

        // TODO: dispatch SMS with the OTP via the configured SMS provider.

        return response()->json([
            'message' => __('api.auth.otp_sent'),
            'debug_otp' => $otp,
        ]);
    }

    /**
     * Verify the OTP sent to the user's phone.
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if (! $user || $user->phone_confirmation_code !== $request->otp) {
            throw ValidationException::withMessages([
                'otp' => [__('api.auth.otp_invalid')],
            ]);
        }
        $user->update(['phone_verified_at' => now()]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => __('api.auth.otp_verified'),
            'token' => $token,
            'user' => new UserResource($user->refresh()),
        ]);
    }

    /**
     * Reset the user password after OTP verification.
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $record = \DB::table('password_reset_tokens')
            ->where('email', $request->phone)
            ->first();

        if (! $record || ! Hash::check($request->otp, $record->token)) {
            throw ValidationException::withMessages([
                'otp' => [__('api.auth.otp_invalid')],
            ]);
        }

        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => [__('api.auth.phone_not_found')],
            ]);
        }

        $user->update(['password' => $request->password]);

        \DB::table('password_reset_tokens')->where('email', $request->phone)->delete();

        return response()->json([
            'message' => __('api.auth.password_updated'),
        ]);
    }

    /**
     * Verify the user's phone number with the confirmation code.
     */
    public function verifyPhone(VerifyPhoneRequest $request): JsonResponse
    {
        $user = User::where('phone', $request->phone)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'phone' => [__('api.auth.phone_not_found')],
            ]);
        }

        if ($user->is_phone_confirmed) {
            return response()->json([
                'message' => __('api.auth.phone_already_confirmed'),
            ]);
        }

        if ($user->phone_confirmation_code !== $request->code) {
            throw ValidationException::withMessages([
                'code' => [__('api.auth.phone_code_invalid')],
            ]);
        }

        $user->update([
            'is_phone_confirmed' => true,
            'phone_confirmation_code' => null,
        ]);

        return response()->json([
            'message' => __('api.auth.phone_confirmed'),
        ]);
    }
}
