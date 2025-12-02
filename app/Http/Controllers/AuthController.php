<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    // Show login page
    public function showLogin(): View
    {
        return view('pages.auth.login');
    }

    // Get Web3Auth configuration
    public function getWeb3Config(): JsonResponse
    {
        return response()->json([
            'clientId' => config('services.web3auth.client_id'),
            'network' => config('services.web3auth.network'),
            'chain' => config('services.zksync'),
            'ui' => config('services.web3auth.ui'),
        ]);
    }

    // Handle Web3Auth login/register
    public function web3Login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'wallet_address' => 'required|string',
            'user_info' => 'required|array',
            'user_info.email' => 'required|email',
        ]);

        $user = $this->findOrCreateUser(
            $validated['user_info'],
            $validated['wallet_address']
        );

        Auth::login($user, true);

        return response()->json([
            'success' => true,
            'redirect' => route('dashboard'),
            'message' => $user->wasRecentlyCreated 
                ? "Welcome, {$user->name}!" 
                : "Welcome back!",
        ]);
    }

    // Find existing user or create new one
    private function findOrCreateUser(array $userInfo, string $walletAddress): User
    {
        return User::updateOrCreate(
            ['email' => $userInfo['email']],
            [
                'name' => $userInfo['name'] ?? explode('@', $userInfo['email'])[0],
                'verifier_id' => $userInfo['verifierId'] ?? $userInfo['email'],
                'wallet_address' => $walletAddress,
                'profile_pic_url' => $userInfo['profileImage'] ?? null,
                'auth_provider' => $userInfo['typeOfLogin'] ?? 'google',
            ]
        );
    }

    // Logout user and invalidate session
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }


}
