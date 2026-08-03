
@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['index']['title']])
<div class="row mt20">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>{{ $config['seo']['index']['table']; }} </h5>
                @include('backend.dashboard.component.toolbox', ['model' => $config['model']])
            </div>
            <div class="ibox-content">
                <x-backend.filter 
                    createRoute="school_catalogue.create"
                    submitRoute="school_catalogue.index"
                />
                <x-backend.customtable 
                    :records="$schoolCatalogues->getCollection()"
                    :columns="[
                        'name' => ['label' => 'Loại Hình Trường', '', 'render' => fn($item) => e(str_repeat('|----', (($item->level > 0)?($item->level - 1):0)).$item->languages->first()->pivot->name)],
                        'creator' => ['class' => 'text-center w-200px', 'label' => 'Người tạo', 'render' => fn($item) => $item->users->name],
                        'created_at' => ['class' => 'text-center w-180px', 'label' => 'Ngày tạo', 'render' => fn($item) => $item->created_at->format('d-m-Y')],
                        'updated_at' => ['class' => 'text-center w-180px', 'label' => 'Ngày Sửa', 'render' => fn($item) => $item->updated_at->format('d-m-Y')],
                    ]"
                    :actions="[
                        ['route' => 'school_catalogue.edit', 'class' => 'btn-success', 'icon' => 'fa-edit'],
                        ['route' => 'school_catalogue.delete', 'class' => 'btn-danger', 'icon' => 'fa-trash'],
                    ]"
                    :model="$config['model']"
                />
                {{ $schoolCatalogues->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
