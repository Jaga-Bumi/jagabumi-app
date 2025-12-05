<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Web3LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
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
    public function web3Login(Web3LoginRequest $request): JsonResponse
    {
        $user = $this->findOrCreateUser(
            $request->user_info,
            $request->wallet_address
        );

        $this->loginUser($user);

        return $this->successResponse($user);
    }

    // Find existing user or create new one
    private function findOrCreateUser(array $userInfo, string $walletAddress): User
    {
        return User::updateOrCreate(
            ['email' => $userInfo['email']],
            [
                'name' => $this->extractName($userInfo),
                'verifier_id' => $userInfo['verifierId'] ?? $userInfo['email'],
                'wallet_address' => $walletAddress,
                'profile_pic_url' => $userInfo['profileImage'] ?? null,
                'auth_provider' => $userInfo['typeOfLogin'] ?? 'google',
            ]
        );
    }

    // Extract user name from info or generate from email
    private function extractName(array $userInfo): string
    {
        return $userInfo['name'] ?? explode('@', $userInfo['email'])[0];
    }

    // Log the user into the application
    private function loginUser(User $user): void
    {
        Auth::login($user, true);
    }

    // Create successful authentication response
    private function successResponse(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'redirect' => route('dashboard'),
            'message' => $this->getWelcomeMessage($user),
        ]);
    }

    // Get appropriate welcome message
    private function getWelcomeMessage(User $user): string
    {
        return $user->wasRecentlyCreated
            ? "Welcome, {$user->name}!"
            : "Welcome back!";
    }

    // Log out the user and invalidate session
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $this->invalidateSession($request);

        return redirect()->route('login');
    }

    // Invalidate and regenerate session tokens
    private function invalidateSession(Request $request): void
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
