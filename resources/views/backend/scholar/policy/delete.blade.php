@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<x-backend.delete 
    :model="$policy"
    submitRoute="scholar.policy.destroy"
/>