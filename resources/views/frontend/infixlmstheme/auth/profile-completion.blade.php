@extends('frontend.infixlmstheme.layouts.master')
@section('title'){{ __('Complete Your Profile') }} | {{ env('APP_NAME') }} @endsection

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

                        <form id="profile-completion-form" class="profile-completion-form" action="{{ route('profile.completion.update') }}" method="POST" data-ajax-form="true">
                            @csrf

<div class="row">
                                <div class="col-md-6">
                                    <div class="single_input">
                                        <span class="primary_label2">{{ __('Gender') }} <span class="text-danger">*</span></span>
                                        <select class="theme_select wide mb_20 rounded-pill" name="gender" id="gender">
                                            <option data-display="{{ __('Select Gender') }}" value="">{{ __('Select Gender') }}</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>{{ __('Other') }}</option>
                                        </select>
                                        <span class="text-danger" role="alert" id="gender-error"></span>
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
                                    <div class="single_input">
                                        <span class="primary_label2">{{ __('Country') }} <span class="text-danger">*</span></span>
                                        <select id="country" class="theme_select rounded-pill wide mb_20" name="country">
                                            @foreach($countries as $country)
                                                <option value="{{ $country->id }}" {{ old('country', $user->country) == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <span class="text-danger" role="alert" id="country-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="single_input" id="state_div">
                                        <span class="primary_label2">{{ __('State') }} <span class="text-danger">*</span></span>
                                        <select class="wide rounded-pill mb_20 stateList" name="state" id="state">
                                            <option data-display="{{ __('State') }}" value="#">{{ __('Select') }} {{ __('State') }}</option>
                                            @if(isset($states) && count($states) > 0)
                                                @foreach($states as $state)
                                                    <option value="{{ $state->id }}" {{ old('state', $user->state) == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger" role="alert" id="state-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="single_input" id="city_div">
                                        <span class="primary_label2">{{ __('City') }} <span class="text-danger">*</span></span>
                                        <select class="rounded-pill wide mb_20 cityList" name="city" id="city">
                                            <option data-display="{{ __('City') }}" value="#">{{ __('Select') }} {{ __('City') }}</option>
                                            @if(isset($cities) && count($cities) > 0)
                                                @foreach($cities as $city)
                                                    <option value="{{ $city->id }}" {{ old('city', $user->city) == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <span class="text-danger" role="alert" id="city-error"></span>
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

                            <!-- Submit Button -->
                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    {{ __('Save Profile') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <!-- Add CSS styling for Select2 dropdowns to match rounded pill design -->
    <style>
        /* Override all Select2 styling to match rounded pill design */
        .select2-container .select2-selection--single {
            height: 50px !important;
            border: 1px solid #e4e6ea !important;
            border-radius: 25px !important;
            background-color: #fff !important;
            padding: 0 20px !important;
            display: flex !important;
            align-items: center !important;
            box-shadow: none !important;
        }

        .select2-container .select2-selection__rendered {
            color: #333 !important;
            font-size: 14px !important;
            line-height: 50px !important;
            padding: 0 !important;
            margin: 0 !important;
            padding-left: 0 !important;
            padding-right: 20px !important;
        }

        .select2-container .select2-selection__arrow {
            height: 48px !important;
            right: 15px !important;
            top: 1px !important;
            width: 20px !important;
        }

        .select2-container .select2-selection__arrow b {
            border-color: #999 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 4px 0 4px !important;
            margin-top: -2px !important;
            margin-left: -4px !important;
        }

        /* Focus and open states */
        .select2-container.select2-container--open .select2-selection--single,
        .select2-container.select2-container--focus .select2-selection--single {
            border-color: #5D78FF !important;
            box-shadow: 0 0 0 0.2rem rgba(93, 120, 255, 0.25) !important;
        }

        /* Ensure country dropdown also gets the styling */
        #country {
            height: 50px !important;
            border-radius: 25px !important;
            padding: 0 20px !important;
        }

        /* Dropdown menu styling */
        .select2-dropdown {
            border: 1px solid #e4e6ea !important;
            border-radius: 8px !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
            margin-top: 2px !important;
        }

        .select2-results__option {
            padding: 10px 20px !important;
            font-size: 14px !important;
        }

        .select2-results__option--highlighted {
            background-color: #5D78FF !important;
            color: white !important;
        }
    </style>

    <!-- Include the external JavaScript file that handles all profile completion logic -->
    <script src="{{ asset('public/js/profile-completion.js') }}"></script>
    <script>
        // Initialize Select2 for all dropdowns including state and city
        $(document).ready(function() {
            // Initialize country dropdown (basic Select2)
            $('#country').select2({
                placeholder: "{{ __('Select Country') }}",
                allowClear: false
            });

            // Initialize state dropdown with AJAX - EXPLICIT INITIALIZATION
            $('#state').select2({
                placeholder: "{{ __('Select State') }}",
                allowClear: true,
                ajax: {
                    url: '/ajaxCounterState',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var countryId = $('#country').find(':selected').val();
                        if (!countryId || countryId === '') {
                            return null;
                        }
                        return {
                            search: params.term || '',
                            page: params.page || 1,
                            id: countryId
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || [],
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: false
                }
            });

            // Initialize city dropdown with AJAX - EXPLICIT INITIALIZATION
            $('#city').select2({
                placeholder: "{{ __('Select City') }}",
                allowClear: true,
                ajax: {
                    url: '/ajaxCounterCity',
                    type: 'GET',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        var stateId = $('#state').find(':selected').val();
                        if (!stateId || stateId === '' || stateId === '#') {
                            return null;
                        }
                        return {
                            search: params.term || '',
                            page: params.page || 1,
                            id: stateId
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || [],
                            pagination: {
                                more: data.pagination ? data.pagination.more : false
                            }
                        };
                    },
                    cache: false
                }
            });

            // Initialize other Select2 dropdowns (timezone, etc.)
            $('.select2').not('#country, #state, #city').select2();

            // Handle form submission
            $('#profile-completion-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.text();

                // Clear previous errors
                $('.invalid-feedback').remove();
                $('.is-invalid').removeClass('is-invalid');
                $('.alert').remove();

                // Show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating Profile...');

                // Prepare form data
                const formData = new FormData(form[0]);

                // Make AJAX request
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            const alert = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            form.prepend(alert);

                            // Redirect after a short delay if profile is complete
                            if (response.is_complete && response.redirect_url) {
                                setTimeout(function() {
                                    window.location.href = response.redirect_url;
                                }, 1500);
                            }
                        } else {
                            // Show error message
                            const alert = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ${response.message || 'An error occurred. Please try again.'}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            form.prepend(alert);
                        }

                        // Reset button
                        submitBtn.prop('disabled', false).text(originalText);

                        // Scroll to top
                        $('html, body').animate({ scrollTop: 0 }, 300);
                    },
                    error: function(xhr) {
                        // Reset button
                        submitBtn.prop('disabled', false).text(originalText);

                        if (xhr.status === 422) {
                            // Handle validation errors
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function(field) {
                                const input = $(`[name="${field}"]`);
                                const errorMessage = errors[field][0];

                                input.addClass('is-invalid');
                                input.after(`<div class="invalid-feedback">${errorMessage}</div>`);
                            });

                            // Show general error message
                            const alert = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Please correct the errors below and try again.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            form.prepend(alert);
                        } else {
                            console.error('Profile update error:', xhr.responseText);
                            const alert = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    An error occurred. Please try again.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            `;
                            form.prepend(alert);
                        }

                        // Scroll to top
                        $('html, body').animate({ scrollTop: 0 }, 300);
                    }
                });
            });
        });
    </script>
@endpush

