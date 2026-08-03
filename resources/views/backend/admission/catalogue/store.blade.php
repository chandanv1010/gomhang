@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo'][$config['method']]['title']])
@include('backend.dashboard.component.formError')
@php
    $url = ($config['method'] == 'create') ? route('admission.catalogue.store') : route('admission.catalogue.update', $admission->id);
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
                <x-ibox heading="Chọn Danh mục cha">
                    <x-backend.select2
                        :options="$dropdown"
                        heading="Chọn danh mục cha"
                        name="parent_id"
                        :selectedValue="$admission->parent_id ?? 0"
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
