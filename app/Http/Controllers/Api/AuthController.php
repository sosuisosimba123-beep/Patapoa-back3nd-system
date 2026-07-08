<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'user_type' => 'required|in:customer,merchant,rider',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'is_active' => true,
            'is_verified' => false, // Email verification could be added here
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'Registration successful', 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Can be email or phone
            'password' => 'required|string',
            'user_type' => 'nullable|string|in:customer,merchant,rider,admin',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $login = $request->login;
        $user = User::where('email', $login)
                    ->orWhere('phone', $login)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('The provided credentials are incorrect.', 401);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Account is deactivated', 403);
        }

        // For testing purposes: morph user type if requested
        if ($request->has('user_type') && $user->user_type !== $request->user_type) {
            $user->user_type = $request->user_type;
            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
        ], 'Login successful');
    }

    /**
     * Social Login (Google)
     * This is a simplified endpoint for handling Google ID tokens sent from the app.
     */
    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'social_id' => 'required|string',
            'provider' => 'required|in:google,facebook',
            'user_type' => 'required|in:customer,merchant,rider',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        // Find or create user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // User doesn't exist, they need to complete registration (phone is missing)
            // For now, we return a flag indicating profile is incomplete
            return $this->successResponse([
                'email' => $request->email,
                'name' => $request->name,
                'social_id' => $request->social_id,
                'provider' => $request->provider,
                'is_new_user' => true,
            ], 'Social login successful, please complete your profile.');
        }

        // User exists, login them in
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'is_new_user' => false,
        ], 'Login successful');
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        // Generate OTP (in production, integrate with SMS gateway like Africa's Talking)
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache or database (simplified for demo)
        cache()->put("otp_{$request->phone}", $otp, now()->addMinutes(5));

        // TODO: Send SMS via Africa's Talking or Twilio
        // For demo, return OTP in response
        return $this->successResponse([
            'otp' => $otp, // Remove in production
        ], 'OTP sent successfully');
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $cachedOtp = cache()->get("otp_{$request->phone}");

        if ($cachedOtp !== $request->otp) {
            return $this->errorResponse('Invalid OTP', 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if ($user) {
            $user->update(['phone_verified_at' => now()]);
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
            ], 'Phone verified successfully');
        }

        return $this->errorResponse('User not found', 404);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    public function me(Request $request)
    {
        return $this->successResponse(
            $request->user()->load(['merchant', 'rider', 'wallet']),
            'User retrieved successfully'
        );
    }

    public function refresh(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth_token')->plainTextToken;

        return $this->successResponse(['token' => $token], 'Token refreshed successfully');
    }
}
