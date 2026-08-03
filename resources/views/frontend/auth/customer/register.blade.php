@extends('frontend.homepage.layout')

@section('content')

<div class="login-wrapper cat-bg">
    <div class="login-card">

        <h2 class="title">Tạo tài khoản mới</h2>
        <p class="subtitle">Vui lòng điền đầy đủ thông tin bên dưới để đăng ký.</p>

        {{-- Lỗi chung --}}
        @if ($errors->has('register'))
            <div class="alert alert-error" style="color:#d10000; margin-bottom: 10px;">
                {{ $errors->first('register') }}
            </div>
        @endif

        <form action="{{ route('customer.doregister') }}" method="POST">
            @csrf

            {{-- NAME --}}
            <div class="form-group">
                <label>Họ tên</label>
                <div class="input-group">
                    <span class="icon">
                        {{-- Có thể dùng icon user --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"/>
                            <path d="M4 20a8 8 0 0 1 16 0"/>
                        </svg>
                    </span>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Nhập họ tên">
                </div>
                @error('name')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- EMAIL --}}
            <div class="form-group">
                <label>Email</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16a2 2 0 0 1 2 2v12a2 
                            2 0 0 1-2 2H4a2 
                            2 0 0 1-2-2V6a2 
                            2 0 0 1 2-2z" />
                            <polyline points="22,6 12,13 2,6" />
                        </svg>
                    </span>
                    <input type="text" name="email" value="{{ old('email') }}" placeholder="Nhập email">
                </div>
                @error('email')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- PHONE --}}
            <div class="form-group">
                <label>Số điện thoại</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 
                            2 0 0 1-2.18 
                            2 19.79 19.79 0 0 1-8.63-3.07 19.5 
                            19.5 0 0 1-6-6 
                            19.79 19.79 0 0 1-3.07-8.67A2 
                            2 0 0 1 4.11 2h3a2 
                            2 0 0 1 2 1.72c.12.83.37 1.63.72 
                            2.39a2 2 0 0 1-.45 2.18l-1.27 
                            1.27a16 16 0 0 0 6 
                            6l1.27-1.27a2 
                            2 0 0 1 2.18-.45c.76.35 1.56.6 
                            2.39.72A2 
                            2 0 0 1 22 16.92z" />
                        </svg>
                    </span>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại">
                </div>
                @error('phone')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- ADDRESS --}}
            <div class="form-group">
                <label>Địa chỉ</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 12-9 12S3 
                            17 3 10a9 
                            9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </span>
                    <input type="text" name="address" value="{{ old('address') }}" placeholder="Nhập địa chỉ">
                </div>
                @error('address')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- PASSWORD --}}
            <div class="form-group">
                <label>Mật khẩu</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="10" width="16" height="12" rx="2" ry="2"/>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                        </svg>
                    </span>
                    <input type="password" name="password" placeholder="Nhập mật khẩu">
                </div>
                @error('password')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- CONFIRM PASSWORD --}}
            <div class="form-group">
                <label>Xác nhận mật khẩu</label>
                <div class="input-group">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                            viewBox="0 0 24 24" fill="none" stroke="#8B0023" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20c4.418 0 8-3.582 
                            8-8s-3.582-8-8-8-8 
                            3.582-8 8 3.582 8 8 8zm0 
                            0l4-4-2-2-2 2-1-1"/>
                        </svg>
                    </span>
                    <input type="password" name="re_password" placeholder="Nhập lại mật khẩu">
                </div>
                @error('re_password')
                <div class="field-error">{{ $message }}</div>
                @enderror
                
            </div>

            <button type="submit" class="login-btn">
                Đăng ký
            </button>

            <div class="register-text">
                Đã có tài khoản? <a href="{{ route('customer.login') }}">Đăng nhập ngay</a>
            </div>

        </form>

    </div>
</div>

@endsection
