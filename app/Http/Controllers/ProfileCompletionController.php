<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\UserInfo;
use App\Country;
use Modules\StudentSetting\Entities\Institute;
use App\Models\TimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileCompletionController extends Controller
{
    /**
     * Show the profile completion form
     */
    public function show()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If profile is already complete, redirect to dashboard
        if ($this->isProfileComplete($user)) {
            return redirect()->route('student.dashboard');
        }

        // Get initial states and cities if user has country/state selected
        $states = collect();
        $cities = collect();
        
        if ($user->country) {
            try {
                $states = DB::table('states')
                    ->select('id', 'name')
                    ->where('country_id', $user->country)
                    ->get();
                    
                if ($user->state) {
                    $cities = DB::table('spn_cities')
                        ->select('id', 'name')
                        ->where('state_id', $user->state)
                        ->get();
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch initial states/cities: ' . $e->getMessage());
            }
        }

        $data = [
            'user' => $user,
            'countries' => Country::all(),
            'institutes' => Institute::all(['id', 'name']),
            'completionPercentage' => $this->calculateProfileCompletion($user),
            'states' => $states,
            'cities' => $cities,
        ];

        return view('frontend.infixlmstheme.auth.profile-completion', $data);
    }

    /**
     * Update the user's profile information
     */
    public function update(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'dob' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'exists:countries,id'],
            'institute_id' => ['nullable', 'exists:lms_institutes,id'],
            'timezone_id' => ['required', 'exists:time_zones,id'],
            'about' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update user basic info
        $userData = [
            'phone' => $request->phone,
            'dob' => $request->dob,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'gender' => $request->gender,
            'about' => $request->about,
        ];

        // Use query builder to update the user
        DB::table('users')
            ->where('id', $user->id)
            ->update($userData);

        // Update or create user info
        UserInfo::updateOrCreate(
            ['user_id' => $user->id],
            [
                'timezone_id' => $request->timezone_id,
                'short_description' => $request->about,
                'institute_id' => $request->institute_id,
            ]
        );

        // Check if profile is now complete
        $isComplete = $this->isProfileComplete($user);
        $completionPercentage = $this->calculateProfileCompletion($user);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'is_complete' => $isComplete,
            'completion_percentage' => $completionPercentage,
            'redirect_url' => $isComplete ? route('student.dashboard') : null,
        ]);
    }

    /**
     * Check if user's profile is complete
     */
    protected function isProfileComplete($user)
    {
        $requiredFields = [
            'phone', 'dob', 'address', 'city', 'state', 'country', 'gender'
        ];

        foreach ($requiredFields as $field) {
            if (empty($user->$field)) {
                return false;
            }
        }

        $userInfo = UserInfo::where('user_id', $user->id)->first();
        if (!$userInfo || !$userInfo->timezone_id) {
            return false;
        }

        return true;
    }

    /**
     * Calculate profile completion percentage
     */
    protected function calculateProfileCompletion($user)
    {
        $requiredFields = [
            'phone', 'dob', 'address', 'city', 'state', 'country', 'gender'
        ];

        $totalFields = count($requiredFields) + 1; // +1 for timezone
        $completedFields = 0;

        foreach ($requiredFields as $field) {
            if (!empty($user->$field)) {
                $completedFields++;
            }
        }

        $userInfo = UserInfo::where('user_id', $user->id)->first();
        if ($userInfo && $userInfo->timezone_id) {
            $completedFields++;
        }

        return $totalFields > 0 ? round(($completedFields / $totalFields) * 100) : 0;
    }

    /**
     * Get states for a country via AJAX
     */
    public function getStates(Request $request)
    {
        try {
            $countryId = $request->input('id');

            if (!$countryId) {
                return response()->json(['results' => []]);
            }

            // Get states from the database
            $states = DB::table('states')
                ->select('id', 'name')
                ->where('name', 'like', '%' . ($request->search ?? '') . '%')
                ->where('country_id', $countryId)
                ->get();

            $response = [];
            foreach ($states as $item) {
                $response[] = [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            }
            
            return response()->json(['results' => $response]);

        } catch (\Exception $e) {
            Log::error('Error in getStates: ' . $e->getMessage());
            return response()->json(['results' => []]);
        }
    }

    /**
     * Get cities for a state via AJAX
     */
    public function getCities(Request $request)
    {
        try {
            $stateId = $request->input('id');

            if (!$stateId) {
                return response()->json(['results' => []]);
            }

            // Get cities from the database
            $cities = DB::table('spn_cities')
                ->select('id', 'name')
                ->where('name', 'like', '%' . ($request->search ?? '') . '%')
                ->where('state_id', $stateId)
                ->get();

            $response = [];
            foreach ($cities as $item) {
                $response[] = [
                    'id' => $item->id,
                    'text' => $item->name
                ];
            }
            
            return response()->json(['results' => $response]);

        } catch (\Exception $e) {
            Log::error('Error in getCities: ' . $e->getMessage());
            return response()->json(['results' => []]);
        }
    }
}
