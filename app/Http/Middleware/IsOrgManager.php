<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Quest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsOrgManager
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string|null  $paramName  The route parameter name that contains the org_id or quest_id
     */
    public function handle(Request $request, Closure $next, ?string $paramName = 'org_id'): Response
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        // Get organization ID from request (body or route parameter)
        $orgId = $request->input('org_id') ?? $request->route($paramName);

        // If paramName is quest_id or similar, get org_id from the quest
        if (!$orgId && $request->route('id')) {
            $quest = Quest::find($request->route('id'));
            $orgId = $quest ? $quest->org_id : null;
        }

        if (!$orgId && $request->route('questId')) {
            $quest = Quest::find($request->route('questId'));
            $orgId = $quest ? $quest->org_id : null;
        }

        if (!$orgId) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not specified.'
            ], 400);
        }

        $organization = Organization::find($orgId);

        if (!$organization) {
            return response()->json([
                'success' => false,
                'message' => 'Organization not found.'
            ], 404);
        }

        // Check if user is MANAGER
        $isManager = OrganizationMember::where('organization_id', $organization->id)
            ->where('user_id', Auth::id())
            ->where('role', 'MANAGER')
            ->exists();

        // Check if user is CREATOR
        $isCreator = $organization->created_by === Auth::id();

        if (!$isManager && !$isCreator) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to manage this organization.'
            ], 403);
        }

        return $next($request);
    }
}
