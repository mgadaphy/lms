<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if ($user && $user->role_id == 3 && !$this->isProfileComplete($user)) {
            return redirect()->route('profile.completion.show');
        }
        
        return $next($request);
    }
    
    /**
     * Check if user's profile is complete
     */
    protected function isProfileComplete($user)
    {
        $requiredFields = [
            'phone', 'dob', 'address', 'city', 'country', 'gender'
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        $userInfo = $user->userInfo;
        if (!$userInfo || !$userInfo->timezone_id) {
            return false;
        }

        return true;
    }
}
