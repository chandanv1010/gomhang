<div class="col-lg-4">
    <div class="ibox">
        <div class="ibox-title">
            <h5>Thời gian áp dụng Voucher</h5>
        </div>
        <div class="ibox-content">
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Ngày bắt đầu <span class="text-danger"> (*)</span></label>
                <div class="form-date">
                    <input 
                        type="text"
                        name="start_date"
                        value="{{ old('start_date', (isset($model->start_date)) ? convertDateTime($model->start_date) : null) }}"
                        class="form-control datepicker"
                        placeholder=""
                        autocomplete="off"
                    >
                    <span><i class="fa fa-calendar"></i></span>
                </div>
            </div>
            <div class="form-row mb15">
                <label for="" class="control-label text-left">Ngày kết thúc <span class="text-danger"> (*)</span></label>
                <div class="form-date">
                    <input 
                        type="text"
                        name="end_date"
                        value="{{ 
                            old('end_date', (isset($model->end_date)) ? convertDateTime($model->end_date) : null ) 
                        }}"
                        class="form-control datepicker"
                        placeholder=""
                        autocomplete="off"
                        @if((old('neverEndDate', ($model->neverEndDate ?? null)) == 'accept'))
                            disabled
                        @endif
                    >
                    <span><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>
    </div>
    @php
        $combine_promotion = \App\Enums\VoucherEnum::COMBINE_PROMOTION;
    @endphp
    <div class="ibox">
        <div class="ibox-content combine">
            <input 
                id="{{ $combine_promotion }}" 
                value="{{ $combine_promotion }}" 
                type="checkbox" 
                name="combine_promotion"
                {{ isset($voucher) && $voucher->combine_promotion == $combine_promotion ? 'checked' : null }}
            >
            <label for="{{ $combine_promotion }}">Áp dụng chung với chương trình khuyến mại</label>
        </div>
    </div>
</div>
<input 
    type="hidden" 
    class="input-product-and-quantity" 
    value="{{ json_encode(__('module.item_voucher')) }}"
>
<input 
    type="hidden" 
    class="conditionItemSelected" 
    value="[]"
>