<?php

use App\Enums\PromotionEnum;

if(!function_exists('convertRevenueChartData')){
    function convertRevenueChartData($chartData, $data = 'monthly_revenue', $label = 'month' , $text = 'Tháng'){
        $newArray = [];
        if(!is_null($chartData) && count($chartData)){
            foreach($chartData as $key => $val){
                $newArray['data'][] = $val->{$data};
                $newArray['label'][] = $text.' '.$val->{$label};
            }
        }
        return $newArray;
    }
}


if(!function_exists('growHtml')){
    function growHtml($grow){
        if($grow > 0){
            return '<div class="stat-percent font-bold text-success">'.$grow.'% <i class="fa fa-level-up"></i></div>';
        }else{
            return '<div class="stat-percent font-bold text-danger">'.$grow.'% <i class="fa fa-level-down"></i></div>';
        }
    }
}

if(!function_exists('growth')){
    function growth($currentValue, $previousValue){
        $divison = ($previousValue == 0) ? 1 : $previousValue;
        $grow =  ($currentValue - $previousValue) / $divison * 100;
        return number_format($grow, 1);
    }
}

if(!function_exists('pre')){
    function pre($data = ''){
        echo '<pre>';
        print_r($data);
        echo '<pre>';
        die();
    }
}

if(!function_exists('image')){
    function image($image){
        
        if(empty($image)) {
            if (request()->segment(1) === 'admin' || request()->segment(1) === 'seller') {
                return 'backend/img/not-found.jpg';
            }
            return 'https://gomhang.vn/wp-content/uploads/2026/08/072dcae0-adf1-40fb-a738-6bb864078920-400x533.png';
        }

        $image = str_replace('/public/', '/', $image);

        return $image;
    }
}

if(!function_exists('system_brand')){
    /**
     * Brand name from the systems table. Views used to hardcode "Gomhang.vn".
     *
     * @param  array|null  $system  The $system array shared with frontend views.
     */
    function system_brand($system = null, string $fallback = 'Gomhang.vn'){
        $value = is_array($system) ? trim((string)($system['homepage_brand'] ?? '')) : '';

        return ($value !== '') ? $value : $fallback;
    }
}

if(!function_exists('system_website_url')){
    /** Site URL from the systems table, e.g. https://gomhang.vn */
    function system_website_url($system = null, string $fallback = ''){
        $value = is_array($system) ? trim((string)($system['homepage_website'] ?? '')) : '';

        return ($value !== '') ? $value : $fallback;
    }
}

if(!function_exists('system_website_label')){
    /**
     * Host only, for places that show the domain as a label - printing the whole
     * "https://..." inside a small button reads badly.
     */
    function system_website_label($system = null, string $fallback = 'Gomhang.vn'){
        $url = system_website_url($system);
        if($url === ''){
            return $fallback;
        }

        $host = parse_url($url, PHP_URL_HOST) ?: $url;

        return preg_replace('#^www\.#i', '', rtrim($host, '/'));
    }
}

if(!function_exists('system_phone')){
    /**
     * Best available contact number: the sales hotline if set, otherwise the
     * general phone. Digits only when $digitsOnly, for tel:/zalo.me links.
     */
    function system_phone($system = null, bool $digitsOnly = false){
        $candidates = ['contact_hotline', 'contact_phone', 'contact_contact_buy', 'contact_contact_support'];
        $value = '';
        foreach($candidates as $key){
            $candidate = is_array($system) ? trim((string)($system[$key] ?? '')) : '';
            if($candidate !== ''){
                $value = $candidate;
                break;
            }
        }

        return $digitsOnly ? preg_replace('/\D+/', '', $value) : $value;
    }
}

if(!function_exists('brand_logo')){
    /**
     * Resolve the logo of a brand attribute, or null when there is nothing to
     * show so the caller can fall back to a text badge.
     *
     * Order of preference:
     *   1. the image uploaded through the admin (attributes.image)
     *   2. the SVG bundled under public/frontend/images/brands/<canonical>.svg
     */
    function brand_logo($brand){
        $uploaded = is_object($brand) ? ($brand->image ?? null) : ($brand['image'] ?? null);
        if(!empty($uploaded)) {
            return str_replace('/public/', '/', $uploaded);
        }

        $canonical = is_object($brand) ? ($brand->canonical ?? null) : ($brand['canonical'] ?? null);
        if(empty($canonical)) {
            return null;
        }

        $relative = 'frontend/images/brands/' . $canonical . '.svg';

        return file_exists(public_path($relative)) ? $relative : null;
    }
}

if(!function_exists('getGiaoHangNhanhToken')){
    function getGiaoHangNhanhToken(){
       return '21e62550-a35f-11ef-a89d-dab02cbaab48';
    }
}


if(!function_exists('convert_price')){
    function convert_price(mixed $price = '', $flag = false){
        if($price === null) return 0;
        return ($flag === false) ? str_replace('.','', $price) : number_format($price, 0, ',', '.');
    }
}

if(!function_exists('getPercent')){
    function getPercent($product = null, $discountValue = 0){
        return ($product->price > 0) ? round($discountValue/$product->price*100) : 0;
    
    }
}

if(!function_exists('getPromotionPrice')){
    function getPromotionPrice($priceMain = 0, $discountValue = 0){
       

        return $priceMain - $discountValue;
    
    }
}


// if(!function_exists('getPrice')){
//     function getPrice($product = null){
//         $result = [
//             'price' => $product->price, 
//             'priceSale' => 0,
//             'percent' => 0, 
//             'html' => ''
//         ];

//         if($product->price == 0){

//             $result['html'] .= '<div class="price mt10">';
//                 $result['html'] .= '<div class="price-sale">Liên Hệ</div>';
//             $result['html'] .= '</div>';
//             return $result;
//         }

//         if(isset($product->promotions) && isset($product->promotions->discountType)){
//             $result['percent'] = getPercent($product, $product->promotions->discount);
//             if($product->promotions->discountValue > 0){
//                 $result['priceSale'] = getPromotionPrice($product->price, $product->promotions->discount);
//             }
//         }
//         $result['html'] .= '<div class="price uk-flex uk-flex-middle mt10">';
//             $result['html'] .= '<div class="price-sale">'.(($result['priceSale'] > 0) ? convert_price($result['priceSale'], true) : convert_price($result['price'], true) ).'<span class="currency">₫</span></div>';
//             if($result['priceSale'] > 0){
//                 $result['html'] .= '<div class="price-old uk-flex uk-flex-middle">'.convert_price($result['price'], true).'đ <div class="percent"><div class="percent-value">-'.$result['percent'].'%</div></div></div>';
                
//             }
//         $result['html'] .= '</div>';
//         return $result;
//     }
// }

if(!function_exists('getPrice')){
    /**
     * Price of a product for the cart, the compare page and the product card
     * components.
     *
     * This used to be a stub that returned zeros and the text "Liên Hệ". Because
     * CartService reads it when adding to the cart, every item was added at 0đ -
     * an order could be placed for nothing. It now shares one source of truth
     * with the pages (getProductPriceInfo), so what a shopper sees is what the
     * cart charges.
     *
     * Contract kept for existing callers: `priceSale` is 0 when there is no
     * discount, and they fall back to `price`. Read `hasPromotion` instead of
     * testing `priceSale > 0` if a 100%-off campaign has to be honoured.
     *
     * @return array{price: float, priceSale: float, percent: int, hasPromotion: bool, html: string}
     */
    function getPrice($product = null){
        if (is_null($product)) {
            return [
                'price' => 0.0,
                'priceSale' => 0.0,
                'percent' => 0,
                'hasPromotion' => false,
                'html' => '<div class="price mt10"><div class="price-sale">Liên Hệ</div></div>',
            ];
        }

        $info = getProductPriceInfo($product);
        $price = (float) $info['price'];
        $hasPromotion = (bool) $info['hasPromotion'];
        $priceSale = $hasPromotion ? (float) $info['priceSale'] : 0.0;
        $percent = (int) $info['percent'];

        if ($price <= 0) {
            $html = '<div class="price mt10"><div class="price-sale">Liên Hệ</div></div>';
        } elseif ($hasPromotion) {
            $html = '<div class="price mt10">'
                . '<span class="product-sale-price">' . convert_price($priceSale, true) . 'đ</span>'
                . '<span class="product-discount-badge">Giảm ' . $percent . '%</span>'
                . '<span class="product-old-price">' . convert_price($price, true) . 'đ</span>'
                . '</div>';
        } else {
            $html = '<div class="price mt10">'
                . '<span class="product-sale-price">' . convert_price($price, true) . 'đ</span>'
                . '</div>';
        }

        return [
            'price' => $price,
            'priceSale' => $priceSale,
            'percent' => $percent,
            'hasPromotion' => $hasPromotion,
            'html' => $html,
        ];
    }
}

if(!function_exists('getVariantPrice')){
    /**
     * Price of a specific product variant. Also previously a zero-returning stub,
     * so variant products went into the cart free of charge too.
     *
     * @param  object|null  $variant           Row from product_variants (has its own price)
     * @param  object|null  $variantPromotion  Row from PromotionRepository::findPromotionByVariantUuid()
     * @return array{price: float, priceSale: float, percent: int, hasPromotion: bool, html: string}
     */
    function getVariantPrice($variant = null, $variantPromotion = null){
        $price = (float) ($variant->price ?? 0);
        $discount = (float) ($variantPromotion->discount ?? 0);

        // A discount can never exceed the price.
        $discount = max(0.0, min($discount, $price));
        $hasPromotion = ($price > 0 && $discount > 0);

        $priceSale = $hasPromotion ? max(0.0, $price - $discount) : 0.0;
        $percent = $hasPromotion ? (int) round(($discount / $price) * 100) : 0;

        if ($price <= 0) {
            $html = '<div class="price mt10"><div class="price-sale">Liên Hệ</div></div>';
        } elseif ($hasPromotion) {
            $html = '<div class="price mt10">'
                . '<span class="product-sale-price">' . convert_price($priceSale, true) . 'đ</span>'
                . '<span class="product-discount-badge">Giảm ' . $percent . '%</span>'
                . '<span class="product-old-price">' . convert_price($price, true) . 'đ</span>'
                . '</div>';
        } else {
            $html = '<div class="price mt10">'
                . '<span class="product-sale-price">' . convert_price($price, true) . 'đ</span>'
                . '</div>';
        }

        return [
            'price' => $price,
            'priceSale' => $priceSale,
            'percent' => $percent,
            'hasPromotion' => $hasPromotion,
            'html' => $html,
        ];
    }
}


if(!function_exists('getReview')){
    function getReview($product = null){

        /** @var $product Product */
        $totalReviews = $product->reviews()->count();
        $totalRate = number_format($product->reviews()->avg('score'), 1);
        $starPercent = ($totalReviews == 0) ? '0' : $totalRate/5*100;

        return [
            'star' => $starPercent,
            'count' => $totalReviews,
            'totalRate' => $totalRate
        ];
        
    }
}


if(!function_exists('convert_array')){
    function convert_array($system = null, $keyword = '', $value = ''){
        $temp = [];
        if(is_array($system)){
            foreach($system as $key => $val){
                $temp[$val[$keyword]] = $val[$value];
            }
        }
        if(is_object($system)){
            foreach($system as $key => $val){
                $temp[$val->{$keyword}] = $val->{$value};
            }
        }

        return $temp;
    }
}

if(!function_exists('convertDateTime')){
    function convertDateTime(string $date = '', string $format = 'd/m/Y H:i', string $inputDateFormat = 'Y-m-d H:i:s'){
       $carbonDate = \Carbon\Carbon::createFromFormat($inputDateFormat, $date);

       return $carbonDate->format($format);
    }
}

if(!function_exists('renderDiscountInformation')){
    function renderDiscountInformation($promotion = null){
        if($promotion->method === 'product_and_quantity'){
            // discountInformation là cột json và có thể NULL: khuyến mãi tạo ra mà
            // chưa nhập mức giảm thì cột này trống. Đọc thẳng ['info']['discountValue']
            // trên null làm sập cả trang danh sách khuyến mãi.
            $info = is_array($promotion->discountInformation)
                ? ($promotion->discountInformation['info'] ?? null)
                : null;

            if(is_array($info) && isset($info['discountValue']) && $info['discountValue'] !== ''){
                $discountType = (($info['discountType'] ?? '') == 'percent') ? '%' : 'đ';
                return '<span class="label label-success">'.$info['discountValue'].$discountType.' </span>';
            }

            // Chưa có mức giảm thì nói rõ, đừng để ô trống khiến tưởng lỗi.
            return '<span class="label label-default">Chưa thiết lập</span>';
        }
        return  '<div><a href="'.route('promotion.edit', $promotion->id).'">Xem chi tiết</a></div>';
    }
}

if(!function_exists('renderDiscountVoucher')){
    function renderDiscountVoucher($voucher = null){
        $discount_value = $voucher->discount_value;
        $discount_type = ($voucher->discount_type == 'PERCENTAGE') ? '%' : 'đ';
        return '<span class="label label-success">'.$discount_value.$discount_type.' </span>';
    }
}

if(!function_exists('renderSystemInput')){
    function renderSystemInput(string $name = '', $systems = null){
        return '<input 
            type="text"
            name="config['.$name.']"
            value="'.old($name, ($systems[$name]) ?? '').'"
            class="form-control"
            placeholder=""
            autocomplete="off"
        >';
    }
}


if(!function_exists('renderSystemImages')){
    function renderSystemImages(string $name = '', $systems = null){
        return '<input 
            type="text"
            name="config['.$name.']"
            value="'.old($name, ($systems[$name]) ?? '').'"
            class="form-control upload-image"
            placeholder=""
            autocomplete="off"
        >';
    }
}


if(!function_exists('renderSystemTextarea')){
    function renderSystemTextarea(string $name = '', $systems = null){
        return '<textarea name="config['.$name.']" class="form-control system-textarea">'.old($name, ($systems[$name]) ?? '').'</textarea>';
    }
}

if(!function_exists('renderSystemEditor')){
    function renderSystemEditor(string $name = '', $systems = null){
        return '<textarea name="config['.$name.']" id="'.$name.'" class="form-control system-textarea ck-editor">'.old($name, ($systems[$name]) ?? '').'</textarea>';
    }
}

if(!function_exists('renderSystemLink')){
    function renderSystemLink(array $item = [], $systems = null){
        return (isset($item['link'])) ? '<a class="system-link" target="'.$item['link']['target'].'" href="'.$item['link']['href'].'">'.$item['link']['text'].'</a>' : '';
    }
}

if(!function_exists('renderSystemTitle')){
    function renderSystemTitle(array $item = [], $systems = null){
        return (isset($item['title'])) ? '<span class="system-link text-danger">'.$item['title'].'</span>' : '';
    }
}

if(!function_exists('renderSystemSelect')){
    function renderSystemSelect(array $item, string $name = '', $systems = null){
       $html = '<select name="config['.$name.']" class="form-control">';
            foreach($item['option'] as $key => $val){
                $html .= '<option '.((isset($systems[$name]) && $key == $systems[$name]) ? 'selected' : '').' value="'.$key.'">'.$val.'</option>';
            }
       $html .= '</select>';

       return $html;
    }
}

if(!function_exists('write_url')){
    function write_url($canonical = null, bool $fullDomain = true, $suffix = true){
        $canonical = ($canonical) ?? '';
        if(strpos($canonical, 'http') !== false){
            return $canonical;
        }
        $fullUrl = (($fullDomain === true) ? config('app.url') : '').$canonical.( ($suffix === true) ? config('apps.general.suffix') : '' );
        return $fullUrl;
    }
}

if(!function_exists('seo')){
    /**
     * Meta tags for a model-backed page.
     *
     * @param  string  $ogType  'product' or 'article' where it applies, so the page
     *                          does not claim og:type=website for everything.
     */
    function seo($model = null, $page = 1, string $ogType = 'website'){
        $canonical = ($page > 1) ? write_url($model->canonical, true, false).'/trang-'.$page.config('apps.general.suffix'): write_url($model->canonical, true, true);

        // ?? only falls back on null, so an empty meta_title from the admin used to
        // ship an empty <title>. Compare against '' as well.
        $title = trim((string)($model->meta_title ?? ''));
        if($title === ''){
            $title = trim((string)($model->name ?? ''));
        }
        if($page > 1){
            // Paginated pages need distinct titles, otherwise they read as duplicates.
            $title .= ' - Trang ' . $page;
        }

        $description = trim((string)($model->meta_description ?? ''));
        if($description === ''){
            // Was $model->descipriont - a typo, so this fallback never ran and pages
            // without an explicit meta description shipped an empty one.
            $description = cut_string_and_decode($model->description ?? '', 168);
        }
        if(trim((string) $description) === ''){
            // Posts often have no summary; the opening of the body is better than nothing.
            $description = cut_string_and_decode($model->content ?? '', 168);
        }

        return [
            'meta_title' => $title,
            'meta_keyword' => trim((string)($model->meta_keyword ?? '')),
            'meta_description' => $description,
            'meta_image' => $model->image ?? '',
            'canonical' => $canonical,
            'og_type' => $ogType,
            // Page 2 onwards adds nothing new for search engines.
            'follow' => ($page > 1) ? 'noindex,follow' : 'index,follow',
        ];
    }
}

if(!function_exists('recursive')){
    function recursive($data, $parentId = 0){
        $temp = [];
        if(!is_null($data) && count($data)){
            foreach($data as $key => $val){
                if($val->parent_id == $parentId){
                    $temp[] = [
                        'item' => $val,
                        'children' => recursive($data, $val->id)
                    ];
                }
            }
        }
        return $temp;
    }
}

if(!function_exists('frontend_recursive_menu')){
    function frontend_recursive_menu(array $data = [], int $parentId = 0, int $count = 1, $type = 'html'){
        $html = '';
        if(isset($data) && !is_null($data) && count($data)){
            if($type == 'html'){
                foreach($data as $key => $val){
                    // Guard: bỏ qua menu item không có language (tránh lỗi pivot on null)
                    $firstLang = $val['item']->languages->first();
                    if (is_null($firstLang) || is_null($firstLang->pivot)) {
                        continue;
                    }
                    $name = $firstLang->pivot->name;
                    $canonical = write_url($firstLang->pivot->canonical, true, true);
                    $ulClass = ($count >= 1) ? 'menu-level__'.($count + 1) : '';
                    $html .= '<li class="'.(($count != 0 && count($val['children'])) ? 'children' : '').'">';
                        $html .= '<a href="'.(($name == 'Trang chủ') ? '.' : $canonical).'" title="'.$name.'" data-menu-id="'.$val['item']->id.'">'.
                        (($name == 'Home') ? '' : '').$name.'</a>';
                        if(count($val['children'])){
                            $html .= '<div class="dropdown-menu">';
                                $html .= '<ul class="uk-list uk-clearfix menu-style '.$ulClass.'">';
                                    $html .= frontend_recursive_menu($val['children'], $val['item']->parent_id,  $count + 1, $type);
                                $html .= '</ul>';
                            $html .='</div>';
                        }
                    $html .= '</li>';
                }
                return $html;
            } 
        }
        return $data;
       
    }
}

if (!function_exists('frontend_recursive_mobile_menu')) {
    function frontend_recursive_mobile_menu(array $data = [], int $parentId = 0, int $count = 1) {
        $html = '';
        if (isset($data) && !is_null($data) && count($data)) {
            foreach ($data as $key => $val) {
                $firstLang = $val['item']->languages->first();
                if (is_null($firstLang) || is_null($firstLang->pivot)) {
                    continue;
                }
                $name = $firstLang->pivot->name;
                $canonical = write_url($firstLang->pivot->canonical, true, true);
                
                $hasChildren = count($val['children']) > 0;
                
                if ($hasChildren) {
                    $html .= '<li class="uk-parent mobile-parent-item" style="position: relative;">';
                    $html .= '<a href="' . (($name == 'Trang chủ') ? '.' : $canonical) . '" title="' . $name . '" style="padding-right: 50px !important; display: block; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">' . $name . '</a>';
                    $html .= '<span class="mobile-menu-toggle" style="position: absolute; right: 0; top: 0; height: 50px; width: 50px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: rgba(255, 255, 255, 0.5); z-index: 10;"><i class="fa fa-chevron-down" style="font-size: 12px; transition: transform 0.2s;"></i></span>';
                    $html .= '<ul class="uk-nav-sub mobile-submenu-list" style="display: none; padding-left: 15px; margin: 0; background: rgba(0,0,0,0.15); border-bottom: 1px solid rgba(255,255,255,0.05);">';
                    $html .= frontend_recursive_mobile_menu($val['children'], $val['item']->parent_id, $count + 1);
                    $html .= '</ul>';
                    $html .= '</li>';
                } else {
                    $html .= '<li>';
                    $html .= '<a href="' . (($name == 'Trang chủ') ? '.' : $canonical) . '" title="' . $name . '" style="display: block; border-bottom: 1px solid rgba(255, 255, 255, 0.05);">' . $name . '</a>';
                    $html .= '</li>';
                }
            }
        }
        return $html;
    }
}


if(!function_exists('recursive_menu')){
    function recursive_menu($data){
        $html = '';
        if(count($data)){
            foreach($data as $key => $val){
                $itemId = $val['item']->id;
                $firstLang = $val['item']->languages->first();
                $itemName = ($firstLang && $firstLang->pivot) ? $firstLang->pivot->name : '(No name)';
                $itemUrl = route('menu.children', ['id' => $itemId]);


                $html .= "<li class='dd-item' data-id='$itemId'>" ;
                    $html .= "<div class='dd-handle'>";
                        $html .= "<span class='label label-info'><i class='fa fa-arrows'></i></span> $itemName";
                    $html .= "</div>";
                    $html .= "<a class='create-children-menu' href='$itemUrl'> Quản lý menu con </a>";

                    if(count($val['children'])){
                        $html .= "<ol class='dd-list'>";
                            $html .= recursive_menu($val['children']);
                        $html .= '</ol>';
                    }
                $html .= "</li>";
            }
        }
        return $html;
    }
}


if(!function_exists('buildMenu')){
    function buildMenu($menus = null, $parent_id = 0, $prefix = ''){
        $output = [];
        $count = 1;

        if(count($menus)){
            foreach($menus as $key => $val){
                if($val->parent_id == $parent_id){
                    $val->position = $prefix.$count;
                    $output[] = $val;
                    $output = array_merge($output, buildMenu($menus, $val->id, $val->position . '.'));
                    $count++;
                }
            }
        }
        return $output;
    }
}

use Illuminate\Support\Str;
if(!function_exists('loadClass')){
    function loadClass(string $model = '', $folder = 'Repositories', $interface = 'Repository'){
        $serviceInstance = null;
        $namespace = Str::words(Str::headline($model), 1, '');
        $version2 = ['Scholar', 'School', 'Major', 'Admission'];
        if(in_array($namespace, $version2)){
            $interface = 'Repo';
        }
        // $serviceInterfaceNamespace = '\App\\'.$folder.'\\' . ucfirst($model) . $interface;
        $serviceInterfaceNamespace = '\App\\' . $folder . '\\' . $namespace . '\\' . $model . $interface;
        if (class_exists($serviceInterfaceNamespace)) {
            $serviceInstance = app($serviceInterfaceNamespace);
        }
        return $serviceInstance;
    }
}

if(!function_exists('convertArrayByKey')){
    function convertArrayByKey($object = null, $fields = []){
        $temp = [];
        foreach($object as $key => $val){
            foreach($fields as $field){
                if(is_array($object)){
                    $temp[$field][] = $val[$field];
                }else{
                    $extract = explode('.', $field);
                    if(count($extract) == 2){
                        if($extract[1] == 'languages'){
                            $temp[$extract[0]][] = $val->{$extract[1]}->first()->pivot->{$extract[0]};
                        }else{
                            $temp[$extract[0]][] = $val->pivot->{$extract[0]};
                        }
                        
                    }else{
                        $temp[$field][] = $val->{$field}; 
                    }
                    
                }
            }
        }
        return $temp;
    }
}

if(!function_exists('renderQuickBuy')){
    function renderQuickBuy($product, string $canonical = '', string $name = ''){

        $class = 'btn-addCart';
        $openModal = '';
        if(isset($product->product_variants) && count($product->product_variants)){
            $class = '';
            $canonical = '#popup';
            $openModal = 'data-uk-modal';
        }

        $html = '<a href="'.$canonical.'" '.$openModal.' title="'.$name.'" class="'.$class.'">
                <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g>
                    <path d="M24.4941 3.36652H4.73614L4.69414 3.01552C4.60819 2.28593 4.25753 1.61325 3.70863 1.12499C3.15974 0.636739 2.45077 0.366858 1.71614 0.366516L0.494141 0.366516V2.36652H1.71614C1.96107 2.36655 2.19748 2.45647 2.38051 2.61923C2.56355 2.78199 2.68048 3.00626 2.70914 3.24952L4.29414 16.7175C4.38009 17.4471 4.73076 18.1198 5.27965 18.608C5.82855 19.0963 6.53751 19.3662 7.27214 19.3665H20.4941V17.3665H7.27214C7.02705 17.3665 6.79052 17.2764 6.60747 17.1134C6.42441 16.9505 6.30757 16.7259 6.27914 16.4825L6.14814 15.3665H22.3301L24.4941 3.36652ZM20.6581 13.3665H5.91314L4.97214 5.36652H22.1011L20.6581 13.3665Z" fill="#253D4E"></path>
                    <path d="M7.49414 24.3665C8.59871 24.3665 9.49414 23.4711 9.49414 22.3665C9.49414 21.2619 8.59871 20.3665 7.49414 20.3665C6.38957 20.3665 5.49414 21.2619 5.49414 22.3665C5.49414 23.4711 6.38957 24.3665 7.49414 24.3665Z" fill="#253D4E"></path>
                    <path d="M17.4941 24.3665C18.5987 24.3665 19.4941 23.4711 19.4941 22.3665C19.4941 21.2619 18.5987 20.3665 17.4941 20.3665C16.3896 20.3665 15.4941 21.2619 15.4941 22.3665C15.4941 23.4711 16.3896 24.3665 17.4941 24.3665Z" fill="#253D4E"></path>
                    </g>
                    <defs>
                    <clipPath>
                    <rect width="24" height="24" fill="white" transform="translate(0.494141 0.366516)"></rect>
                    </clipPath>
                    </defs>
                </svg>
        </a>';
    return $html;
    }
}

if(!function_exists('cutnchar')){
	function cutnchar($str = NULL, $n = 320){
		if(strlen($str) < $n) return $str;
		$html = substr($str, 0, $n);
		$html = substr($html, 0, strrpos($html,' '));
		return $html.'...';
	}
}

if(!function_exists('cut_string_and_decode')){
	function cut_string_and_decode($str = NULL, $n = 200){
        $str = html_entity_decode($str);
        $str = strip_tags($str);
        $str = cutnchar($str, $n);
        return $str;
	}
}

if(!function_exists('categorySelectRaw')){
    function categorySelectRaw($table = 'products'){
        $rawQuery = "
            (
                SELECT COUNT(id) 
                FROM {$table}s
                JOIN {$table}_catalogue_{$table} as tb3 ON tb3.{$table}_id = {$table}s.id
                WHERE tb3.{$table}_catalogue_id IN (
                    SELECT id
                    FROM {$table}_catalogues as parent_category
                    WHERE lft >= (SELECT lft FROM {$table}_catalogues as pc WHERE pc.id = {$table}_catalogues.id)
                    AND rgt <= (SELECT rgt FROM {$table}_catalogues as pc WHERE pc.id = {$table}_catalogues.id)
                )
            ) as {$table}s_count 
        "; 
        return $rawQuery;
    }
}


if(!function_exists('sortString')){
    function sortString($string = ''){
        $extract = explode(',', $string);
        $extract = array_map('trim', $extract);
        sort($extract, SORT_NUMERIC);
        $newArray = implode(',', $extract);
        return $newArray;
    }
}


if(!function_exists('sortAttributeId')){
    function sortAttributeId(array $attributeId = []){
        sort($attributeId, SORT_NUMERIC);
        $attributeId = implode(',', $attributeId);
        return $attributeId;
    }
}


if(!function_exists('vnpayConfig')){
    function vnpayConfig(){
        return [
            'vnp_Url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'vnp_Returnurl' => write_url('return/vnpay'),
            'vnp_TmnCode' => 'RLE42FCR',
            'vnp_HashSecret' => 'OQPUUZRVSSJASOQVUQHHURHBXGDIMBTU',
            'vnp_apiUrl' => 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html',
            'apiUrl' => 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'
        ];
    }
}


if(!function_exists('momoConfig')){
    function momoConfig(){
        return [
            'partnerCode' => 'MOMOBKUN20180529',
            'accessKey' => 'klm05TvNBzhg7h7j',
            'secretKey' => 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa',
        ];
    }
}

if(!function_exists('zaloConfig')){
    function zaloConfig(){
        return [
            'appid' => '553',
            'key1' => '9phuAOYhan4urywHTh0ndEXiV3pKHr5Q',
            'key2' => 'Iyz2habzyr7AG8SgvoBCbKwKi3UzlLi3',
        ];
    }
}

if(!function_exists('execPostRequest')){
    function execPostRequest($url, $data){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }
}


if(!function_exists('getReviewName')){
    function getReviewName($string){
        // $string = Nguyễn Công Tuấn
        $words = explode(' ', $string);
        $initialize = '';
        foreach($words as $key => $val){
            $initialize .= strtoupper(substr($val, 0, 1));
        }
        return $initialize;
    }
}


if(!function_exists('generateStar')){
    function generateStar($rating){
        $rating = max(1, min(5, $rating));
        $output = '<div class="review-star">';
            for($i = 1; $i <= $rating; $i++){
                $output .= '<i class="fa fa-star"></i>';
            }
            for($i = $rating + 1; $i <= 5; $i++){
                $output .= '<i class="fa fa-star-o"></i>';
            }
        $output .= '</div>';
        return $output;
    }
}


if(!function_exists('convertCombineArray')){
    function convertCombineArray(mixed $data, $mix_1 = ''){
        $array = [];
        foreach($data as $key => $val){
            $array[$val->id] = (($mix_1 != '') ? $val->{$mix_1} : $val->code).' / '.$val->phone;
        }
        return $array;
    }
}


if(!function_exists('convertArray')){
    function convertArray($datas){
        $id = [];
        foreach ($datas as $data) {
            $id[]= $data->id;
        }
        return $id;
    }
}

if(!function_exists('convertToIdNameArray')){
    function convertToIdNameArray($customers)
   {
    $idNameArray = [];

    foreach ($customers as $customer) {
        $idNameArray[$customer['id']] = $customer['name'];
    }

    return $idNameArray;
    }

}

if(!function_exists('convertToK')){
    function convertToK($discount)
   {
        if ($discount >= 1000) {
            return number_format($discount / 1000, 0, '.', '') . 'k';
        }
        return $discount;
    }
}
  
use Illuminate\Support\Facades\DB;
if(!function_exists('convertData')){
    function convertData($data, $type)
    {
        $promotion_id = $data->id;
        $payload_pivot = ($type == 'products') ? $data->promotion_rules : $data->promotion_gifts;
        $products = ($type == 'products') ? 
        DB::table('promotion_rules')->where('promotion_id', $promotion_id)->get() : DB::table('promotion_gifts')->where('promotion_id', $promotion_id)->get();
        $temp = [];
        if(!is_null($products)){
            foreach($products as $k => $v){
                $temp['id'][$k] = $v->product_id;
                $temp['quantity'][$k] = $v->quantity;
                $temp['image'][$k] = $payload_pivot[$k]['image'];
                $temp['name'][$k] = $payload_pivot[$k]->languages->first()->pivot->name;
            }
        }
        return $temp;
    }
}



if(!function_exists('thumb')){
    function thumb($path, $width = null, $height = null)
    {
        $width = 600;
        $height = 400;

        if (empty($path)) {
            if (request()->segment(1) === 'admin' || request()->segment(1) === 'seller') {
                return asset('images/no-image.jpg');
            }
            return 'https://gomhang.vn/wp-content/uploads/2026/08/072dcae0-adf1-40fb-a738-6bb864078920-400x533.png';
        }
        
        $params = ['src' => $path];
        
        if ($width) {
            $params['w'] = $width;
        }
        
        if ($height) {
            $params['h'] = $height;
        }
        
        // return route('thumb', $params);

        return $path;
    }
}

if (!function_exists('convertImgToAnchor')) {
    function convertImgToAnchor($html) {
        if (!$html || !is_string($html)) {
            return $html;
        }

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $images = $dom->getElementsByTagName('img');
        if ($images->length === 0) {
            return $html;
        }

        foreach ($images as $image) {
            $src = $image->getAttribute('src') ?: '';
            $alt = $image->getAttribute('alt') ?: '';
            $class = $image->getAttribute('class') ?: 'img-cover img-zoomin';

            // Tạo thẻ <a>
            $anchor = $dom->createElement('a');
            $anchor->setAttribute('href', $src);
            $anchor->setAttribute('title', $alt);
            $anchor->setAttribute('class', $class);

            // Thêm <div class="skeleton-loading"> vào trong <a>
            $skeleton = $dom->createElement('span');
            $skeleton->setAttribute('class', 'skeleton-loading');
            $anchor->appendChild($skeleton);

            // Thêm <img class="lazy-image"> vào trong <a>
            $newImg = $dom->createElement('img');
            $newImg->setAttribute('class', 'lazy-image');
            $newImg->setAttribute('data-src', $src);
            $newImg->setAttribute('alt', $alt);
            $anchor->appendChild($newImg);

            // Thay thế <img> bằng <a> hoàn chỉnh
            $image->parentNode->replaceChild($anchor, $image);
        }

        $html = $dom->saveHTML();
        // Loại bỏ các thẻ HTML bổ sung do DOMDocument thêm vào
        $html = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace(['<html><body>', '</body></html>'], '', $html));

        return $html;
    }
}

if (!function_exists('calculateCourses')) {
    function calculateCourses($product) {
        $totalMinutes = 0;
        $totalSession = 0;
        $temp = $product->chapter;
        if (!is_array($temp)) {
            $temp = json_decode($temp, true); 
        }
        foreach ($temp as $chapter) {
            if (isset($chapter['content']) && is_array($chapter['content'])) {
                foreach ($chapter['content'] as $lesson) {
                    $totalSession++;
                    if (isset($lesson['time'])) {
                        $totalMinutes += (int)$lesson['time'];
                    }
                }
            }
        }
        
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        
        $durationText = '';
        if ($hours > 0 && $minutes > 0) {
            $durationText = $hours . ' giờ ' . $minutes . ' phút';
        } elseif ($hours > 0) {
            $durationText = $hours . ' giờ';
        } else {
            $durationText = $minutes . ' phút';
        }
        $chapters = [
            'durationText' => $durationText,
            'totalSession' => $totalSession
        ];
        return $chapters;
    }
}

if (!function_exists('getProductPriceInfo')) {
    /**
     * Price a product for display. Discounts come from the promotions module and
     * nowhere else - the old products.percent fallback is gone, so a discount on
     * screen always corresponds to a real campaign a shopper can be held to.
     *
     * Expects PromotionPricingService to have attached `promotions` (the campaign
     * in force now) and `promotionChain` (the segments after it).
     *
     * @return array{price: float, priceSale: float, percent: int, hasPromotion: bool,
     *               endDate: ?string, promotionName: ?string, chain: array}
     */
    function getProductPriceInfo($product) {
        $originalPrice = (float)($product->price ?? 0);
        $chain = $product->promotionChain ?? [];

        $blank = [
            'price' => $originalPrice,
            'priceSale' => $originalPrice,
            'percent' => 0,
            'hasPromotion' => false,
            'endDate' => null,
            'promotionName' => null,
            'chain' => is_array($chain) ? $chain : [],
        ];

        $promo = $product->promotions ?? null;
        if ($promo instanceof \Illuminate\Support\Collection || $promo instanceof \Illuminate\Database\Eloquent\Collection) {
            $promo = $promo->first();
        } elseif (is_array($promo)) {
            $promo = empty($promo) ? null : (object) $promo;
        }

        if (!is_object($promo)) {
            return $blank;
        }

        $discount = (float)($promo->discount ?? 0);
        if ($discount <= 0 || $originalPrice <= 0) {
            return $blank;
        }

        $discount = min($discount, $originalPrice);

        return [
            'price' => $originalPrice,
            'priceSale' => max(0.0, $originalPrice - $discount),
            'percent' => (int) round(($discount / $originalPrice) * 100),
            'hasPromotion' => true,
            'endDate' => $promo->endsAt ?? null,
            'promotionName' => $promo->name ?? null,
            'chain' => $blank['chain'],
        ];
    }
}

if(!function_exists('asset_v')){
    /**
     * asset() kèm số phiên bản theo thời điểm sửa file.
     *
     * asset() trơn sinh URL không đổi, nên sau khi sửa file JS/CSS thì trình
     * duyệt vẫn chạy bản cũ trong cache - đã mất một lượt tưởng là chưa sửa
     * được lỗi. Gắn ?v=<mtime> thì file đổi là URL đổi, tự làm mới cho mọi
     * người, không phải nhờ ai Ctrl+F5.
     *
     * Không tìm thấy file thì trả về asset() thường chứ không chặn trang.
     */
    function asset_v(string $path): string
    {
        $full = public_path($path);

        if(!is_file($full)){
            return asset($path);
        }

        return asset($path) . '?v=' . filemtime($full);
    }
}
