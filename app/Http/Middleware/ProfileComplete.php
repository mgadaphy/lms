<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileCompletionController;
use Symfony\Component\HttpFoundation\Response;

class ProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Skip middleware for these routes
        $excludedRoutes = [
            'profile.completion.show',
            'profile.completion.update',
            'logout',
            'verification.notice',
            'verification.verify',
            'verification.resend',
        ];
        
        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }
        
        // Check if user is a student and profile is incomplete
        if ($user && $user->role_id == 3 && !ProfileCompletionController::isProfileComplete($user)) {
            return redirect()->route('profile.completion.show');
        }
        
        return $next($request);
    }
    
    // Profile completion logic moved to ProfileCompletionController::isProfileComplete()
    // This eliminates code duplication and ensures consistency
}
