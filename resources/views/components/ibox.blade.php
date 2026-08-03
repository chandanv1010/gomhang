@props(['heading'])

<div {{ $attributes->merge(['class' => 'ibox w']) }}>
    <div class="ibox-title">
        <h5>{{ $heading }}</h5>
    </div>
    <div class="ibox-content">
        {{ $slot }}
    </div>
</div>
