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
                <form action="{{ route('distribution.area.index') }}">
                    <div class="filter-wrapper">
                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                            <div class="perpage">
                                @php
                                    $perpage = request('perpage') ?: old('perpage');
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
                                    @include('backend.dashboard.component.filterPublish')
                                    @include('backend.dashboard.component.keyword')
                                    <a href="{{ route('distribution.area.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm mới khu vực</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                
                <table class="table table-striped table-bordered mt20">
                    <thead>
                    <tr>
                        <th style="width:50px;">
                            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
                        </th>
                        <th>Tên khu vực</th>
                        <th>Thuộc khu vực cha</th>
                        <th class="text-center" style="width:100px;">Tình Trạng</th>
                        <th class="text-center" style="width:120px;">Thao tác</th>
                    </tr>
                    </thead>
                    <tbody>
                        @if(isset($areas) && is_object($areas))
                            @foreach($areas as $area)
                            <tr>
                                <td>
                                    <input type="checkbox" value="{{ $area->id }}" class="input-checkbox checkBoxItem">
                                </td>
                                <td>
                                    <span class="text-success">{{ $area->name }}</span>
                                </td>
                                <td>
                                    {{ $area->parent->name ?? 'Cấp cha (Miền)' }}
                                </td>
                                <td class="text-center js-switch-{{ $area->id }}"> 
                                    <input type="checkbox" value="{{ $area->publish }}" class="js-switch status " data-field="publish" data-model="{{ $config['model'] }}" {{ ($area->publish == 2) ? 'checked' : '' }} data-modelId="{{ $area->id }}" />
                                </td>
                                <td class="text-center"> 
                                    <a href="{{ route('distribution.area.edit', $area->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                                    <a href="{{ route('distribution.area.delete', $area->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                {{ $areas->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
