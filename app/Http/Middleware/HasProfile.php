<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HasProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // $table->string('name', 30);
        // $table->string('handle', 30)->nullable()->unique();
        // $table->string('email')->unique();
        // $table->text('bio')->nullable();
        // $table->string('phone')->nullable()->unique();
        // $table->string('verifier_id')->nullable()->unique();
        // $table->string('wallet_address', 42)->nullable()->unique();
        // $table->string('avatar_url')->nullable();
        // $table->string('auth_provider')->default('google');
        // $table->string('password')->nullable();
        // $table->boolean('is_removed')->default(false);
        // $table->enum('role', ['USER', 'ORG_MAKER', 'SUPER_ADMIN'])->default('USER');
        
        $user = $request->user();

        if ($user->role === 'SUPER_ADMIN') {
            return $next($request);
        }

        if (!$user->name || !$user->handle || !$user->wallet_address || !$user->phone || !$user->email) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Profile incomplete. Please complete your profile to proceed.'
            // ], 403);

            // Langsung ke onboarding page
            // return redirect()->route('onboarding');
        }

        return $next($request);
    }
}
