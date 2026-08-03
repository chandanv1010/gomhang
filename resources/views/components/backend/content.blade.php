@props(['name', 'description', 'content', 'offContent' => false, 'disabled' => false])

<div class="ibox">
    <div class="ibox-title">
        <h5>Thông tin chung</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Tiêu đề<span class="text-danger">(*)</span></label>
                    <input 
                        type="text"
                        name="name"
                        value="{{ old('name', ($name) ?? '' ) }}"
                        class="form-control change-title"
                        data-flag = "{{ (isset($name)) ? 1 : 0 }}"
                        placeholder=""
                        autocomplete="off"
                        {{ (isset($disabled) && $disabled === true) ? 'disabled' : '' }}
                    >
                </div>
            </div>
        </div>
        <div class="row mb30">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Mô tả ngắn </label>
                    <textarea 
                        name="description" 
                        class="ck-editor" 
                        id="ckDescription"
                        {{ (isset($disabled) && $disabled === true) ? 'disabled' : '' }}
                        data-height="100">{{ old('description', ($description) ?? '') }}</textarea>
                </div>
            </div>
        </div>
        @if(!$offContent)
            <div class="row mb15">
                <div class="col-lg-12">
                    <div class="form-row">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <label for="" class="control-label text-left">Nội dung</label>
                            <a href="" class="multipleUploadImageCkeditor" data-target="ckContent">{{ __('messages.upload') }}</a>
                        </div>
                        <textarea
                            name="content"
                            class="form-control ck-editor"
                            placeholder=""
                            autocomplete="off"
                            id="ckContent"
                            data-height="500"
                            {{ (isset($disabled) && $disabled === true) ? 'disabled' : '' }}
                        >{{ old('content', ($content) ?? '' ) }}</textarea>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
