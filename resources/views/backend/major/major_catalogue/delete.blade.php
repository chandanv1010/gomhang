@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<x-backend.delete
    :model="$majorCatalogue"
    submitRoute="major_catalogue.destroy"
/>