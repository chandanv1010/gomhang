@if(isset($items) && count($items))
<ul class="uk-nav filter-list {{ isset($level) && $level > 0 ? 'child-catalogue-list' : '' }}" style="{{ isset($level) && $level > 0 ? 'padding-left: 15px;' : '' }}">
    @foreach($items as $node)
        @php
            $cat   = $node['item'];
            $name  = $cat->languages->first()->pivot->name ?? $cat->name;
            $id    = $cat->id;
        @endphp

        <li>
            <label>
                <input type="checkbox"
                       class="filterAttribute filtering"
                       value="{{ $id }}"
                       data-group="catalogue">
                {{ $name }}
            </label>

            {{-- Nếu có con thì render tiếp --}}
            @if(isset($node['children']) && count($node['children']))
                @include('frontend.product.catalogue.component.product-catalogue-tree', [
                    'items' => $node['children'],
                    'level' => ($level ?? 0) + 1
                ])
            @endif
        </li>
    @endforeach
</ul>
@endif