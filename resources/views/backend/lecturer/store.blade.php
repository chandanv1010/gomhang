@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('lecturer.store') : route('lecturer.update', $lecturer->id);
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
                                    <label for="" class="control-label text-left">Họ Tên <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        value="{{ old('name', ($lecturer->name) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Chức Vụ <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="position"
                                        value="{{ old('position', ($lecturer->position) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">{{ __('messages.description') }} </label>
                                    <textarea 
                                        name="description" 
                                        class="ck-editor" 
                                        id="ckDescription"
                                        data-height="200">{{ old('description', ($lecturer->description) ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-row">
                                    <label for="" class="control-label text-left mb10">Chọn ảnh đại diện</label>
                                    <span 
                                        class="image img-cover image-target" 
                                        style="
                                            height:250px;
                                            padding: 20px;
                                            text-align: center;
                                            border: 1px dashed #b8b2b2;
                                            display:flex;
                                            align-items: center;
                                            justify-content:center;
                                        "
                                    >
                                        <img src="{{ (old('image', ($lecturer->image) ?? '' ) ? old('image', ($lecturer->image) ?? '')   :  'backend/img/image.svg') }}" alt="" style="width:110px;height:110px;">
                                    </span>
                                    <input type="hidden" name="image" value="{{ old('image', ($lecturer->image) ?? '' ) }}">
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