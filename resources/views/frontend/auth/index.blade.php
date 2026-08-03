@extends('frontend.homepage.layout')

@section('content')

<div class="login-wrapper cat-bg">
    <div class="login-card">

        <h2 class="title">Đăng nhập tài khoản</h2>
        <p class="subtitle">Chào mừng khách hàng đã quay lại! Vui lòng điền thông tin bên dưới.</p>

        @if ($errors->has('login'))
            <div class="alert alert-error" style="color: #d10000; margin-bottom: 10px;">
                {{ $errors->first('login') }}
            </div>
        @endif
        <form action="{{ route('customer.dologin') }}" method="POST" id="loginForm">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 
                            2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </span>
                    <input type="text" name="email" value="{{ old('email') }}" placeholder="Nhập email của bạn">
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
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="10" width="16" height="12" rx="2" ry="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                        </svg>
                    </span>
                    <input type="password" name="password" placeholder="Nhập mật khẩu" >
                   
                </div>
                @if ($errors->has('password'))
                    <div class="field-error">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <div class="extra-line">
                <label class="remember">
                    <input type="checkbox" name="rememberMe"> Ghi nhớ đăng nhập
                </label>
                <a href="#" class="forgot">Quên mật khẩu?</a>
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
