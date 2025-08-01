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
                                        <label for="state">{{ __('State/Province') }} <span class="text-danger">*</span></label>
                                        <select name="state" id="state" class="form-control">
                                            <option value="">{{ __('Select State') }}</option>
                                            @if($user->state)
                                                <option value="{{ $user->state }}" selected>{{ $user->state }}</option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback" id="state-error"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">{{ __('City') }} <span class="text-danger">*</span></label>
                                        <select name="city" id="city" class="form-control">
                                            <option value="">{{ __('Select City') }}</option>
                                            @if($user->city)
                                                <option value="{{ $user->city }}" selected>{{ $user->city }}</option>
                                            @endif
                                        </select>
                                        <span class="invalid-feedback" id="city-error"></span>
                                    </div>
                                </div>
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

                            <div class="form-group text-center mt-4">
                                <button type="submit" class="btn btn-primary" id="submit-btn">
                                    <span id="submit-text">{{ __('Save & Continue') }}</span>
                                    <span id="submit-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('public/frontend/infixlmstheme/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('.select2').select2({
                width: '100%'
            });

            // Initialize state select2
            $('#state').select2({
                width: '100%',
                placeholder: '{{ __("Select State") }}',
                allowClear: true,
                ajax: {
                    url: '{{ route("profile.completion.getStates") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            country_id: $('#country').val(),
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; },
                minimumInputLength: 1
            });

            // Initialize city select2
            $('#city').select2({
                width: '100%',
                placeholder: '{{ __("Select City") }}',
                allowClear: true,
                ajax: {
                    url: '{{ route("profile.completion.getCities") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            state_id: $('#state').val(),
                            search: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results || []
                        };
                    },
                    cache: true
                },
                escapeMarkup: function (markup) { return markup; },
                minimumInputLength: 1
            });

            // Load states when country changes
            $('#country').on('change', function() {
                let countryId = $(this).val();
                let stateSelect = $('#state');
                let citySelect = $('#city');

                // Clear current states and cities
                stateSelect.val(null).trigger('change');
                citySelect.val(null).trigger('change');

                if (countryId) {
                    // Enable state select
                    stateSelect.prop('disabled', false);
                } else {
                    stateSelect.prop('disabled', true);
                    citySelect.prop('disabled', true);
                }
            });

            // Load cities when state changes
            $('#state').on('change', function() {
                let stateId = $(this).val();
                let citySelect = $('#city');

                // Clear current cities
                citySelect.val(null).trigger('change');

                if (stateId) {
                    // Enable city select
                    citySelect.prop('disabled', false);
                } else {
                    citySelect.prop('disabled', true);
                }
            });

            // Trigger country change if a country is already selected
            @if(!empty($user->country))
                $('#country').trigger('change');
            @endif
            @if(!empty($user->state))
                $('#state').trigger('change');
            @endif

            // Handle form submission
            $('#profile-completion-form').on('submit', function(e) {
                e.preventDefault();

                // Reset errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Show loading state
                const submitBtn = $('#submit-btn');
                const submitText = $('#submit-text');
                const submitSpinner = $('#submit-spinner');

                submitBtn.prop('disabled', true);
                submitText.addClass('d-none');
                submitSpinner.removeClass('d-none');

                // Get form data
                let formData = new FormData(this);

                // Submit form via AJAX
                $.ajax({
                    url: '{{ route("profile.completion.update") }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update progress bar
                            $('#completion-percentage').text(response.completion_percentage + '%');
                            $('#completion-progress').css('width', response.completion_percentage + '%')
                                .attr('aria-valuenow', response.completion_percentage);

                            // Show success message
                            toastr.success(response.message);

                            // Redirect if profile is complete
                            if (response.is_complete && response.redirect_url) {
                                setTimeout(function() {
                                    window.location.href = response.redirect_url;
                                }, 1500);
                            }
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            // Validation errors
                            let errors = xhr.responseJSON.errors;
                            for (let field in errors) {
                                $(`#${field}`).addClass('is-invalid');
                                $(`#${field}-error`).text(errors[field][0]);
                            }
                        } else {
                            // Other errors
                            toastr.error('{{ __("Something went wrong. Please try again.") }}');
                        }
                    },
                    complete: function() {
                        // Reset button state
                        submitBtn.prop('disabled', false);
                        submitText.removeClass('d-none');
                        submitSpinner.addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush
