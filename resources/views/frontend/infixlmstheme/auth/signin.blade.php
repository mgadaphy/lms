@php use Modules\StudentSetting\Entities\Institute; @endphp
@extends(theme('auth.layouts.app'))
@section('content')
<div class="modern-signin-wrapper">
    <div class="signin-container">
        <!-- Left Side - Form -->
        <div class="signin-form-section">
            <div class="form-header">
                <div class="logo-container">
                    <a href="{{ url('/') }}">
                        <img src="{{asset(Settings('logo') )}}" alt="Logo" class="logo">
                    </a>
                </div>
                <div class="welcome-text">
                    <h1 class="welcome-title" id="page-title">Welcome back</h1>
                    <p class="welcome-subtitle" id="page-subtitle">Please sign in to your account</p>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-btn active" data-tab="login" onclick="switchTab('login')">
                    <svg class="tab-icon" viewBox="0 0 24 24">
                        <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    <span>Log In</span>
                </button>
                <button class="tab-btn" data-tab="register" onclick="switchTab('register')">
                    <svg class="tab-icon" viewBox="0 0 24 24">
                        <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Sign Up</span>
                </button>
            </div>

            <!-- Success Messages Container -->
            <div id="success-container" class="success-container" style="display: none;"></div>

            <!-- Error Messages Container -->
            <div id="error-container" class="error-container" style="display: none;">
                <div id="error-messages" class="error-messages"></div>
            </div>

            <!-- Login Tab Content -->
            <div id="login-tab" class="tab-content active">
                <!-- Social Login Buttons -->
                @if(saasEnv('ALLOW_FACEBOOK_LOGIN')=='true' || saasEnv('ALLOW_GOOGLE_LOGIN')=='true')
                <div class="social-login-section">
                    @if(saasEnv('ALLOW_FACEBOOK_LOGIN')=='true')
                    <a href="{{ route('social.oauth', 'facebook') }}" class="social-btn facebook-btn">
                        <svg class="social-icon" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                        <span>Continue with Facebook</span>
                    </a>
                    @endif

                    @if(saasEnv('ALLOW_GOOGLE_LOGIN')=='true')
                    <a href="{{ route('social.oauth', 'google') }}" class="social-btn google-btn">
                        <svg class="social-icon" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>
                    @endif

                    <div class="divider">
                        <span class="divider-text">or</span>
                    </div>
                </div>
                @endif

                <!-- Login Form -->
                <form action="{{route('login')}}" method="POST" id="loginForm" class="signin-form">
                    @csrf
                    <div class="form-group">
                        <label for="login-email" class="form-label">{{__('common.Email Address')}}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <input type="email" 
                                   id="login-email"
                                   name="email" 
                                   value="{{old('email')}}"
                                   class="form-input"
                                   placeholder="{{__('common.Enter your email')}}"
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="login-password" class="form-label">{{__('common.Password')}}</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                            </svg>
                            <input type="password" 
                                   id="login-password"
                                   name="password" 
                                   class="form-input"
                                   placeholder="{{__('common.Enter your password')}}"
                                   autocomplete="current-password"
                                   required>
                            <button type="button" class="password-toggle" onclick="togglePassword('login-password')">
                                <svg class="eye-icon" viewBox="0 0 24 24">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Captcha -->
                    @if(saasEnv('NOCAPTCHA_FOR_LOGIN')=='true')
                    <div class="form-group">
                        @if(saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
                            {!! NoCaptcha::display(["data-size"=>"invisible"]) !!}
                        @else
                            {!! NoCaptcha::display() !!}
                        @endif
                    </div>
                    @endif

                    <!-- Remember Me & Forgot Password -->
                    <div class="form-options">
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }} value="1">
                            <span class="checkmark"></span>
                            <span class="checkbox-label">{{__('common.Remember Me')}}</span>
                        </label>
                        
                        @if(Settings('allow_force_logout'))
                        <label class="checkbox-wrapper">
                            <input type="checkbox" name="force" {{ old('force') ? 'checked' : '' }} value="1">
                            <span class="checkmark"></span>
                            <span class="checkbox-label">{{__('auth.Force login')}}</span>
                        </label>
                        @endif

                        <a href="{{route('SendPasswordResetLink')}}" class="forgot-link">
                            {{__('common.Forgot Password')}}?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group">
                        @if(saasEnv('NOCAPTCHA_FOR_LOGIN')=='true' && saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
                            <button type="button" class="submit-btn g-recaptcha" 
                                    data-sitekey="{{saasEnv('NOCAPTCHA_SITEKEY')}}" 
                                    data-size="invisible" 
                                    data-callback="onLoginSubmit">
                                <span class="btn-text">{{__('common.Login')}}</span>
                                <svg class="btn-icon" viewBox="0 0 24 24">
                                    <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                                </svg>
                            </button>
                        @else
                            <button type="submit" class="submit-btn">
                                <span class="btn-text">{{__('common.Login')}}</span>
                                <svg class="btn-icon" viewBox="0 0 24 24">
                                    <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </form>

                <!-- Demo Login Buttons -->
                @if(config('app.demo_mode'))
                <div class="demo-section">
                    <p class="demo-text">{{__('common.Demo Login')}}</p>
                    <div class="demo-buttons">
                        @foreach($roles as $role)
                        <a href="{{route('auto.login',$role->id)}}" class="demo-btn">
                            {{$role->name}}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Register Tab Content -->
            <div id="register-tab" class="tab-content">
                <form action="{{route('signin.register')}}" method="POST" id="registerForm" class="signin-form">
                    @csrf
                    <div class="row">
                        @if(isModuleActive('Organization'))
                            <div class="col-12 mt_20">
                                <label>{{trans('organization.account_type')}}</label>
                                <ul class="quiz_select d-flex">
                                    <li>
                                        <label class="primary_bulet_checkbox d-flex">
                                            <input checked class="quizAns" name="account_type" type="radio" value="3">
                                            <span class="checkmark mr_10"></span>
                                            <span class="label_name">{{__('common.Student')}} </span>
                                        </label>
                                    </li>
                                    <li class="ms-3">
                                        <label class="primary_bulet_checkbox d-flex">
                                            <input class="quizAns" name="account_type" type="radio" value="5">
                                            <span class="checkmark mr_10"></span>
                                            <span class="label_name">{{__('organization.Organization')}} </span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        @endif

                        @if($custom_field->show_name)
                            <div class="col-12 mt_20">
                                <div class="input-group custom_group_field">
                                    <input type="text" class="form-control ps-0"
                                           placeholder="{{__('student.Enter Full Name')}} {{ $custom_field->required_name ? '*' : ''}}"
                                           {{ $custom_field->required_name ? 'required' : ''}} 
                                           name="name" value="{{old('name')}}">
                                </div>
                            </div>
                        @endif

                        <div class="col-12 mt_20">
                            <div class="input-group custom_group_field">
                                <input type="email" class="form-control ps-0" required
                                       placeholder="{{__('common.Enter Email')}} *" 
                                       name="email" value="{{old('email')}}">
                            </div>
                        </div>

                        @if($custom_field->show_phone)
                            <div class="col-12 mt_20">
                                <div class="input-group custom_group_field">
                                    <input type="text" class="form-control ps-0"
                                           placeholder="{{__('common.Enter Phone Number')}} {{ $custom_field->required_phone ? '*' : ''}}"
                                           {{ $custom_field->required_phone ? 'required' : ''}}
                                           name="phone" value="{{old('phone')}}">
                                </div>
                            </div>
                        @endif

                        <div class="col-12 mt_20">
                            <div class="input-group custom_group_field">
                                <input type="password" class="form-control ps-0" required
                                       placeholder="{{__('frontend.Enter Password')}} *"
                                       autocomplete="new-password" name="password">
                            </div>
                        </div>

                        <div class="col-12 mt_20">
                            <div class="input-group custom_group_field">
                                <input type="password" class="form-control ps-0" required
                                       placeholder="{{__('common.Enter Confirm Password')}} *"
                                       name="password_confirmation">
                            </div>
                        </div>

                        @if($custom_field->show_dob)
                            <div class="col-12 mt_20">
                                <div class="input-group custom_group_field">
                                    <input id="dob" type="text" class="form-control ps-0 datepicker w-100"
                                           placeholder="{{__('common.Date of Birth')}} {{ $custom_field->required_dob ? '*' : ''}}"
                                           {{ $custom_field->required_dob ? 'required' : ''}}
                                           name="dob" value="{{ old('dob') }}">
                                </div>
                            </div>
                        @endif

                        @if($custom_field->show_company)
                            <div class="col-12 mt_20">
                                <div class="input-group custom_group_field">
                                    <input type="text" class="form-control ps-0"
                                           placeholder="{{__('common.Enter Company')}} {{ $custom_field->required_company ? '*' : ''}}"
                                           {{ $custom_field->required_company ? 'required' : ''}}
                                           name="company" value="{{old('company')}}">
                                </div>
                            </div>
                        @endif

                        @if($custom_field->show_identification_number)
                            <div class="col-12 mt_20">
                                <div class="input-group custom_group_field">
                                    <input type="text" class="form-control ps-0"
                                           placeholder="{{__('common.Enter Identification Number')}} {{ $custom_field->required_identification_number ? '*' : ''}}"
                                           {{ $custom_field->required_identification_number ? 'required' : ''}}
                                           name="identification_number" value="{{old('identification_number')}}">
                                </div>
                            </div>
                        @endif

                        @if($custom_field->show_job_title)
                            <div class="col-12 mt_20">
                                <div class="input-group custom_group_field">
                                    <input type="text" class="form-control ps-0"
                                           placeholder="{{__('common.Enter Job Title')}} {{ $custom_field->required_job_title ? '*' : ''}}"
                                           {{ $custom_field->required_job_title ? 'required' : ''}}
                                           name="job_title" value="{{old('job_title')}}">
                                </div>
                            </div>
                        @endif

                        @if($custom_field->show_gender)
                            <div class="col-xl-12">
                                <div class="short_select mt-3">
                                    <div class="row">
                                        <div class="col-xl-5">
                                            <h5 class="mr_10 font_16 f_w_500 mb-0">{{ __('common.choose_gender') }} {{ $custom_field->required_gender ? '*' : '' }}</h5>
                                        </div>
                                        <div class="col-xl-7">
                                            <select class="small_select w-100" name="gender" {{ $custom_field->required_gender ? 'selected' : '' }}>
                                                <option value="male">{{__('common.Male')}}</option>
                                                <option value="female">{{__('common.Female')}}</option>
                                                <option value="other">{{__('common.Other')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($custom_field->show_student_type)
                            <div class="col-xl-12">
                                <div class="short_select mt-3">
                                    <div class="row">
                                        <div class="col-xl-5">
                                            <h5 class="mr_10 font_16 f_w_500 mb-0">{{ __('common.choose_student_type') }} {{ $custom_field->required_student_type ? '*' : '' }}</h5>
                                        </div>
                                        <div class="col-xl-7">
                                            <select class="small_select w-100" name="student_type" {{ $custom_field->required_student_type ? 'selected' : '' }}>
                                                <option value="personal">{{__('common.Personal')}}</option>
                                                <option value="corporate">{{__('common.Corporate')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($custom_field->show_institute)
                            <div class="col-xl-12">
                                <div class="short_select mt-3">
                                    <div class="row">
                                        <div class="col-xl-5">
                                            <h5 class="mr_10 font_16 f_w_500 mb-0">{{ __('common.choose_institute') }} {{ $custom_field->required_institute ? '*' : '' }}</h5>
                                        </div>
                                        <div class="col-xl-7">
                                            <select class="small_select w-100" name="institute_id">
                                                <option value="">{{__('common.select_one')}}</option>
                                                @foreach(Institute::where('status',1)->get() as $institute)
                                                    <option value="{{$institute->id}}">{{$institute->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(isModuleActive('Affiliate'))
                            <div class="col-12 mt_20">
                                <div class="input-group custom_group_field">
                                    <input type="text" class="form-control ps-0"
                                           placeholder="{{__('affiliate.Referral Code')}} ({{__('frontend.Optional')}})"
                                           name="referral_code" value="{{old('referral_code')}}">
                                </div>
                            </div>
                        @endif

                        <div class="col-12 mt_20">
                            <div class="remember_forgot_passs d-flex align-items-center">
                                <label class="primary_checkbox d-flex" for="checkbox">
                                    <input type="checkbox" id="checkbox" required>
                                    <span class="checkmark mr_15"></span>
                                    <p>{{__('frontend.By signing up, you agree to')}} <a target="_blank" href="{{url('terms')}}">{{__('frontend.Terms of Service')}}</a> {{__('frontend.and')}} <a target="_blank" href="{{url('privacy')}}">{{__('frontend.Privacy Policy')}}</a></p>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mt_20">
                            @if(saasEnv('NOCAPTCHA_FOR_REG')=='true')
                                @if(saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
                                    {!! NoCaptcha::display(["data-size"=>"invisible"]) !!}
                                @else
                                    {!! NoCaptcha::display() !!}
                                @endif
                            @endif
                        </div>

                        <div class="col-12 mt_20">
                            @if(saasEnv('NOCAPTCHA_FOR_REG')=='true' && saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
                                <button type="button" class="g-recaptcha theme_btn text-center w-100 disable_btn" disabled
                                        data-sitekey="{{saasEnv('NOCAPTCHA_SITEKEY')}}" data-size="invisible"
                                        data-callback="onRegisterSubmit"
                                        class="theme_btn text-center w-100"> {{__('common.Register')}}</button>
                            @else
                                <button type="submit" class="theme_btn text-center w-100 disable_btn" disabled id="submitBtn">
                                    {{__('common.Register')}}
                                </button>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Side - Banner -->
        <div class="signin-banner-section">
            @php
                $route = Route::currentRouteName();
                if($route=="register"){
                    $title = $page->reg_title;
                    $banner = $page->reg_banner;
                    $slogans1 = $page->reg_slogans1;
                    $slogans2 = $page->reg_slogans2;
                    $slogans3 = $page->reg_slogans3;
                } elseif($route=="login" || $route=="signin"){
                    $title = $page->title;
                    $banner = $page->banner;
                    $slogans1 = $page->slogans1;
                    $slogans2 = $page->slogans2;
                    $slogans3 = $page->slogans3;
                } else {
                    $title = $page->forget_title;
                    $banner = $page->forget_banner;
                    $slogans1 = $page->forget_slogans1;
                    $slogans2 = $page->forget_slogans2;
                    $slogans3 = $page->forget_slogans3;
                }
            @endphp
            
            <div class="banner-content">
                <div class="banner-text">
                    <h2 class="banner-title">{{$title ?? 'Welcome to Infix Learning Management System'}}</h2>
                    <div class="banner-slogans">
                        <span class="slogan">{{$slogans1 ?? 'Excellence.'}}</span>
                        <span class="slogan">{{$slogans2 ?? 'Community.'}}</span>
                        <span class="slogan">{{$slogans3 ?? 'Diversity.'}}</span>
                    </div>
                </div>
                <div class="banner-image">
                    <img src="{{asset($banner ?? 'public/frontend/infixlmstheme/img/banner/global.png')}}" alt="Banner">
                </div>
            </div>
        </div>
    </div>
</div>

{!! Toastr::message() !!}

<style>
/* Success message styles */
.success-container {
    transition: all 0.3s ease;
}

.alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border: 1px solid #badbcc;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem;
    margin: 0 0 1.5rem 0;
    display: flex;
    align-items: flex-start;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.alert-success i {
    font-size: 1.25rem;
    margin-top: 0.15rem;
    margin-right: 0.75rem;
    color: #0f5132;
}

.alert-success div {
    flex: 1;
}

.error-container {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 0.25rem;
    color: #721c24;
    margin-bottom: 1.5rem;
    padding: 1rem;
}

.error-container ul {
    margin-bottom: 0;
    padding-left: 1.5rem;
}

.error-container li {
    margin-bottom: 0.25rem;
}

.error-container li:last-child {
    margin-bottom: 0;
}
.modern-signin-wrapper {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.signin-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    display: grid;
    grid-template-columns: 1fr 1fr;
    max-width: 1200px;
    width: 100%;
    min-height: 600px;
}

.signin-form-section {
    padding: 60px 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.form-header {
    text-align: center;
    margin-bottom: 40px;
}

.logo-container {
    margin-bottom: 30px;
}

.logo {
    max-width: 180px;
    height: auto;
}

.welcome-title {
    font-size: 32px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 8px;
    font-family: var(--font_family1);
}

.welcome-subtitle {
    font-size: 16px;
    color: #718096;
    margin: 0;
    font-family: var(--font_family2);
}

/* Tab Navigation Styles */
.tab-navigation {
    display: flex;
    background: #f7fafc;
    border-radius: 12px;
    padding: 4px;
    margin-bottom: 30px;
    gap: 4px;
}

.tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    background: transparent;
    border-radius: 8px;
    color: #718096;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: var(--font_family2);
}

.tab-btn.active {
    background: white;
    color: #667eea;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.tab-btn:hover:not(.active) {
    color: #4a5568;
}

.tab-icon {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

/* Tab Content Styles */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* Error Container Styles */
.error-container {
    background: #fed7d7;
    border: 1px solid #feb2b2;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
}

.error-messages {
    color: #c53030;
    font-size: 14px;
    font-weight: 500;
}

.error-messages ul {
    margin: 0;
    padding-left: 20px;
}

.error-messages li {
    margin-bottom: 4px;
}

/* Social Login Styles */
.social-login-section {
    margin-bottom: 30px;
}

.social-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 12px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    background: white;
    color: #4a5568;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.3s ease;
    margin-bottom: 12px;
}

.social-btn:hover {
    border-color: #cbd5e0;
    background: #f7fafc;
    transform: translateY(-1px);
}

.social-icon {
    width: 20px;
    height: 20px;
    margin-right: 12px;
    fill: currentColor;
}

.facebook-btn {
    border-color: #1877f2;
    color: #1877f2;
}

.facebook-btn:hover {
    background: #1877f2;
    color: white;
}

.google-btn {
    border-color: #db4437;
    color: #db4437;
}

.google-btn:hover {
    background: #db4437;
    color: white;
}

.divider {
    position: relative;
    text-align: center;
    margin: 20px 0;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #e2e8f0;
}

.divider-text {
    background: white;
    padding: 0 15px;
    color: #718096;
    font-size: 14px;
    font-weight: 500;
}

/* Form Styles */
.signin-form {
    width: 100%;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 8px;
    font-family: var(--font_family2);
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-icon {
    position: absolute;
    left: 16px;
    width: 20px;
    height: 20px;
    fill: #a0aec0;
    z-index: 1;
}

.form-input {
    width: 100%;
    padding: 16px 16px 16px 48px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    font-family: var(--font_family2);
    transition: all 0.3s ease;
    background: white;
}

.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.password-toggle {
    position: absolute;
    right: 16px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
}

.eye-icon {
    width: 20px;
    height: 20px;
    fill: #a0aec0;
    transition: fill 0.3s ease;
}

.password-toggle:hover .eye-icon {
    fill: #4a5568;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}

.checkbox-wrapper {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #4a5568;
    font-family: var(--font_family2);
}

.checkbox-wrapper input[type="checkbox"] {
    display: none;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    margin-right: 8px;
    position: relative;
    transition: all 0.3s ease;
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkmark {
    background: #667eea;
    border-color: #667eea;
}

.checkbox-wrapper input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.forgot-link {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: color 0.3s ease;
}

.forgot-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

.submit-btn {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Inter', sans-serif;
}

.submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.btn-text {
    margin-right: 8px;
}

.btn-icon {
    width: 20px;
    height: 20px;
    fill: currentColor;
    transition: transform 0.3s ease;
}

.submit-btn:hover .btn-icon {
    transform: translateX(4px);
}

.demo-section {
    margin-top: 20px;
    text-align: center;
}

.demo-text {
    font-size: 14px;
    color: #718096;
    margin-bottom: 12px;
    font-family: var(--font_family2);
}

.demo-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
    flex-wrap: wrap;
}

.demo-btn {
    padding: 8px 16px;
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    color: #4a5568;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
    font-family: var(--font_family2);
}

.demo-btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

/* Registration Form Styles */
.custom_group_field {
    position: relative;
}

.custom_group_field .form-control {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    font-family: var(--font_family2);
    transition: all 0.3s ease;
    background: white;
}

.custom_group_field .form-control:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.quiz_select {
    list-style: none;
    padding: 0;
    margin: 0;
}

.primary_bulet_checkbox {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #4a5568;
    font-family: var(--font_family2);
}

.primary_bulet_checkbox input[type="radio"] {
    display: none;
}

.primary_bulet_checkbox .checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #e2e8f0;
    border-radius: 50%;
    margin-right: 8px;
    position: relative;
    transition: all 0.3s ease;
}

.primary_bulet_checkbox input[type="radio"]:checked + .checkmark {
    background: #667eea;
    border-color: #667eea;
}

.primary_bulet_checkbox input[type="radio"]:checked + .checkmark::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 6px;
    height: 6px;
    background: white;
    border-radius: 50%;
}

.short_select {
    margin-bottom: 20px;
}

.small_select {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    font-family: var(--font_family2);
    transition: all 0.3s ease;
    background: white;
}

.small_select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.remember_forgot_passs {
    margin-bottom: 20px;
}

.primary_checkbox {
    display: flex;
    align-items: flex-start;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #4a5568;
    font-family: var(--font_family2);
}

.primary_checkbox input[type="checkbox"] {
    display: none;
}

.primary_checkbox .checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    margin-right: 12px;
    position: relative;
    transition: all 0.3s ease;
    flex-shrink: 0;
    margin-top: 2px;
}

.primary_checkbox input[type="checkbox"]:checked + .checkmark {
    background: #667eea;
    border-color: #667eea;
}

.primary_checkbox input[type="checkbox"]:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.theme_btn {
    width: 100%;
    padding: 16px 24px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white !important;
    border: none;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
}

.theme_btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.theme_btn.disable_btn {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Banner Section */
.signin-banner-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 40px;
    position: relative;
    overflow: hidden;
}

.signin-banner-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.banner-content {
    text-align: center;
    color: white;
    position: relative;
    z-index: 1;
}

.banner-title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 20px;
    line-height: 1.3;
    font-family: var(--font_family1);
}

.banner-slogans {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 30px;
}

.slogan {
    font-size: 16px;
    font-weight: 500;
    opacity: 0.9;
    font-family: var(--font_family2);
}

.banner-image {
    margin-top: 30px;
}

.banner-image img {
    max-width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .signin-container {
        grid-template-columns: 1fr;
        max-width: 500px;
    }
    
    .signin-banner-section {
        display: none;
    }
    
    .signin-form-section {
        padding: 40px 30px;
    }
}

@media (max-width: 768px) {
    .modern-signin-wrapper {
        padding: 10px;
    }
    
    .signin-form-section {
        padding: 30px 20px;
    }
    
    .welcome-title {
        font-size: 24px;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .demo-buttons {
        flex-direction: column;
        align-items: center;
    }
}

@media (max-width: 480px) {
    .signin-form-section {
        padding: 20px 15px;
    }
    
    .welcome-title {
        font-size: 20px;
    }
    
    .form-input {
        padding: 14px 14px 14px 44px;
    }
    
    .input-icon {
        left: 12px;
        width: 18px;
        height: 18px;
    }
}
</style>

<script>
// Tab switching functionality
function switchTab(tabName) {
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    document.querySelector(`[data-tab="${tabName}"]`).classList.add('active');
    
    // Update tab content
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`${tabName}-tab`).classList.add('active');
    
    // Update page title and subtitle
    const pageTitle = document.getElementById('page-title');
    const pageSubtitle = document.getElementById('page-subtitle');
    
    if (tabName === 'login') {
        pageTitle.textContent = 'Welcome back';
        pageSubtitle.textContent = 'Please sign in to your account';
        document.title = 'Log In - ' + document.title.split(' - ')[0];
    } else {
        pageTitle.textContent = 'Create Account';
        pageSubtitle.textContent = 'Join our learning community';
        document.title = 'Sign Up - ' + document.title.split(' - ')[0];
    }
    
    // Clear error messages
    hideErrors();
}

// Password toggle functionality
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const eyeIcon = passwordInput.parentElement.querySelector('.eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
    }
}

// Error handling functions
function showErrors(errors) {
    const errorContainer = document.getElementById('error-container');
    const errorMessages = document.getElementById('error-messages');
    
    // Clear any existing success messages
    errorMessages.innerHTML = '';
    
    let errorHtml = '<ul class="mb-0">';
    for (const [field, messages] of Object.entries(errors)) {
        if (Array.isArray(messages)) {
            messages.forEach(message => {
                errorHtml += `<li>${message}</li>`;
            });
        } else {
            errorHtml += `<li>${messages}</li>`;
        }
    }
    errorHtml += '</ul>';
    
    errorMessages.innerHTML = errorHtml;
    errorContainer.className = 'error-container';
    errorContainer.style.display = 'block';
}

function hideErrors() {
    const errorContainer = document.getElementById('error-container');
    errorContainer.style.display = 'none';
    errorContainer.className = 'error-container';
    
    // Don't hide success messages when hiding errors
}

// Form submission handling
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('.submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnIcon = submitBtn.querySelector('.btn-icon');
    const originalText = btnText.textContent;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    btnText.textContent = '{{__("common.Signing In...")}}';
    if (btnIcon) btnIcon.style.display = 'none';
    
    // Clear any previous errors
    hideErrors();
    
    fetch('{{route("login")}}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            // Default redirect if none provided
            window.location.href = '{{url("/")}}';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (error.errors) {
            showErrors(error.errors);
        } else {
            showErrors({general: [error.message || 'An error occurred. Please try again.']});
        }
        submitBtn.disabled = false;
        btnText.textContent = originalText;
        if (btnIcon) btnIcon.style.display = 'block';
    });
});

// Registration form handling
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> {{__("common.Registering...")}}';
    
    // Clear any previous messages
    const successContainer = document.getElementById('success-container');
    const errorContainer = document.getElementById('error-container');
    successContainer.style.display = 'none';
    errorContainer.style.display = 'none';
    
    fetch('{{route("signin.register")}}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message in green
            const successHtml = `
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-3" style="font-size: 1.5rem;"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Registration Successful!</h5>
                        <p class="mb-0">${data.message || 'A verification link has been sent to your email address. Please check your inbox and click the link to verify your account.'}</p>
                    </div>
                </div>
            `;
            
            successContainer.innerHTML = successHtml;
            successContainer.style.display = 'block';
            
            // Reset form
            this.reset();
            
            // Re-enable the button with original text
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            // Auto-scroll to show the success message
            successContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            
            // Remove any existing login form errors when registration is successful
            const loginForm = document.getElementById('loginForm');
            if (loginForm) {
                loginForm.reset();
            }
        }
    })
    .catch(error => {
        console.error('Registration error:', error);
        
        // Re-enable the button with original text
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        
        // Show error in the error container
        if (error.errors) {
            showErrors(error.errors);
        } else {
            showErrors({general: [error.message || 'An error occurred during registration. Please try again.']});
        }
        
        // Scroll to show the error message
        errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});

// Checkbox handling for registration form
document.getElementById('checkbox').addEventListener('change', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (this.checked) {
        submitBtn.classList.remove('disable_btn');
        submitBtn.removeAttribute('disabled');
    } else {
        submitBtn.classList.add('disable_btn');
        submitBtn.setAttribute('disabled', 'disabled');
    }
});

// Google reCAPTCHA callbacks
function onLoginSubmit(token) {
    document.getElementById("loginForm").submit();
}

function onRegisterSubmit(token) {
    document.getElementById("registerForm").dispatchEvent(new Event('submit'));
}

// Initialize page title
document.addEventListener('DOMContentLoaded', function() {
    // Set initial page title
    document.title = 'Log In - ' + document.title.split(' - ')[0];
});
</script>

@if(saasEnv('NOCAPTCHA_FOR_LOGIN')=='true' && saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

@if(saasEnv('NOCAPTCHA_FOR_REG')=='true' && saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif 