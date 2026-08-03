<div class="ibox">
    <div class="ibox-title">
        <h5>Thông tin chung</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            @if(!isset($offTitle))
            <div class="col-lg-6">
                <div class="form-row">
                    <label for="" class="control-label text-left">Tên Voucher <span class="text-danger">(*)</span></label>
                    <input 
                        type="text"
                        name="name"
                        value="{{ old('name', ($model->name) ?? '' ) }}"
                        class="form-control"
                        placeholder="Nhập vào tên voucher"
                        autocomplete="off"
                    >
                </div>
            </div>
            @endif
            <div class="col-lg-6">
                <div class="form-row">
                    <label for="" class="control-label text-left">Mã Voucher <span class="text-danger"> (*)</span></label>
                    <input 
                        type="text"
                        name="code"
                        value="{{ old('code', ($model->code) ?? '' ) }}"
                        class="form-control"
                        placeholder="Nếu mã Voucher để trống hệ thống sẽ tự tạo"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-6">
                <div class="form-row">
                    <label for="" class="control-label text-left">Số lượng <span class="text-danger"> (*)</span></label>
                    <input 
                        type="text"
                        name="total_quantity"
                        value="{{ old('total_quantity', ($model->total_quantity) ?? '' ) }}"
                        class="form-control int"
                        placeholder=""
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-row">
                    <label for="" class="control-label text-left">Số lần sử dụng / User <span class="text-danger"> (*)</span></label>
                    <input 
                        type="text"
                        name="max_usage_per_user"
                        value="{{ old('max_usage_per_user', ($model->max_usage_per_user) ?? '' ) }}"
                        class="form-control int"
                        placeholder=""
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Mô tả voucher</label>
                    <textarea style="height:100px;padding:10px;" name="description" class="form-control form-textarea" placeholder="Nhập vào mô tả của voucher...">{{ old('description', ($model->description) ?? '' ) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>