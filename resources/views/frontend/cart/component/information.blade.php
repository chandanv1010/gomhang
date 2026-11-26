<div class="panel-head">
    <div class="uk-flex uk-flex-middle uk-flex-space-between">
        <h2 class="cart-heading">
            <span>Thông tin giao hàng</span>
        </h2>
    </div>
    
</div>
<div class="panel-body mb30">
    <div class="cart-information">
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input 
                        type="text"
                        name="fullname"
                        value="{{ old('fullname', $customerAuth->name ?? '') }}"
                        placeholder="Nhập vào Họ Tên"
                        class="input-text"
                    >
                </div>
            </div>
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <input 
                        type="text"
                        name="phone"
                        value="{{ old('phone', $customerAuth->phone ?? '') }}"
                        placeholder="Nhập vào Số điện thoại"
                        class="input-text"
                    >
                </div>
            </div>
        </div>
        <div class="form-row mb20">
            <input 
                type="text"
                name="address"
                value="{{ old('address', $customerAuth->address ?? '') }}"
                placeholder="Số nhà, tên đường (ví dụ: 226 Quang Trung)"
                class="input-text"
            >
        </div>
        {{-- Dia gioi sau sap nhap (01/07/2025): chi con Tinh/Thanh pho -> Phuong/Xa. --}}
        <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <select name="province_id" class="input-text cart-province" required>
                        <option value="">[Chọn Tỉnh/Thành phố]</option>
                        @foreach($provinces as $city)
                            <option value="{{ $city->code }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="uk-width-large-1-2">
                <div class="form-row">
                    <select name="ward_id" class="input-text cart-ward" required>
                        <option value="">[Chọn Phường/Xã]</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-row">
            <input 
                type="text"
                name="description"
                value="{{ old('description') }}"
                placeholder="Ghi chú thêm (Ví dụ: Giao hàng vào lúc 3 giờ chiều)"
                class="input-text"
            >
        </div>
    </div>
</div>

<script>
    (function () {
        var provinceId = @json((string) old('province_id', $customerAuth->province_id ?? ''));
        var wardId = @json((string) old('ward_id', $customerAuth->ward_id ?? ''));
        var provinceSelect = document.querySelector('.cart-province');
        var wardSelect = document.querySelector('.cart-ward');
        if (!provinceSelect || !wardSelect) return;

        function loadWards(code, selected) {
            wardSelect.innerHTML = '<option value="">[Chọn Phường/Xã]</option>';
            if (!code) return;
            wardSelect.disabled = true;
            var url = @json(url('ajax/location/getLocation')) + '?target=wards&data%5Blocation_id%5D=' + encodeURIComponent(code);
            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    wardSelect.innerHTML = res.html;
                    if (selected) wardSelect.value = selected;
                })
                .catch(function () {})
                .finally(function () { wardSelect.disabled = false; });
        }

        provinceSelect.addEventListener('change', function () { loadWards(this.value, ''); });

        if (provinceId) {
            provinceSelect.value = provinceId;
            if (provinceSelect.value === provinceId) loadWards(provinceId, wardId);
        }
    })();
</script>
