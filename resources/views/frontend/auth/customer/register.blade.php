@extends('frontend.homepage.layout')

@section('content')

<div class="login-wrapper">
    <div class="login-card login-card--register">

        <h2 class="title">Tạo tài khoản mới</h2>
        <p class="subtitle">Vui lòng điền đầy đủ thông tin bên dưới để đăng ký.</p>

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->has('register'))
            <div class="alert alert-error">{{ $errors->first('register') }}</div>
        @endif

        <form action="{{ route('customer.doregister') }}" method="POST">
            @csrf

            {{-- Two columns on desktop, one on mobile - six stacked fields made for
                 a long scroll before. --}}
            <div class="form-grid">

                {{-- NAME --}}
                <div class="form-group">
                    <label for="reg-name">Họ tên</label>
                    <div class="input-group">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="#e02b1d" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z"/>
                                <path d="M4 20a8 8 0 0 1 16 0"/>
                            </svg>
                        </span>
                        <input id="reg-name" type="text" name="name" value="{{ old('name') }}"
                            autocomplete="name" placeholder="Nhập họ tên">
                    </div>
                    @error('name')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- PHONE --}}
                <div class="form-group">
                    <label for="reg-phone">Số điện thoại</label>
                    <div class="input-group">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="#e02b1d" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.83.37 1.63.72 2.39a2 2 0 0 1-.45 2.18l-1.27 1.27a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.18-.45c.76.35 1.56.6 2.39.72A2 2 0 0 1 22 16.92z"/>
                            </svg>
                        </span>
                        <input id="reg-phone" type="tel" name="phone" value="{{ old('phone') }}"
                            autocomplete="tel" inputmode="numeric" placeholder="Nhập số điện thoại">
                    </div>
                    @error('phone')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- EMAIL --}}
                <div class="form-group form-grid--full">
                    <label for="reg-email">Email</label>
                    <div class="input-group">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="#e02b1d" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input id="reg-email" type="email" name="email" value="{{ old('email') }}"
                            autocomplete="email" placeholder="Nhập email">
                    </div>
                    @error('email')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- ADDRESS --}}
                <div class="form-group form-grid--full">
                    <label for="reg-address">Địa chỉ</label>
                    <div class="input-group">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="#e02b1d" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                        </span>
                        <input id="reg-address" type="text" name="address" value="{{ old('address') }}"
                            autocomplete="street-address" placeholder="Nhập địa chỉ">
                    </div>
                    @error('address')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- PASSWORD --}}
                <div class="form-group">
                    <label for="reg-password">Mật khẩu</label>
                    <div class="input-group">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="#e02b1d" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="10" width="16" height="12" rx="2" ry="2"/>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                            </svg>
                        </span>
                        <input id="reg-password" type="password" name="password"
                            autocomplete="new-password" placeholder="Nhập mật khẩu">
                    </div>
                    @error('password')<div class="field-error">{{ $message }}</div>@enderror
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div class="form-group">
                    <label for="reg-re-password">Xác nhận mật khẩu</label>
                    <div class="input-group">
                        <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 24 24" fill="none" stroke="#e02b1d" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 20c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zm0 0l4-4-2-2-2 2-1-1"/>
                            </svg>
                        </span>
                        <input id="reg-re-password" type="password" name="re_password"
                            autocomplete="new-password" placeholder="Nhập lại mật khẩu">
                    </div>
                    @error('re_password')<div class="field-error">{{ $message }}</div>@enderror
                </div>

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
