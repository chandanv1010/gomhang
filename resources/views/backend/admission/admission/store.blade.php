@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('admission.store') : route('admission.update', $admission->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                @php
                    $translation = (isset($admission)) ? $admission->languages->first()->pivot : null;
                @endphp
                <x-backend.content
                    :name="$translation?->name"
                    description="{!! $translation?->description !!}"
                    content="{!! $translation?->content !!}"
                />
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Thông tin tuyển sinh</h5>
                    </div>
                    <div class="ibox-content">
                        <table class="form-table">
                            <thead>
                                <tr>
                                    <th>Mùa học bổng</th>
                                    <th>Thời gian tuyển sinh</th>
                                    <th>Hạn chót nộp đơn</th>
                                    <th>Vị trí</th>
                                    <th>Phí nộp đơn</th>
                                    <th>Chế độ giáo dục</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="admissions_info[season]" 
                                            class="form-control" 
                                            placeholder="Ví dụ : 2024" 
                                            value="{{ old('admissions_info.season', $admission->admissions_info['season'] ?? '') }}"
                                        >
                                    </td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="admissions_info[admission_time]" 
                                            class="form-control" 
                                            placeholder="Ví dụ : tháng 9" 
                                            value="{{ old('admissions_info.admission_time', $admission->admissions_info['admission_time'] ?? '') }}"
                                        >
                                    </td>
                                    <td>
                                        <input 
                                            type="date" 
                                            name="admissions_info[apply_deadline]" 
                                            value="{{ old('admissions_info.apply_deadline', isset($admission) ? date('Y-m-d', strtotime($admission->admissions_info['apply_deadline'])) : '') }}" 
                                            class="form-control" 
                                            placeholder="" 
                                            autocomplete="off"
                                        >
                                    </td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="admissions_info[position]" 
                                            class="form-control" 
                                            placeholder="Ví dụ : sinh viên" 
                                            value="{{ old('admissions_info.position', $admission->admissions_info['position'] ?? '') }}"
                                        >
                                    </td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="admissions_info[application_fee]" 
                                            class="form-control int" 
                                            placeholder="" 
                                            value="{{ old('admissions_info.application_fee', $admission->admissions_info['application_fee'] ?? '') }}"
                                        >
                                    </td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="admissions_info[education_mode]" 
                                            class="form-control" 
                                            placeholder="Ví dụ : ngoại tuyến" 
                                            value="{{ old('admissions_info.education_mode', $admission->admissions_info['education_mode'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <x-backend.album 
                    :model="$admission ?? null"
                />
                <x-backend.seo 
                    :meta_title="$translation?->meta_title"
                    :meta_keyword="$translation?->meta_keyword"
                    :meta_description="$translation?->meta_description"
                    :canonical="$translation?->canonical"
                />
            </div>
            <div class="col-lg-3">
                <x-ibox heading="Thông tin tuyển sinh">
                    <x-backend.select2
                        :options="$dropdown"
                        heading="Chọn danh mục cha"
                        name="admission_catalogue_id"
                        :selectedValue="$admission->admission_catalogue_id ?? 0"
                        class="mb10"
                    />
                    @php
                        $admission_school_ids = isset($admission) ? $admission->admission_schools->pluck('id')->toArray() : null;
                    @endphp
                    <x-backend.select2
                        :options="$schools"
                        heading="Chọn trường"
                        name="admission_schools"
                        :selectedValue="$admission_school_ids ?? []"
                        multiple
                        class="mb10"
                    />
                    <x-backend.select2
                        :options="$scholars"
                        heading="Chọn học bổng"
                        name="scholar_id"
                        :selectedValue="$admission->scholar_id ?? 0"
                        class="mb10"
                    />
                    @php
                        $admission_train_ids = isset($admission) ? $admission->admission_trains->pluck('id')->toArray() : null;
                    @endphp
                    <x-backend.select2
                        :options="$trains"
                        :heading="__('messages.train')"
                        name="admission_trains"
                        :selectedValue="$admission_train_ids ?? []"
                        multiple
                    />
                </x-ibox>
                <x-ibox heading="Ảnh đại diện">
                    <x-backend.image-preview 
                        name="image"
                        :value="$admission->image ?? ''"
                    />
                </x-ibox>
                <x-ibox heading="Cấu hình nâng cao">
                    <x-backend.select2 
                        :options="__('messages.publish')"
                        name="publish"
                        :selectedValue="$admission->publish ?? 0"
                        class="mb10"
                    />
                </x-ibox>
            </div>
        </div>
        <div class="text-right mb15 fixed-bottom">
            <button class="btn btn-primary" type="submit" name="send" value="send_and_stay">{{ __('messages.save') }}</button>
            <button class="btn btn-success" type="submit" name="send" value="send_and_exit">Đóng</button>
        </div>
    </div>
</form>
<style>
    .form-table {
        width: 100%;
        border-collapse: collapse;
    }
    .form-table thead th {
        background-color: #f8f9fa;
        padding: 15px 10px;
        text-align: center;
        font-weight: 500;
        color: #495057;
        border: 1px solid #dee2e6;
        vertical-align: top;
    }
    .form-table tbody td {
        padding: 10px;
        text-align: center;
        border: 1px solid #dee2e6;
        vertical-align: top;
    }
</style>