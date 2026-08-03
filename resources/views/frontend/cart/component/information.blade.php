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
                placeholder="Nhập vào địa chỉ: ví dụ đường Lạc Long Quân..."
                class="input-text"
            >
        </div>
        {{-- <div class="uk-grid uk-grid-medium mb20">
            <div class="uk-width-large-1-3">
                <select readonly disabled name="province_id" id="" class="province city location setupSelect2" data-target="districts">
                    <option value="0">[Chọn Thành Phố]</option>
                    @foreach($provinces as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select readonly disabled name="district_id" id="" class="setupSelect2 district location" data-target="wards">
                    <option value="0">Chọn Quận Huyện</option>
                </select>
            </div>
            <div class="uk-width-large-1-3">
                <select readonly disabled name="ward_id" id="" class="setupSelect2 ward">
                    <option value="0">Chọn Phường Xã</option>
                </select>
            </div>
        </div> --}}
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
    var province_id = '{{ (isset($customerAuth->province_id)) ? $customerAuth->province_id : old('province_id') }}'
    var district_id = '{{ (isset($customerAuth->district_id)) ? $customerAuth->district_id : old('district_id') }}'
    var ward_id = '{{ (isset($customerAuth->ward_id)) ? $customerAuth->ward_id : old('ward_id') }}'
</script>

