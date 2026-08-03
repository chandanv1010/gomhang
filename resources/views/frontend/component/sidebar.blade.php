<!-- ASIDE PRODUCT -->
<aside class="aside">
    {{-- DANH MỤC SẢN PHẨM --}}
    @php
        $category = \App\Models\ProductCatalogue::where('publish', 2)
            ->orderByDesc('order')
            ->orderByDesc('id')
            ->with(['languages'])
            ->get();

        $category = recursive($category);
        
        // dd($category);
    @endphp

    @if(!empty($category))
        @foreach($category as $val)
        @php
            $catName = $val['item']->languages->first()->pivot->name;
        @endphp
            <section class="aside-category aside-panel">
                <div class="aside-heading"><span>{{ $catName }}</span></div>
                <div class="aside-body">
                    @if(!empty($val['children']))
                        <ul class="uk-list uk-clearfix list-cat">
                            @foreach($val['children'] as $valChildren)
                                @php
                                    $name = $valChildren['item']->languages->first()->pivot->name;
                                    $canonical = write_url($valChildren['item']->languages->first()->pivot->canonical);
                                @endphp
                                <li>
                                    <a href="{{ $canonical }}" title="{{ $name }}" class="uk-position-relative">
                                        <span>{{ $name }}</span>
                                        @if(!empty($valChildren['children']))
                                            <span class="btn_dropdown_sp">
                                                <i class="fa fa-plus" aria-hidden="true"></i>
                                            </span>
                                        @endif
                                    </a>

                                    @if(!empty($valChildren['children']))
                                        <ul class="uk-clearfix children" style="display:none">
                                            @foreach($valChildren['children'] as $valS)
                                             @php
                                                $nameS = $valS['item']->languages->first()->pivot->name;
                                                $canonicalS = write_url($valS['item']->languages->first()->pivot->canonical);
                                            @endphp
                                                <li>
                                                    <a href="{{ $canonicalS }}" title="{{ $nameS }}">{{ $nameS }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </section>
        @endforeach
    @endif
    <div class="support">
        <div class="images">
            <img src="https://vinahome.vn/template/frontend/images/suport.png" alt="">
        </div>
        <div class="hotline">
            <div class="icon">
                <img src="https://vinahome.vn/template/frontend/images/icon-holine.jpg" alt="">
            </div>
            <div class="nav-icon">
                <span class="sp1">Hotline</span>
                <span class="sp2">0968643487</span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</aside>

{{-- SCRIPT DROPDOWN --}}
<script>
	$(document).ready(function() {
		$(document).on('click', '.btn_dropdown_sp', function(event) {
			event.preventDefault();
			$(this).toggleClass('active');
			$(this).closest('li').find('.children').slideToggle();
			return false;
		});
	});		
</script>    
