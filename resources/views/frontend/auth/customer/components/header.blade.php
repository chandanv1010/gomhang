<div class="profile-header">
    <div class="avatar-block">
        <div class="avatar-wrapper">
            <img id="avatarPreview"
                src="{{ $customer->image ?? asset('frontend/resources/img/default-avatar.png') }}" 
                class="avatar-img" alt="Avatar">

            <button type="button" class="avatar-overlay" id="btnChangeAvatar">
                <i class="fa fa-camera mr5"></i> Thay đổi ảnh
            </button>

            <input type="file" id="avatarInput" accept="image/*" hidden>
        </div>
    </div>

    <div class="info-block">
        <h2 class="name">{{ $customer->name }}</h2>
        <p class="email">{{ $customer->email }}</p>

        <div class="points-box">
            <span class="label">Tổng điểm tích luỹ:</span>
            <span class="points">{{ number_format($customer->point ?? 0) }}</span>
        </div>
    </div>
</div>
<div class="avatar-error alert alert-danger hidden"></div>