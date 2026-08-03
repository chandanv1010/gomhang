@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('distribution.store') : route('distribution.update', $distribution->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tên nhà phân phối <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        value="{{ old('name', ($distribution->name) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Số điện thoại <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="phone"
                                        value="{{ old('phone', ($distribution->phone) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Email</label>
                                    <input 
                                        type="text"
                                        name="email"
                                        value="{{ old('email', ($distribution->email) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Địa chỉ <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="address"
                                        value="{{ old('address', ($distribution->address) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Miền <span class="text-danger">(*)</span></label>
                                    <select name="province_id" id="province_id" class="form-control select2">
                                        <option value="0">Chọn Miền (Phía Bắc / Phía Nam)</option>
                                        @if(isset($regions))
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}" {{ old('province_id', ($distribution->province_id) ?? 0) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Tỉnh / Thành phố <span class="text-danger">(*)</span></label>
                                    <select name="district_id" id="district_id" class="form-control select2">
                                        <option value="0">Chọn Tỉnh / Thành phố</option>
                                        @if(isset($areas))
                                            @foreach($areas as $area)
                                                <option value="{{ $area->id }}" {{ old('district_id', ($distribution->district_id) ?? 0) == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Mã nhúng bản đồ Google Map (Thẻ iframe) <span class="text-danger">(*)</span></label>
                                    <textarea 
                                        name="map" 
                                        class="form-control" 
                                        rows="5"
                                        placeholder='Ví dụ: <iframe src="https://www.google.com/maps/embed?..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'
                                    >{{ old('map', ($distribution->map) ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left mb10">Hình ảnh</label>
                                    <span 
                                        class="image img-cover image-target" 
                                        style="
                                            height:200px;
                                            padding: 20px;
                                            text-align: center;
                                            border: 1px dashed #b8b2b2;
                                            display:flex;
                                            align-items: center;
                                            justify-content:center;
                                            cursor: pointer;
                                        "
                                    >
                                        <img src="{{ (old('image', ($distribution->image) ?? '' ) ? old('image', ($distribution->image) ?? '')   :  'backend/img/image.svg') }}" alt="" style="width:100px;height:100px;object-fit:contain;">
                                    </span>
                                    <input type="hidden" name="image" value="{{ old('image', ($distribution->image) ?? '' ) }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left mb10">Trạng thái hiển thị</label>
                                    <select name="publish" class="form-control">
                                        <option value="2" {{ old('publish', ($distribution->publish) ?? 2) == 2 ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="1" {{ old('publish', ($distribution->publish) ?? 2) == 1 ? 'selected' : '' }}>Ngừng hoạt động</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('backend.dashboard.component.button')
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        $(readyFunction);
        
        function readyFunction() {
            var initialLoad = true;
            
            $('#province_id').on('change', function() {
                var provinceId = $(this).val();
                var $districtSelect = $('#district_id');
                
                if (provinceId > 0) {
                    if (!initialLoad) {
                        $districtSelect.html('<option value="0">Đang tải...</option>');
                    }
                    $.ajax({
                        url: '{{ route("ajax.distribution.getArea") }}',
                        type: 'GET',
                        data: { parent_id: provinceId },
                        dataType: 'json',
                        success: function(res) {
                            var html = '<option value="0">Chọn Tỉnh / Thành phố</option>';
                            var selectedDistrict = '{{ old("district_id", ($distribution->district_id) ?? 0) }}';
                            $.each(res, function(key, area) {
                                var selected = (selectedDistrict == area.id) ? 'selected' : '';
                                html += '<option value="' + area.id + '" ' + selected + '>' + area.name + '</option>';
                            });
                            $districtSelect.html(html);
                            
                            // re-initialize select2 if needed
                            if ($districtSelect.hasClass('select2-hidden-accessible')) {
                                $districtSelect.select2('destroy');
                            }
                            $districtSelect.select2();
                            initialLoad = false;
                        },
                        error: function() {
                            $districtSelect.html('<option value="0">Lỗi khi tải dữ liệu</option>');
                            initialLoad = false;
                        }
                    });
                } else {
                    $districtSelect.html('<option value="0">Chọn Tỉnh / Thành phố</option>');
                    initialLoad = false;
                }
            });
            
            if ($('#province_id').val() > 0) {
                $('#province_id').trigger('change');
            } else {
                initialLoad = false;
            }
        }
    });
</script>
