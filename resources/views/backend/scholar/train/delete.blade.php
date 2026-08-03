@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<x-backend.delete 
    :model="$train"
    submitRoute="scholar.train.destroy"
/>