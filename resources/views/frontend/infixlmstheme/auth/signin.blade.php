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
                    <h1 class="welcome-title">{{__('Welcome back')}}</h1>
                    <p class="welcome-subtitle">{{__('Please sign in to your account')}}</p>
                </div>
            </div>

            <!-- Social Login Buttons -->
            @if(saasEnv('ALLOW_FACEBOOK_LOGIN')=='true' || saasEnv('ALLOW_GOOGLE_LOGIN')=='true')
            <div class="social-login-section">
                @if(saasEnv('ALLOW_FACEBOOK_LOGIN')=='true')
                <a href="{{ route('social.oauth', 'facebook') }}" class="social-btn facebook-btn">
                    <svg class="social-icon" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                                         <span>{{__('Continue with Facebook')}}</span>
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
                                         <span>{{__('Continue with Google')}}</span>
                </a>
                @endif

                                 <div class="divider">
                     <span class="divider-text">{{__('or')}}</span>
                 </div>
            </div>
            @endif

            <!-- Login Form -->
            <form action="{{route('login')}}" method="POST" id="loginForm" class="signin-form">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">{{__('common.Email Address')}}</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                        </svg>
                        <input type="email" 
                               id="email"
                               name="email" 
                               value="{{old('email')}}"
                               class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                               placeholder="{{__('common.Enter your email')}}"
                               required>
                    </div>
                    @if($errors->first('email'))
                        <span class="error-message">{{$errors->first('email')}}</span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">{{__('common.Password')}}</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                        <input type="password" 
                               id="password"
                               name="password" 
                               class="form-input {{ $errors->has('password') ? 'error' : '' }}"
                               placeholder="{{__('common.Enter your password')}}"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg class="eye-icon" viewBox="0 0 24 24">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                            </svg>
                        </button>
                    </div>
                    @if($errors->first('password'))
                        <span class="error-message">{{$errors->first('password')}}</span>
                    @endif
                </div>

                <!-- Captcha -->
                @if(saasEnv('NOCAPTCHA_FOR_LOGIN')=='true')
                <div class="form-group">
                    @if(saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
                        {!! NoCaptcha::display(["data-size"=>"invisible"]) !!}
                    @else
                        {!! NoCaptcha::display() !!}
                    @endif
                    @if ($errors->has('g-recaptcha-response'))
                        <span class="error-message">{{$errors->first('g-recaptcha-response')}}</span>
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
                                data-callback="onSubmit">
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

            <!-- Register Link -->
            @if(Settings('student_reg')==1 && saasPlanCheck('student')==false)
            <div class="register-section">
                                 <p class="register-text">
                     {{__("Don't have an account")}}? 
                     <a href="{{route('register')}}" class="register-link">{{__('common.Register')}}</a>
                 </p>
            </div>
            @endif

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

.form-input.error {
    border-color: #e53e3e;
}

.error-message {
    display: block;
    color: #e53e3e;
    font-size: 14px;
    margin-top: 6px;
    font-family: var(--font_family2);
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

.register-section {
    text-align: center;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
}

.register-text {
    font-size: 14px;
    color: #718096;
    margin: 0;
    font-family: var(--font_family2);
}

.register-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.register-link:hover {
    color: #764ba2;
    text-decoration: underline;
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

.signin-banner-section {
    background: linear-gradient(135deg, var(--system_primery_color) 0%, var(--system_primery_gredient1) 100%);
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
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.eye-icon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.innerHTML = '<path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>';
    } else {
        passwordInput.type = 'password';
        eyeIcon.innerHTML = '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>';
    }
}

// Form submission handling
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const submitBtn = this.querySelector('.submit-btn');
    const btnText = submitBtn.querySelector('.btn-text');
    const btnIcon = submitBtn.querySelector('.btn-icon');
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    btnText.textContent = '{{__("common.Signing In...")}}';
    btnIcon.style.display = 'none';
});

// Google reCAPTCHA callback
function onSubmit(token) {
    document.getElementById("loginForm").submit();
}
</script>

@if(saasEnv('NOCAPTCHA_FOR_LOGIN')=='true' && saasEnv('NOCAPTCHA_IS_INVISIBLE')=="true")
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif
@endsection 