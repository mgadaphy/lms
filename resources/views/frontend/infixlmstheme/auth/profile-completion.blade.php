@extends('frontend.infixlmstheme.layouts.master')
@section('title'){{ __('Complete Your Profile') }} | {{ env('APP_NAME') }} @endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('public/frontend/infixlmstheme/css/select2.min.css') }}">
    <style>
        .progress {
            height: 10px;
            margin-bottom: 25px;
        }
        .progress-bar {
            background-color: #5D78FF;
            transition: width 0.6s ease;
        }
        .profile-complete-card {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 30px;
        }
        .profile-complete-header {
            background: linear-gradient(135deg, #5D78FF 0%, #5D78FF 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            padding: 20px;
        }
        .profile-complete-body {
            padding: 30px;
        }
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .form-control {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #e4e6fc;
        }
        .btn-primary {
            background-color: #5D78FF;
            border: none;
            padding: 10px 30px;
            font-weight: 600;
            border-radius: 5px;
        }
        .btn-primary:hover {
            background-color: #4a5fef;
        }
        .select2-container--default .select2-selection--single {
            height: 45px;
            border: 1px solid #e4e6fc;
            border-radius: 5px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 43px;
        }
    </style>
@endsection

@section('mainContent')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="profile-complete-card">
                    <div class="profile-complete-header text-center">
                        <h3>{{ __('Complete Your Profile') }}</h3>
                        <p class="mb-0">{{ __('Please provide the following information to complete your profile') }}</p>
                    </div>
                    <div class="profile-complete-body">
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{ __('Profile Completion') }}</span>
                                <span id="completion-percentage">{{ $completionPercentage }}%</span>
                            </div>
                            <div class="progress">
                                <div id="completion-progress" class="progress-bar" role="progressbar"
                                     style="width: {{ $completionPercentage }}%"
                                     aria-valuenow="{{ $completionPercentage }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100"></div>
                            </div>
                        </div>

                        <form id="profile-completion-form">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">{{ __('Gender') }} <span class="text-danger">*</span></label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option value="">{{ __('Select Gender') }}</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                        </select>
                                        <span class="invalid-feedback" id="gender-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                               value="{{ old('phone', $user->phone) }}" placeholder="{{ __('Enter your phone number') }}">
                                        <span class="invalid-feedback" id="phone-error"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dob">{{ __('Date of Birth') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="dob" name="dob"
                                               value="{{ old('dob', $user->dob ? \Carbon\Carbon::parse($user->dob)->format('Y-m-d') : '') }}">
                                        <span class="invalid-feedback" id="dob-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="institute_id">{{ __('Institute') }}</label>
                                        <select name="institute_id" id="institute_id" class="form-control select2">
                                            <option value="">{{ __('Select Institute') }}</option>
                                            @foreach($institutes as $institute)
                                                <option value="{{ $institute->id }}" {{ old('institute_id', $user->userInfo->institute_id ?? '') == $institute->id ? 'selected' : '' }}>
                                                    {{ $institute->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="institute_id-error"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">{{ __('Address') }} <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="address" name="address"
                                       value="{{ old('address', $user->address) }}" placeholder="{{ __('Enter your address') }}">
                                <span class="invalid-feedback" id="address-error"></span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="country">{{ __('Country') }} <span class="text-danger">*</span></label>
                                        <select name="country" id="country" class="form-control select2">
                                            <option value="">{{ __('Select Country') }}</option>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('country', $user->country) == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="country-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="state">{{ __('State/Province') }} <span class="text-danger">*</span></label>
                                        <select name="state" id="state" class="form-control">
                                            <option value="">{{ __('Select State/Province') }}</option>
                                            @if(isset($states) && count($states) > 0)
                                                @foreach($states as $state)
                                                    <option value="{{ $state->id }}" {{ old('state', $user->state) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="invalid-feedback" id="state-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">{{ __('City') }} <span class="text-danger">*</span></label>
                                        <select name="city" id="city" class="form-control">
                                            <option value="">{{ __('Select City') }}</option>
                                            @if(isset($cities) && count($cities) > 0)
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city', $user->city) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="invalid-feedback" id="city-error"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="timezone_id">{{ __('Timezone') }} <span class="text-danger">*</span></label>
                                <select name="timezone_id" id="timezone_id" class="form-control select2">
                                    <option value="">{{ __('Select Timezone') }}</option>
                                    @php
                                        $timezones = \Modules\Setting\Model\TimeZone::all();
                                    @endphp
                                    @foreach($timezones as $timezone)
                                        <option value="{{ $timezone->id }}" {{ old('timezone_id', $user->userInfo->timezone_id ?? '') == $timezone->id ? 'selected' : '' }}>
                                            {{ $timezone->time_zone }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="timezone_id-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="about">{{ __('About Me') }}</label>
                                <textarea class="form-control" id="about" name="about" rows="3"
                                          placeholder="{{ __('Tell us about yourself...') }}">{{ old('about', $user->about) }}</textarea>
                                <span class="invalid-feedback" id="about-error"></span>
                            </div>

                            <!-- Debug Section: Current Selections -->
                            <div class="card mt-4">
                                <div class="card-header bg-warning text-dark font-weight-bold d-flex justify-content-between align-items-center">
                                    <span>Debug Information</span>
                                </div>
                                <div class="card-body p-0" id="debug-container">
                                    <div class="p-3 border-bottom">
                                        <h6>Current Selections:</h6>
                                        <div id="current-selections" class="mb-3">
                                            <div>Country: <span id="debug-country">Not selected</span></div>
                                            <div>State: <span id="debug-state">Not selected</span></div>
                                            <div>City: <span id="debug-city">Not selected</span></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

    <!-- Load jQuery and Select2 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.4.0.min.js"></script>
    <script src="{{ asset('public/frontend/infixlmstheme/js/select2.min.js') }}"></script>
    <script>
    $(document).ready(function() {
        // Initialize Select2 for all relevant selects
        $('#country, #state, #city, .select2').select2({ width: '100%' });

        // Utility: update debug info
        function updateSelections() {
            let countryText = 'Not selected';
            let stateText = 'Not selected';
            let cityText = 'Not selected';
            
            // Get country text
            const $country = $('#country');
            if ($country.val()) {
                const countryData = $country.select2('data');
                countryText = countryData && countryData[0] ? countryData[0].text : $country.find('option:selected').text();
            }
            // Get state text
            const $state = $('#state');
            if ($state.val()) {
                const stateData = $state.select2('data');
                stateText = stateData && stateData[0] ? stateData[0].text : $state.find('option:selected').text();
            }
            // Get city text
            const $city = $('#city');
            if ($city.val()) {
                const cityData = $city.select2('data');
                cityText = cityData && cityData[0] ? cityData[0].text : $city.find('option:selected').text();
            }
            $('#debug-country').text(countryText);
            $('#debug-state').text(stateText);
            $('#debug-city').text(cityText);
        }

        // Load states for a given country
        function loadStates(countryId, selectedStateId = null, callback = null) {
            const $state = $('#state');
            const $city = $('#city');
            $state.prop('disabled', true).empty().append('<option value="">Select State</option>');
            $city.prop('disabled', true).empty().append('<option value="">Select City</option>');
            if (!countryId) {
                $state.prop('disabled', false);
                $city.prop('disabled', false);
                updateSelections();
                if (callback) callback();
                return;
            }
            $.ajax({
                url: '{{ route("profile.completion.getStates") }}',
                type: 'GET',
                data: { id: countryId },
                dataType: 'json',
                success: function(response) {
                    $state.prop('disabled', false).empty().append('<option value="">Select State</option>');
                    if (response.results && response.results.length > 0) {
                        response.results.forEach(function(state) {
                            $state.append(`<option value="${state.id}">${state.text}</option>`);
                        });
                        if (selectedStateId) {
                            $state.val(selectedStateId).trigger('change.select2');
                        }
                    }
                    updateSelections();
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading states:', error);
                    $state.prop('disabled', false).html('<option value="">Error loading states</option>');
                    updateSelections();
                    if (callback) callback();
                }
            });
        }

        // Load cities for a given state
        function loadCities(stateId, selectedCityId = null, callback = null) {
            const $city = $('#city');
            $city.prop('disabled', true).empty().append('<option value="">Select City</option>');
            if (!stateId) {
                $city.prop('disabled', false);
                updateSelections();
                if (callback) callback();
                return;
            }
            $.ajax({
                url: '{{ route("profile.completion.getCities") }}',
                type: 'GET',
                data: { id: stateId },
                dataType: 'json',
                success: function(response) {
                    $city.prop('disabled', false).empty().append('<option value="">Select City</option>');
                    if (response.results && response.results.length > 0) {
                        response.results.forEach(function(city) {
                            $city.append(`<option value="${city.id}">${city.text}</option>`);
                        });
                        if (selectedCityId) {
                            $city.val(selectedCityId).trigger('change.select2');
                        }
                    }
                    updateSelections();
                    if (callback) callback();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading cities:', error);
                    $city.prop('disabled', false).html('<option value="">Error loading cities</option>');
                    updateSelections();
                    if (callback) callback();
                }
            });
        }

        // On country change
        $('#country').on('change', function() {
            const countryId = $(this).val();
            // Clear state and city, then load new states
            loadStates(countryId, null, function() {
                // After loading states, city is always cleared
                $('#city').val('').trigger('change.select2');
            });
        });

        // On state change
        $('#state').on('change', function() {
            const stateId = $(this).val();
            // Clear city, then load new cities
            loadCities(stateId, null);
        });

        // On city change
        $('#city').on('change', function() {
            updateSelections();
        });

        // Initialize on page load
        function initializeDropdowns() {
            const initialCountry = $('#country').val();
            const initialState = $('#state').val();
            const initialCity = $('#city').val();
            // If both state and city are prefilled, chain the loading
            if (initialCountry) {
                loadStates(initialCountry, initialState, function() {
                    if (initialState) {
                        loadCities(initialState, initialCity);
                    } else {
                        updateSelections();
                    }
                });
            } else {
                updateSelections();
            }
        }
        // Wait for Select2 to be fully initialized
        setTimeout(initializeDropdowns, 100);
    });
    </script>

