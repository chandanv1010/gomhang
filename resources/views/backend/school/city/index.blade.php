
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
                    createRoute="school.city.create"
                    submitRoute="school.city.index"
                />
                <x-backend.customtable 
                    :records="$records->getCollection()"
                    :columns="[
                        'name' => ['label' => 'Thành phố', 'render' => fn($item) => e($item->name)],
                        'creator' => ['label' => 'Người tạo', 'render' => fn($item) => $item->users->name],
                        'created_at' => ['label' => 'Ngày tạo', 'render' => fn($item) => $item->created_at->format('d-m-Y')],
                        'updated_at' => ['label' => 'Ngày Sửa', 'render' => fn($item) => $item->updated_at->format('d-m-Y')],
                    ]"
                    :actions="[
                        ['route' => 'school.city.edit', 'class' => 'btn-success', 'icon' => 'fa-edit'],
                        ['route' => 'school.city.delete', 'class' => 'btn-danger', 'icon' => 'fa-trash'],
                    ]"
                    :model="$config['model']"
                />
                {{ $records->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
