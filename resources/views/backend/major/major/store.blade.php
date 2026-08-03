@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('major.store') : route('major.update', $major->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                @php
                    $translation = (isset($major)) ? $major->languages->first()->pivot : null;
                @endphp
                <x-backend.content
                    :name="$translation?->name"
                    description="{!! $translation?->description !!}"
                    content="{!! $translation?->content !!}"
                />
                <x-backend.album 
                    :model="$major ?? null"
                />
                <x-backend.seo 
                    :meta_title="$translation?->meta_title"
                    :meta_keyword="$translation?->meta_keyword"
                    :meta_description="$translation?->meta_description"
                    :canonical="$translation?->canonical"
                />
            </div>
            <div class="col-lg-3">
                <x-ibox heading="Thông tin chuyên ngành">
                    <div class="form-row mb10">
                        <span class="text-danger notice">Tổng số apply</span>
                        <input 
                            type="number" 
                            name="total_applications" 
                            class="form-control" 
                            placeholder="Nhập tổng số apply"
                            value="{{ old('total_applications', $major->total_applications ?? '') }}"
                        >
                    </div>
                    <x-backend.select2
                        :options="$dropdown"
                        heading="Chọn ngành"
                        name="major_catalogue_id"
                        :selectedValue="$major->major_catalogues[0]->id ?? 0"
                        class="mb10"
                    />
                    <x-backend.select2
                        :options="$trains"
                        heading="Chọn hệ đào tạo"
                        name="train_id"
                        :selectedValue="$major->train_id ?? 0"
                    />
                </x-ibox>

                <x-ibox heading="Ảnh đại diện">
                    <x-backend.image-preview 
                        name="image"
                        :value="$major->image ?? ''"
                    />
                </x-ibox>

                <x-ibox heading="Cấu hình nâng cao">
                    <x-backend.select2 
                        :options="__('messages.publish')"
                        name="publish"
                        :selectedValue="$major->publish ?? 0"
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
