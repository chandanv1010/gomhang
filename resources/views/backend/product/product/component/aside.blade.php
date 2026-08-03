<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="product_catalogue_id" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option
                                {{ $key == old('product_catalogue_id', isset($product->product_catalogue_id) ? $product->product_catalogue_id : '') ? 'selected' : '' }}
                                value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @php
            $catalogue = [];
            if (isset($product)) {
                foreach ($product->product_catalogues as $key => $val) {
                    $catalogue[] = $val->id;
                }
            }
        @endphp
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label">{{ __('messages.subparent') }}</label>
                    <select multiple name="catalogue[]" class="form-control setupSelect2" id="">
                        @foreach ($dropdown as $key => $val)
                            <option @if (is_array(old('catalogue', isset($catalogue) && count($catalogue) ? $catalogue : [])) &&
                                    isset($product->product_catalogue_id) &&
                                    $key !== $product->product_catalogue_id &&
                                    in_array($key, old('catalogue', isset($catalogue) ? $catalogue : []))) selected @endif value="{{ $key }}">
                                {{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.product.information') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.code') }}</label>
                    <input type="text" name="code" value="{{ old('code', $product->code ?? time()) }}"
                        class="form-control">
                </div>
            </div>
        </div>
        <div class="row mb15">
            {{-- <div class="col-lg-6">
                <div class="form-row">
                    <label for="" class="control-label text-left">Số lượng bài<span class="text-danger">(*)</span></label>
                    <input
                        type="text"
                        name="total_lesson"
                        value="{{ old('total_lesson', ($product->total_lesson) ?? '' ) }}"
                        class="form-control change-title int"
                        placeholder="VD: 23 bài"
                        autocomplete="off"
                    >
                </div>
            </div>
            <div class="col-lg-6 mb15">
                <div class="form-row">
                    <label for="" class="control-label text-left">Thời lượng<span class="text-danger">(*)</span></label>
                    <input
                        type="text"
                        name="duration"
                        value="{{ old('duration', ($product->duration) ?? '' ) }}"
                        class="form-control change-title"
                        placeholder="VD: 12 tiếng"
                        autocomplete="off"
                    >
                </div>
            </div> --}}
            <div class="col-lg-12 hidden">
                <div class="form-row">
                    <label for="" class="control-label text-left">Giảng viên<span
                            class="text-danger">(*)</span></label>
                    <select name="lecturer_id" class="form-control setupSelect2">
                        <option value="0">[Chọn Giảng Viên]</option>
                        @foreach ($lecturers as $key => $val)
                            <option
                                {{ $val->id == old('lecturer_id', isset($product->lecturer_id) ? $product->lecturer_id : '') ? 'selected' : '' }}
                                value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.made_in') }}</label>
                    <input type="text" name="made_in" value="{{ old('made_in', $product->made_in ?? null) }}"
                        class="form-control ">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">{{ __('messages.product.price') }}</label>
                    <input type="text" name="price"
                        value="{{ old('price', isset($product) ? number_format($product->price, 0, ',', '.') : '') }}"
                        class="form-control int">
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="">Tồn kho</label>
                    <input type="text" name="stock"
                        value="{{ old('stock', optional($product ?? null)->stock ?? 0) }}" class="form-control"
                        min="0">
                </div>
            </div>
        </div>
        <div class="form-row mb20 hidden">
            <label for="" class="control-label text-left">Thời gian BH</label>
            <div class="guarantee">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <input type="text" name="guarantee" value="{{ old('guarantee', $product->guarantee ?? null) }}"
                        class="text-right form-control int" placeholder="" autocomplete="off"
                        style="margin-right:10px;">
                    <select class="setupSelect2" name="" id="">
                        <option value="month">tháng</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-row mb15">
            <label for="">Mã Nhúng Video Demo</label>
            <textarea type="text" name="iframe" class="form-control" style="height:168px;"
                placeholder="Nhập mã nhúng iframe của video">{{ old('iframe', $product->iframe ?? '') }}</textarea>
        </div>
        <div class="form-row hidden">
            <label for="">Nội dung khóa học</label>
            <div class="text-danger" style="font-size:12px;font-style:italic">Mỗi nội dung thể hiện trên 1 dòng</div>
            <textarea type="text" name="lession_content" class="form-control" style="height:168px;">{{ old('lession_content', $product->lession_content ?? '') }}</textarea>
        </div>
    </div>
</div>


@include('backend.dashboard.component.publish', ['model' => $product ?? null, 'hideImage' => false])

@if (!empty($product->qrcode))
    <div class="ibox w">
        <div class="ibox-title">
            <h5>Mã QRCODE</h5>
        </div>
        <div class="ibox-content qrcode">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-row">
                        {!! $product->qrcode !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
