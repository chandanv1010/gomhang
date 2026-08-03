<div class="ibox">
    <div class="ibox-title">
        <h5>Cài đặt thông tin chi tiết voucher</h5>
    </div>
    <div class="ibox-content">
        <div class="form-row voucher">
            <div class="fix-label" for="">Chọn hình thức áp dụng voucher</div>
            <select name="method" class="setupSelect2 promotionMethod " id="">
                <option value="none">Chọn hình thức</option>
                @foreach(__('module.voucher') as $key => $val)
                    <option value="{{ $key }}"> {{ $val }} </option>
                @endforeach
            </select>
        </div>
        <div class="promotion-container">
            
        </div>
    </div>
</div>