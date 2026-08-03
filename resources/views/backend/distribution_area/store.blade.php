@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['create']['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('distribution.area.store') : route('distribution.area.update', $area->id);
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
                                    <label for="" class="control-label text-left">Tên Khu Vực <span class="text-danger">(*)</span></label>
                                    <input 
                                        type="text"
                                        name="name"
                                        value="{{ old('name', ($area->name) ?? '' ) }}"
                                        class="form-control"
                                        placeholder=""
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-row">
                                    <label for="" class="control-label text-left">Khu Vực Cha <span class="text-danger">(*)</span></label>
                                    <select name="parent_id" class="form-control select2">
                                        <option value="0" {{ old('parent_id', ($area->parent_id) ?? 0) == 0 ? 'selected' : '' }}>Là danh mục cha (Miền Bắc / Miền Nam)</option>
                                        @if(isset($parents))
                                            @foreach($parents as $parent)
                                                <option value="{{ $parent->id }}" {{ old('parent_id', ($area->parent_id) ?? 0) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
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
                                    <label for="" class="control-label text-left mb10">Trạng thái hiển thị</label>
                                    <select name="publish" class="form-control">
                                        <option value="2" {{ old('publish', ($area->publish) ?? 2) == 2 ? 'selected' : '' }}>Hoạt động</option>
                                        <option value="1" {{ old('publish', ($area->publish) ?? 2) == 1 ? 'selected' : '' }}>Ngừng hoạt động</option>
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
