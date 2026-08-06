@extends('frontend.homepage.layout')

@section('content')

<div class="login-wrapper">
    <div class="login-card">

        <h1 class="title">Đăng nhập tài khoản</h1>
        <p class="subtitle">Chào mừng khách hàng đã quay lại! Vui lòng điền thông tin bên dưới.</p>

        {{-- Why CustomerAuth sent the visitor here. Passed as a view variable
             because flash data set in that middleware does not survive the
             redirect in this app. --}}
        @if (!empty($authNotice))
            <div class="alert alert-error">{{ $authNotice }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->has('login'))
            <div class="alert alert-error">{{ $errors->first('login') }}</div>
        @endif
        <form action="{{ route('customer.dologin') }}" method="POST" id="loginForm">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#e01b24" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 
                            2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </span>
                    <input id="login-email" type="email" name="email" value="{{ old('email') }}"
                        autocomplete="email" placeholder="Nhập email của bạn">
                </div>
                @if ($errors->has('email'))
                    <div class="field-error">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label>Mật khẩu</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#e01b24" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="10" width="16" height="12" rx="2" ry="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                        </svg>
                    </span>
                    <input id="login-password" type="password" name="password"
                        autocomplete="current-password" placeholder="Nhập mật khẩu">
                </div>
                @if ($errors->has('password'))
                    <div class="field-error">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <div class="extra-line">
                <label class="remember">
                    <input type="checkbox" name="rememberMe" value="1"
                        @if(old('rememberMe')) checked @endif> Ghi nhớ đăng nhập
                </label>
                {{-- "Quên mật khẩu?" pointed at "#". AuthController has
                     forgotPassword()/updatePassword() but no route is registered for
                     them, and MAIL_* is commented out in .env, so there is nothing to
                     link to yet. Left out rather than shipping a dead link. --}}
            </div>

            <button type="submit" class="login-btn">
                Đăng nhập
            </button>

            <div class="register-text">
                Chưa có tài khoản? <a href="{{ route('customer.register') }}">Tạo tài khoản</a>
            </div>
        </form>

    </div>
</div>

@endsection
