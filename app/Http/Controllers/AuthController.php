<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    // Web3Auth JWKS endpoint
    private const WEB3AUTH_JWKS_URL = 'https://api-auth.web3auth.io/jwks';

    // Show login page
    public function showLogin(): View
    {
        return view('auth.login');
    }

    // Handle Web3Auth login/register
    public function web3Login(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'id_token' => 'required|string',
                'wallet_address' => 'required|string|size:42',
                'user_info' => 'required|array',
                'user_info.email' => 'required|email',
                'user_info.name' => 'required|string',
                'user_info.verifierId' => 'required|string',
            ]);

            $this->verifyWeb3AuthToken($validated['id_token']);

            $user = $this->findOrCreateUser(
                $validated['user_info'],
                $validated['wallet_address']
            );

            Auth::login($user, true);

            $isNewUser = $user->wasRecentlyCreated;
            $message = $isNewUser
                ? "Account created! Welcome, {$user->name}!"
                : "Welcome back, {$user->name}!";

            Log::info('Web3Auth Success', [
                'action' => $isNewUser ? 'register' : 'login',
                'email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('dashboard'),
                'message' => $message,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data provided.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Web3Auth Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication failed. Please try again.',
            ], 401);
        }
    }

    // Find existing user or create new one
    private function findOrCreateUser(array $userInfo, string $walletAddress): User
    {
        $user = User::where('email', $userInfo['email'])->first();

        if ($user) {
            $user->update([
                'wallet_address' => $walletAddress,
                'profile_pic_url' => $userInfo['profileImage'] ?? $user->profile_pic_url,
            ]);

            return $user;
        }

        return User::create([
            'email' => $userInfo['email'],
            'name' => $userInfo['name'],
            'verifier_id' => $userInfo['verifierId'],
            'wallet_address' => $walletAddress,
            'profile_pic_url' => $userInfo['profileImage'] ?? null,
            'auth_provider' => $userInfo['typeOfLogin'] ?? 'google',
            'password' => null,
        ]);
    }

    // Logout user and invalidate session
    public function logout(Request $request)
    {
        $email = Auth::user()?->email;

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::info('User logged out', ['email' => $email]);

        return redirect()->route('login');
    }

    // Verify Web3Auth JWT token
    private function verifyWeb3AuthToken(string $idToken): object
    {
        if (empty($idToken)) {
            throw new \Exception('Token is required');
        }

        try {
            $response = Http::timeout(10)->get(self::WEB3AUTH_JWKS_URL);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch JWKS keys');
            }

            $publicKeys = JWK::parseKeySet($response->json());
            $decoded = JWT::decode($idToken, $publicKeys);

            // Ensure token has required identifier
            if (!isset($decoded->sub) && isset($decoded->verifierId)) {
                $decoded->sub = $decoded->verifierId;
            }

            if (!isset($decoded->sub) && isset($decoded->wallets[0]->address)) {
                $decoded->sub = $decoded->wallets[0]->address;
            }

            if (!isset($decoded->sub)) {
                throw new \Exception('Token missing required identifier');
            }

            return $decoded;
        } catch (\Exception $e) {
            Log::error('JWT Verification Failed', [
                'error' => $e->getMessage(),
            ]);

            throw new \Exception('Token verification failed');
        }
    }
}
