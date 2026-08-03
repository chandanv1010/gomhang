@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('major_catalogue.store') : route('major_catalogue.update', $majorCatalogue->id);
@endphp
<form action="{{ $url }}" method="post" class="box">
    @csrf
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-9">
                @php
                    $translation = (isset($majorCatalogue)) ? $majorCatalogue->languages->first()->pivot : null;
                @endphp
                <x-backend.content 
                    :name="$translation?->name"
                    description="{!! $translation?->description !!}"
                    content="{!! $translation?->content !!}"
                />
                <x-backend.album 
                    :model="$majorCatalogue ?? null"
                />

                <x-backend.seo 
                    :meta_title="$translation?->meta_title"
                    :meta_keyword="$translation?->meta_keyword"
                    :meta_description="$translation?->meta_description"
                    :canonical="$translation?->canonical"
                />
            </div>
            <div class="col-lg-3">
                 <x-ibox heading="Thông tin ngành">
                    <x-backend.select2 
                        :options="$majorGroups"
                        :heading="'Chọn nhóm ngành'"
                        name="major_group_id"
                        :selectedValue="$majorCatalogue->major_group_id ?? 0"
                        class="mb10"
                    />
                    <x-backend.select2 
                        :options="$dropdown"
                        :heading="__('messages.parentNotice')"
                        name="parent_id"
                        :selectedValue="$majorCatalogue->parent_id ?? 0"
                        class="mb10"
                    />
                    <div class="form-row">
                        <span class="text-danger notice">Nhập mã ngành</span>
                        <input 
                            type="text" 
                            name="code" 
                            class="form-control" 
                            placeholder="Nhập mã ngành ví dụ : 0101" 
                            value="{{ old('code', $majorCatalogue->code ?? '' ) }}"
                        >
                    </div>
                </x-ibox>
                <x-ibox heading="Ảnh đại diện">
                    <x-backend.image-preview 
                        name="image"
                        :value="$majorCatalogue->image ?? ''"
                    />
                </x-ibox>

                <x-ibox heading="Cấu hình nâng cao">
                    <x-backend.select2 
                        :options="__('messages.publish')"
                        name="publish"
                        :selectedValue="$majorCatalogue->publish ?? 0"
                        class="mb10"
                    />
                    <x-backend.select2 
                        :options="__('messages.follow')"
                        name="follow"
                        :selectedValue="$majorCatalogue->follow ?? 0"
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
