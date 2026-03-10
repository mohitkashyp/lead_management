<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasOrganization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // If user doesn't have a current organization set
        if (!$user->current_organization_id) {
            // Try to get default organization
            $defaultOrg = $user->getDefaultOrganization();
            
            if ($defaultOrg) {
                $user->switchOrganization($defaultOrg->id);
            } else {
                // Get first organization
                $firstOrg = $user->organizations()->first();
                
                if ($firstOrg) {
                    $user->switchOrganization($firstOrg->id);
                } else {
                    // User has no organization - redirect to setup
                    return redirect()->route('organization.setup')
                        ->with('error', 'Please create or join an organization first.');
                }
            }
        }

        // Share current organization with all views
        view()->share('currentOrganization', $user->currentOrganization);

        return $next($request);
    }
}