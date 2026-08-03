@props(['name', 'canonical', 'description', 'image', 'created'])


<div {{ $attributes->merge(['class' => 'feature-post']) }}>
    <a href="" class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></a>
    <div class="info">
        <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
        <div class="created_at">{{ $created }}</div>
        <div class="description">
            {!! strip_tags($description) !!}
        </div>
    </div>
</div>