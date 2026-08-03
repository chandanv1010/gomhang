@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('school.store') : route('school.update', $school->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                @php
                    $translation = (isset($school)) ? $school->languages->first()->pivot : null;
                @endphp
                <x-backend.content
                    :name="$translation?->name"
                    description="{!! $translation?->description !!}"
                    content="{!! $translation?->content !!}"
                />
                <x-ibox heading="Thông tin trường">
                    <div class="row">
                        <div class="column">
                            <table class="info-table">
                                <tr>
                                    <td>Tiếng Việt</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[name_vi]" 
                                            class="form-control"
                                            placeholder="Ví dụ : Đại học Thanh Hoa"
                                            value="{{ old('information.name_vi', $school->information['name_vi'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tiếng Anh</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[name_en]" 
                                            placeholder="Ví dụ : Tsinghua University"
                                            class="form-control"
                                            value="{{ old('information.name_en', $school->information['name_en'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tiếng Trung</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[name_cn]" 
                                            placeholder="Ví dụ : 清华大学"
                                            class="form-control"
                                            value="{{ old('information.name_cn', $school->information['name_cn'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Loại hình</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[type]" 
                                            placeholder="Ví dụ : Trường Công lập"
                                            class="form-control"
                                            value="{{ old('information.type', $school->information['type'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Năm thành lập</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[founded_year]" 
                                            class="form-control"
                                            value="{{ old('information.founded_year', $school->information['founded_year'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Cơ sở trường</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[campuses]" 
                                            class="form-control"
                                            value="{{ old('information.campuses', $school->information['campuses'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Khu vực</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[area]" 
                                            placeholder="Ví dụ : Hoa Bắc"
                                            class="form-control"
                                            value="{{ old('information.area', $school->information['area'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tỉnh</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[province]" 
                                            class="form-control"
                                            value="{{ old('information.province', $school->information['province'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thành phố</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[city]" 
                                            placeholder="Ví dụ : Bắc Kinh"
                                            class="form-control"
                                            value="{{ old('information.city', $school->information['city'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Xếp hạng quốc gia</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[national_rank]" 
                                            class="form-control"
                                            value="{{ old('information.national_rank', $school->information['national_rank'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Học phí 1 năm tiếng</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[language_fee]" 
                                            class="form-control"
                                            value="{{ old('information.language_fee', $school->information['language_fee'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="column">
                            <table class="info-table">
                                <tr>
                                    <td>Cấp thành phố</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[city_level]" 
                                            class="form-control"
                                            value="{{ old('information.city_level', $school->information['city_level'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Xếp hạng thế giới</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[world_rank]" 
                                            class="form-control"
                                            value="{{ old('information.world_rank', $school->information['world_rank'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Học phí hệ Đại học (Tệ/năm)</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[bachelor_fee]" 
                                            class="form-control"
                                            value="{{ old('information.bachelor_fee', $school->information['bachelor_fee'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Quy mô thành phố</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[city_scale]" 
                                            class="form-control"
                                            value="{{ old('information.city_scale', $school->information['city_scale'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Số lượng sinh viên</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[total_students]" 
                                            class="form-control"
                                            value="{{ old('information.total_students', $school->information['total_students'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Học phí hệ Thạc sĩ (Tệ/năm)</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[master_fee]" 
                                            class="form-control"
                                            value="{{ old('information.master_fee', $school->information['master_fee'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Xếp loại thành phố</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[city_rank]" 
                                            class="form-control"
                                            value="{{ old('information.city_rank', $school->information['city_rank'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Số lượng sinh viên quốc tế</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[international_students]" 
                                            class="form-control"
                                            value="{{ old('information.international_students', $school->information['international_students'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Học phí hệ Tiến sĩ (Tệ/năm)</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[phd_fee]" 
                                            class="form-control"
                                            value="{{ old('information.phd_fee', $school->information['phd_fee'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Diện tích (m2)</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[acreage]" 
                                            class="form-control"
                                            value="{{ old('information.acreage', $school->information['acreage'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Số lượng giảng viên</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[faculty_count]" 
                                            class="form-control"
                                            value="{{ old('information.faculty_count', $school->information['faculty_count'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="column">
                            <table class="info-table">
                                <tr>
                                    <td>Phí ký túc xá (Tệ/tháng)</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[dormitory_fee]" 
                                            class="form-control"
                                            value="{{ old('information.dormitory_fee', $school->information['dormitory_fee'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sách thư viện</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[library_books]" 
                                            class="form-control"
                                            value="{{ old('information.library_books', $school->information['library_books'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Số lượng chuyên ngành</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[majors_count]" 
                                            class="form-control"
                                            value="{{ old('information.majors_count', $school->information['majors_count'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sinh hoạt phí (Tệ/tháng)</td>
                                    <td>
                                        <input 
                                            type="text" 
                                            name="information[living_fee]" 
                                            class="form-control"
                                            value="{{ old('information.living_fee', $school->information['living_fee'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Phòng thí nghiệm</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[labs_count]" 
                                            class="form-control"
                                            value="{{ old('information.labs_count', $school->information['labs_count'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Số lượng ngành học</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[programs_count]" 
                                            class="form-control"
                                            value="{{ old('information.programs_count', $school->information['programs_count'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Ngành trọng điểm</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[key_subjects]" 
                                            class="form-control"
                                            value="{{ old('information.key_subjects', $school->information['key_subjects'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Số nhà ăn</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[canteens]" 
                                            class="form-control"
                                            value="{{ old('information.canteens', $school->information['canteens'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Chuyên ngành tiến sĩ</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[phd_programs]" 
                                            class="form-control"
                                            value="{{ old('information.phd_programs', $school->information['phd_programs'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Số loại học bổng</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[scholarship_types]" 
                                            class="form-control"
                                            value="{{ old('information.scholarship_types', $school->information['scholarship_types'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Chuyên ngành thạc sĩ</td>
                                    <td>
                                        <input 
                                            type="number" 
                                            name="information[master_programs]" 
                                            class="form-control"
                                            value="{{ old('information.master_programs', $school->information['master_programs'] ?? '') }}"
                                        >
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </x-ibox>
                <x-backend.training-major
                    :model="$school ?? null"
                />
                <x-backend.question-answer
                    :model="$school ?? null"
                />
                <x-backend.album 
                    :model="$school ?? null"
                />
                <x-backend.seo 
                    :meta_title="$translation?->meta_title"
                    :meta_keyword="$translation?->meta_keyword"
                    :meta_description="$translation?->meta_description"
                    :canonical="$translation?->canonical"
                />
            </div>
            <div class="col-lg-3">
                <x-ibox heading="Thông tin trường">
                    <div class="form-row mb10">
                        <span class="text-danger notice">Mã trường</span>
                        <input 
                            type="text" 
                            name="code" 
                            class="form-control" 
                            placeholder="Nhập mã trường"
                            value="{{ old('rank', $school->code ?? '') }}"
                        >
                    </div>
                    <div class="form-row mb10">
                        <span class="text-danger notice">Xếp hạng</span>
                        <input 
                            type="number" 
                            min="1" 
                            max="1000" 
                            step="1" 
                            name="rank" 
                            class="form-control" 
                            placeholder="Nhập xếp hạng"
                            value="{{ old('rank', $school->rank ?? '') }}"
                        >
                    </div>
                    <x-backend.select2
                        :options="$schoolCatalogues"
                        heading="Chọn loại hình trường"
                        name="school_catalogues"
                        :selectedValue="$school->school_catalogues[0]->id ?? 0"
                        class="mb10"
                    />
                    <x-backend.select2
                        :options="$areas"
                        heading="Chọn khu vực"
                        name="area_id"
                        :selectedValue="$school->area_id ?? 0"
                        class="mb10"
                    />
                    @php
                        $school_project_ids = isset($school) ? $school->school_projects->pluck('id')->toArray() : null;
                    @endphp
                    <x-backend.select2
                        :options="$projects"
                        heading="Chọn dự án"
                        name="school_projects"
                        :selectedValue="$school_project_ids ?? []"
                        multiple
                        class="mb10"
                    />
                    @php
                        $school_scholar_ids = isset($school) ? $school->school_scholars->pluck('id')->toArray() : null;
                    @endphp
                    <x-backend.select2
                        :options="$scholars"
                        heading="Chọn học bổng"
                        name="school_scholars"
                        :selectedValue="$school_scholar_ids ?? []"
                        multiple
                        class="mb10"
                    />
                    @php
                        $school_post_ids = isset($school) ? $school->school_posts->pluck('id')->toArray() : null;
                    @endphp
                    <x-backend.select2
                        :options="$posts"
                        heading="Chọn bài viết liên quan"
                        name="school_posts"
                        :selectedValue="$school_post_ids ?? []"
                        multiple
                        class="mb10"
                    />
                    <div class="form-row mb10">
                        <label for="" class="uk-flex uk-flex-space-between">
                            <span class="text-danger notice" style="margin-bottom: 0;">Toàn cảnh trường</span>
                        </label>
                        <textarea name="panorama" id="" cols="30" rows="10" class="form-control">{{ old('panorama', $school->panorama ?? '') }}</textarea>
                    </div>
                </x-ibox>
                <x-ibox heading="Thông tin liên hệ">
                    <div class="form-row mb10">
                        <span class="text-danger notice">Email</span>
                        <input 
                            type="text" 
                            name="email" 
                            class="form-control" 
                            placeholder="Nhập email"
                            value="{{ old('email', $school->email ?? '') }}"
                        >
                    </div>
                    <div class="form-row mb10">
                        <span class="text-danger notice">Số điện thoaị</span>
                        <input 
                            type="text" 
                            name="phone" 
                            class="form-control" 
                            placeholder="Nhập số điện thoại"
                            value="{{ old('phone', $school->phone ?? '') }}"
                        >
                    </div>
                    <div class="form-row mb10">
                        <span class="text-danger notice">Địa chỉ</span>
                        <input 
                            type="text" 
                            name="address" 
                            class="form-control" 
                            placeholder="Nhập địa chỉ"
                            value="{{ old('address', $school->address ?? '') }}"
                        >
                    </div>
                    <div class="form-row mb10">
                        <span class="text-danger notice">Website</span>
                        <input 
                            type="text" 
                            name="link_website" 
                            class="form-control" 
                            placeholder="Nhập địa chỉ website"
                            value="{{ old('link_website', $school->link_website ?? '') }}"
                        >
                    </div>
                    <div class="form-row mb10">
                        <label for="" class="uk-flex uk-flex-space-between">
                            <span class="text-danger notice" style="margin-bottom: 0;">Iframe bản đồ</span>
                            <a class="system-link" target="_blank" href="https://manhan.vn/hoc-website-nang-cao/huong-dan-nhung-ban-do-vao-website/">Hướng dẫn thiết lập bản đồ</a>
                        </label>
                        <textarea name="map" id="" cols="30" rows="10" class="form-control">{{ old('map', $school->map ?? '') }}</textarea>
                    </div>
                </x-ibox>
                <x-ibox heading="Logo">
                    <x-backend.image-preview 
                        name="logo"
                        :value="$school->logo ?? ''"
                    />
                </x-ibox>
                <x-ibox heading="Ảnh đại diện">
                    <x-backend.image-preview 
                        name="image"
                        :value="$school->image ?? ''"
                    />
                </x-ibox>
                <x-ibox heading="Video">
                    <label for="" class="uk-flex uk-flex-space-between">
                        <a class="system-link" target="_blank" href="">Lưu ý mỗi Iframe của video cách nhau 1 dấu phẩy và xuống dòng</a>
                    </label>
                    <textarea name="video" id="" cols="30" rows="10" class="form-control">{{ old('video', $school->video ?? '') }}</textarea>
                </x-ibox>
                <x-ibox heading="Cấu hình nâng cao">
                    <x-backend.select2 
                        :options="__('messages.publish')"
                        name="publish"
                        :selectedValue="$school->publish ?? 0"
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
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }
    .info-table td {
        padding: 8px;
        border: 1px solid #dee2e6;
    }
    .info-table tr:hover {
        background-color: #f9f9f9;
    }
    .column {
        width: 33.33%;
        float: left;
        padding: 0 10px;
        box-sizing: border-box;
    }
</style>