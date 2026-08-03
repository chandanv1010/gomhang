@props(['name', 'image', 'created', 'canonical', 'description'])
<article {{ $attributes->merge(['class' => 'tp_article__left-image']) }}>
    <a href="{{ $canonical }}" class="image img-cover img-zoomin"><img src="{{ $image }}" alt="{{ $name }}"></a>
    <div class="info">
        <h4 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h4>
        <div class="description">{!! $description !!}</div>
        <div class="created_at">{{ $created }}</div>
    </div>
</article>