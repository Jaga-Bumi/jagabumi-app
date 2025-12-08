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
    public function showLogin()
    {
        return view('pages.auth.login');
    }
    
    // Process Web3Auth login/registration
    public function web3Login(Web3LoginRequest $request)
    {
        $user = $this->findOrCreateUser(
            $request->user_info,
            $request->wallet_address
        );

        Auth::login($user, true);

        $message = $user->wasRecentlyCreated
            ? "Welcome, {$user->name}!"
            : "Welcome back!";

        return ApiResponse::success();
    }

    // Find existing user or create new one
    private function findOrCreateUser(array $userInfo, string $walletAddress)
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
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return ApiResponse::success();
    }

}
