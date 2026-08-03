@extends('frontend.homepage.layout')

@section('content')
<div class="profile-wrapper cat-bg">
    <div class="uk-container uk-container-center">

        {{-- HEADER PROFILE --}}
        @include('frontend.auth.customer.components.header')

        <div class="uk-grid uk-grid-medium mt30">

            {{-- SIDEBAR --}}
            <div class="uk-width-large-1-4">
                @include('frontend.auth.customer.components.sidebar')
            </div>

            {{-- MAIN CONTENT --}}
            <div class="uk-width-large-3-4">
                <div class="panel-profile">
                    <div class="panel-head">
                        <h2 class="heading-2"><span>Hồ sơ của tôi</span></h2>
                        <div class="description">
                            Quản lý thông tin tài khoản cá nhân của khách hàng
                        </div>
                    </div>

                    <div class="panel-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('customer.profile.update') }}" class="profile-form">
                        @csrf

                        {{-- Email static --}}
                        <div class="form-group">
                            <label>Tài khoản đăng nhập</label>
                            <div class="form-static">{{ $customer->email }}</div>
                        </div>

                        {{-- Họ tên --}}
                        <div class="form-group">
                            <label>Họ tên</label>
                            <input type="text" name="name"
                                class="input-text"
                                value="{{ old('name', $customer->name) }}">
                            @error('name')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email"
                                class="input-text"
                                disabled
                                value="{{ old('email', $customer->email) }}">
                            @error('email')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone"
                                class="input-text"
                                value="{{ old('phone', $customer->phone) }}">
                            @error('phone')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Địa chỉ --}}
                        <div class="form-group">
                            <label>Địa chỉ</label>
                            <input type="text" name="address"
                                class="input-text"
                                value="{{ old('address', $customer->address) }}">
                            @error('address')
                                <div class="field-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-save">Lưu thông tin</button>
                    </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    document.querySelector('.logout-btn').addEventListener('click', function() {
        if (confirm('Bạn có chắc chắn muốn đăng xuất không?')) {
            window.location.href = "{{ route('customer.logout') }}";
        }
    }); 
</script>