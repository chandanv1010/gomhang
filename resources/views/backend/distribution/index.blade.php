@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <h5>{{ $config['seo']['index']['table'] }} </h5>
                    @include('backend.dashboard.component.toolbox', ['model' => $config['model']])
                </div>
            </div>
            <div class="ibox-content">
                <form action="{{ route('distribution.index') }}">
                    <div class="filter-wrapper">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="perpage">
                                @php
                                    $perpage = request('perpage') ?: old('perpage');
                                    $provinceId = request('province_id') ?: old('province_id');
                                @endphp
                                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                    <select name="perpage" class="form-control input-sm perpage filter mr10">
                                        @for($i = 20; $i<= 200; $i+=20)
                                        <option {{ ($perpage == $i)  ? 'selected' : '' }}  value="{{ $i }}">{{ $i }} bản ghi</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="action">
                                <div class="uk-flex uk-flex-middle">
                                    <select name="province_id" class="form-control select2 mr10 filter">
                                        <option value="0">Chọn Miền (Phía Bắc / Phía Nam)</option>
                                        @if(isset($regions))
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}" {{ $provinceId == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    @include('backend.dashboard.component.filterPublish')
                                    @include('backend.dashboard.component.keyword')
                                    <a href="{{ route('distribution.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm mới nhà phân phối</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mt20">
                        <thead>
                        <tr>
                            <th style="width:50px;">
                                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                            </th>
                            <th>Hình ảnh</th>
                            <th>Tên nhà phân phối</th>
                            <th>Số điện thoại</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th>Miền</th>
                            <th>Tỉnh / Thành phố</th>
                            <th class="text-center" style="width:100px;">Tình Trạng</th>
                            <th class="text-center" style="width:120px;">Thao tác</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(isset($distributions) && is_object($distributions))
                                @foreach($distributions as $distributor)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{ $distributor->id }}" class="input-checkbox checkBoxItem">
                                    </td>
                                    <td class="text-center">
                                        <img src="{{ $distributor->image ?: 'backend/img/image.svg' }}" alt="" style="width:50px;height:50px;object-fit:cover;">
                                    </td>
                                    <td>
                                        <span class="text-success">{{ $distributor->name }}</span>
                                    </td>
                                    <td>{{ $distributor->phone }}</td>
                                    <td>{{ $distributor->email }}</td>
                                    <td>{{ $distributor->address }}</td>
                                    <td>{{ $distributor->region->name ?? '' }}</td>
                                    <td>{{ $distributor->area->name ?? '' }}</td>
                                    <td class="text-center js-switch-{{ $distributor->id }}"> 
                                        <input type="checkbox" value="{{ $distributor->publish }}" class="js-switch status " data-field="publish" data-model="{{ $config['model'] }}" {{ ($distributor->publish == 2) ? 'checked' : '' }} data-modelId="{{ $distributor->id }}" />
                                    </td>
                                    <td class="text-center"> 
                                        <a href="{{ route('distribution.edit', $distributor->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                        <a href="{{ route('distribution.delete', $distributor->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                {{ $distributions->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
