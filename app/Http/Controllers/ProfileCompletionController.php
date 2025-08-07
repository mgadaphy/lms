<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\UserInfo;
use Modules\StudentSetting\Entities\Institute;
use App\Models\TimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
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
        if (self::isProfileComplete($user)) {
            return redirect('/student-dashboard');
        }

        // Get initial states and cities if user has country/state selected
        $states = collect();
        $cities = collect();

        if ($user->country) {
            try {
                $states = State::select('id', 'name')
                    ->where('country_id', $user->country)
                    ->get();

                if ($user->state) {
                    $cities = City::select('id', 'name')
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
            'institutes' => Institute::select('id', 'name')->get(),
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
            ], 401)->header('Content-Type', 'application/json');
        }

        $user = Auth::user();

        // Only get the fields we expect from the request
        $validatedData = $request->only([
            'gender', 'phone', 'dob', 'address', 'city', 'state', 'country',
            'institute_id', 'timezone_id', 'about'
        ]);

        $validator = Validator::make($validatedData, [
            'gender' => ['required', 'string', 'in:male,female,other'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'dob' => ['required', 'date', 'before:today'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'integer', 'exists:spn_cities,id'],
            'state' => ['required', 'integer', 'exists:states,id'],
            'country' => ['required', 'integer', 'exists:countries,id'],
            'institute_id' => ['nullable', 'integer', 'exists:lms_institutes,id'],
            'timezone_id' => ['required', 'integer', 'exists:time_zones,id'],
            'about' => ['nullable', 'string', 'max:1000'],
        ]);
        
        // Additional security validation: ensure relationships are valid
        if (!$validator->fails()) {
            // Validate that state belongs to selected country
            $stateExists = State::where('id', $validatedData['state'])
                ->where('country_id', $validatedData['country'])
                ->exists();
            
            if (!$stateExists) {
                $validator->errors()->add('state', 'The selected state does not belong to the selected country.');
            }
            
            // Validate that city belongs to selected state
            $cityExists = City::where('id', $validatedData['city'])
                ->where('state_id', $validatedData['state'])
                ->exists();
            
            if (!$cityExists) {
                $validator->errors()->add('city', 'The selected city does not belong to the selected state.');
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422)->header('Content-Type', 'application/json');
        }

        // Use transaction to ensure data integrity
        DB::beginTransaction();

        try {
            // Update user basic info using validated data
            User::where('id', $user->id)->update([
                'phone' => $validatedData['phone'],
                'dob' => $validatedData['dob'],
                'address' => $validatedData['address'],
                'city' => $validatedData['city'],
                'state' => $validatedData['state'],
                'country' => $validatedData['country'],
                'gender' => $validatedData['gender'],
                'about' => $validatedData['about'] ?? null,
            ]);

            // Update or create user info
            UserInfo::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'timezone_id' => $validatedData['timezone_id'],
                    'institute_id' => $validatedData['institute_id'] ?? null,
                ]
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Profile update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Profile update failed. Please try again.',
            ], 500)->header('Content-Type', 'application/json');
        }

        // Refresh user data after update
        $user = User::find($user->id);

        // Check if profile is now complete
        $isComplete = self::isProfileComplete($user);
        $completionPercentage = $this->calculateProfileCompletion($user);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'is_complete' => $isComplete,
            'completion_percentage' => $completionPercentage,
            'redirect_url' => $isComplete ? '/student-dashboard' : null,
        ])->header('Content-Type', 'application/json');
    }

    /**
     * Check if user's profile is complete
     * This method is the single source of truth for profile completion logic
     */
    public static function isProfileComplete($user)
    {
        $requiredFields = [
            'phone', 'dob', 'address', 'city', 'state', 'country', 'gender'
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

            // Get states using Eloquent model
            $query = State::select('id', 'name')
                ->where('country_id', $countryId);

            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            $states = $query->get();

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

            // Get cities using Eloquent model
            $query = City::select('id', 'name')
                ->where('state_id', $stateId);

            if ($request->has('search') && !empty($request->search)) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
            
            $cities = $query->get();

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
