@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<x-backend.delete
    :model="$majorGroup"
    submitRoute="major_group.destroy"
/>