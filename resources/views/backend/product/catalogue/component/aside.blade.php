<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.parent') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="text-danger notice" >*{{ __('messages.parentNotice') }}</span>
                    <select name="parent_id" class="form-control setupSelect2" id="">
                        @foreach($dropdown as $key => $val)
                        <option {{ 
                            $key == old('parent_id', (isset($productCatalogue->parent_id)) ? $productCatalogue->parent_id : '') ? 'selected' : '' 
                            }} value="{{ $key }}">{{ $val }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>{{ __('messages.image') }}</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <span class="image img-cover image-target"><img src="{{ (old('image', ($productCatalogue->image) ?? '' ) ? old('image', ($productCatalogue->image) ?? '')   :  'backend/img/not-found.jpg') }}" alt=""></span>
                    <input type="hidden" name="image" value="{{ old('image', ($productCatalogue->image) ?? '' ) }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>Icon Danh mục</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <input 
                        type="text" 
                        name="icon" 
                        value="{{ old('icon', ($productCatalogue->icon) ?? '' ) }}"
                        class="upload-image form-control"
                    >
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>Màu nền (Background)</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <input 
                        type="color" 
                        name="background" 
                        value="{{ old('background', ($productCatalogue->background) ?? '#006D3A' ) }}"
                        class="form-control"
                        style="height: 40px; padding: 2px;"
                    >
                    <small class="text-muted mt5 d-block">Chọn màu nền hiển thị cho nhóm sản phẩm.</small>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="ibox w">
    <div class="ibox-title">
        <h5>Kiểu hiển thị ảnh</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="image_fit" class="form-control">
                        <option value="cover" {{ old('image_fit', ($productCatalogue->image_fit) ?? 'cover') == 'cover' ? 'selected' : '' }}>Cover (Phóng to vừa khung)</option>
                        <option value="contain" {{ old('image_fit', ($productCatalogue->image_fit) ?? 'cover') == 'contain' ? 'selected' : '' }}>Contain (Thu nhỏ gọn trong khung)</option>
                    </select>
                    <small class="text-muted mt5 d-block">Chọn kiểu hiển thị ảnh trong khung.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@include('backend.dashboard.component.publish', ['model' => ($productCatalogue) ?? null, 'hideImage' => true])
