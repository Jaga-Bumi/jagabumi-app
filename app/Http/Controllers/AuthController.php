<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Web3LoginRequest;
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

    // Handle Web3Auth login/register
    public function web3Login(Web3LoginRequest $request): JsonResponse
    {
        $user = $this->findOrCreateUser(
            $request->user_info,
            $request->wallet_address
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
