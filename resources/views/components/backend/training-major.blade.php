<div class="ibox ibox-train">
    <div class="ibox-title">
        <div>
            <h5>Chuyên ngành đào tạo</h5>
        </div>
    </div>
    <div class="ibox-content">
        <div class="variant-foot mt10">
            <button type="button" class="add-train">Thêm hệ đào tạo</button>
        </div>
        <div class="program-content mt20">
            @php
                $training_majors = old('training_major', $model->training_major ?? []);
            @endphp
            @if(isset($training_majors) && is_array($training_majors) && count($training_majors))
                @foreach($training_majors as $trainIndex => $train)
                    <div class="ibox mt20 train-wrapper" data-train-index="{{ $trainIndex }}">
                        <div class="ibox-title">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                <input type="text" 
                                    name="training_major[{{ $trainIndex }}][train_name]" 
                                    class="form-control" 
                                    value="{{ $train['train_name'] ?? '' }}" 
                                    placeholder="Nhập hệ đào tạo" style="width:75%;">
                                <div class="train-action">
                                    <button type="button" class="add-major-item mr10">+Thêm ngành đào tạo</button>
                                    <button type="button" class="remove-train-item">Xóa hệ đào tạo</button>
                                </div>
                            </div>
                        </div>
                        <div class="ibox-content">
                            @if(!empty($train['major']))
                                @foreach($train['major'] as $majorIndex => $major)
                                    <div class="train-item" data-major-index="{{ $majorIndex }}">
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <div class="train-content">
                                                    <div class="title mb10">
                                                        <input type="text" class="form-control"
                                                            name="training_major[{{ $trainIndex }}][major][{{ $majorIndex }}][code]"
                                                            value="{{ $major['code'] ?? '' }}"
                                                            placeholder="Nhập vào mã ngành">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5">
                                                <div class="train-info">
                                                    <div class="mb10">
                                                        <input type="text" class="form-control"
                                                            name="training_major[{{ $trainIndex }}][major][{{ $majorIndex }}][name]"
                                                            value="{{ $major['name'] ?? '' }}"
                                                            placeholder="Nhập vào tên ngành"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="train-info">
                                                    <div class="mb10">
                                                        <input type="text" class="form-control"
                                                            name="training_major[{{ $trainIndex }}][major][{{ $majorIndex }}][grade]"
                                                            value="{{ $major['grade'] ?? '' }}"
                                                            placeholder="Nhập vào xếp loại"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2">
                                                <div class="train-info">
                                                    <div class="mb10">
                                                        <input type="text" class="form-control"
                                                            name="training_major[{{ $trainIndex }}][major][{{ $majorIndex }}][rank]"
                                                            value="{{ $major['rank'] ?? '' }}"
                                                            placeholder="Nhập vào xếp hạng"
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-1">
                                                <button type="button" class="form-control btn btn-danger remove-major">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>