@extends('frontend.homepage.layout')

@section('content')
<div class="profile-wrapper cat-bg">
    <div class="uk-container uk-container-center">

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
                        <h2 class="heading-2"><span>Thay đổi mật khẩu</span></h2>
                        <div class="description">
                            Cập nhật mật khẩu mới để bảo vệ tài khoản của bạn.
                        </div>
                    </div>

                    <div class="panel-body">

                        {{-- Success Message --}}
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        {{-- Error Message --}}
                        @if(session('error'))
                            <div class="alert alert-error">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('customer.password.update') }}" method="POST" class="profile-form">
                            @csrf

                            {{-- Mật khẩu hiện tại --}}
                            <div class="form-group">
                                <label>Mật khẩu hiện tại</label>
                                <input type="password" name="current_password" class="input-text">
                                @error('current_password')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Mật khẩu mới --}}
                            <div class="form-group">
                                <label>Mật khẩu mới</label>
                                <input type="password" name="password" class="input-text">
                                @error('password')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Nhập lại mật khẩu --}}
                            <div class="form-group">
                                <label>Nhập lại mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="input-text">
                                @error('password_confirmation')
                                    <div class="field-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn-save">Cập nhật mật khẩu</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
