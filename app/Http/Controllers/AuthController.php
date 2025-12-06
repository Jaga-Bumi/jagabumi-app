<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Web3LoginRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.auth.login');
    }
    
    // Process Web3Auth login/registration
    public function web3Login(Web3LoginRequest $request): ApiResponse
    {
        $user = $this->findOrCreateUser(
            $request->user_info,
            $request->wallet_address
        );

        Auth::login($user, true);

        $message = $user->wasRecentlyCreated
            ? "Welcome, {$user->name}!"
            : "Welcome back!";

        return ApiResponse::success(
            [
                'redirect' => route('dashboard'),
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'wallet_address' => $user->wallet_address,
                ]
            ],
            $message
        );
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
                'avatar_url' => $userInfo['profileImage'] ?? null,
                'auth_provider' => $userInfo['typeOfLogin'] ?? 'google',
            ]
        );
    }

    // Log out the user and invalidate session
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

}
