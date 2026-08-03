@include('backend.dashboard.component.breadcrumb', ['title' => $config['seo']['delete']['title']])
<x-backend.delete
    :model="$schoolCatalogue"
    submitRoute="school_catalogue.destroy"
/>