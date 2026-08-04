<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;


use App\Repositories\Product\ProductCatalogueRepository;
use App\Services\V1\Product\ProductCatalogueService;
use App\Services\V1\Product\ProductService;
use App\Services\V1\Core\WidgetService;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Post\PostRepository;
use App\Support\Schema;
use App\Services\V1\Product\CompareService;


use Gloudemans\Shoppingcart\Facades\Cart;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\Post;

class ProductCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $widgetService;
    protected $productRepository;
    protected $lecturerRepository;
    protected $compareService;
    protected $postRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        ProductRepository $productRepository,
        WidgetService $widgetService,
        CompareService $compareService,
        PostRepository $postRepository,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->widgetService = $widgetService;
        $this->productRepository = $productRepository;
        $this->compareService = $compareService;
        $this->postRepository = $postRepository;
        parent::__construct();
    }


    public function index($id, $request, $page = 1)
    {
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($id, $this->language);
        $parent = null;
        $descendantTrees = null;
        $descendantTrees = $this->productCatalogueService->getChildren();
        $filters = $this->filter($productCatalogue);
        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        $products = $this->productService->paginate(
            $request,
            $this->language,
            $productCatalogue,
            $page,
            ['path' => $productCatalogue->canonical],
        );
        $products = $this->combineProductValues($products);
        $productCatalogues = recursive($this->productCatalogueRepository->all(['languages']));
        
        $allCatalogues = $this->productCatalogueRepository->all(['languages', 'products']);
        $subCategories = $allCatalogues->filter(function($cat) use ($productCatalogue) {
            return $cat->parent_id == $productCatalogue->id && $cat->publish == 2;
        });
        foreach ($subCategories as $subCat) {
            $descendants = $allCatalogues->filter(function($item) use ($subCat) {
                return $item->lft >= $subCat->lft && $item->rgt <= $subCat->rgt;
            });
            $totalProducts = $descendants->sum(function($cate) {
                return $cate->products->count();
            });
            $subCat->setAttribute('total_product_count', $totalProducts);
        }
        $subCategories = $subCategories->sortBy('order')->values();

        $chungLoaiList = $allCatalogues->filter(function($cat) { return $cat->parent_id == 4 && $cat->publish == 2; })->sortBy('order')->values();
        $iphoneList = $allCatalogues->filter(function($cat) { return $cat->parent_id == 5 && $cat->publish == 2; })->sortBy('order')->values();
        $samsungList = $allCatalogues->filter(function($cat) { return $cat->parent_id == 6 && $cat->publish == 2; })->sortBy('order')->values();
        $allCategories = $allCatalogues->filter(function($cat) { return $cat->publish == 2; })->sortBy('order')->values();

        // Dynamic Brand count for the current category
        $brands = collect();
        $brandCat = \Illuminate\Support\Facades\DB::table('attribute_catalogues as ac')
            ->join('attribute_catalogue_language as acl', 'ac.id', '=', 'acl.attribute_catalogue_id')
            ->where('acl.name', 'LIKE', '%Thương hiệu%')
            ->orWhere('acl.name', 'LIKE', '%Brand%')
            ->orWhere('ac.id', (int) config('apps.general.brandAttributeCatalogueId'))
            ->select('ac.id')
            ->first();

        if ($brandCat) {
            $brandCatId = $brandCat->id;
            $brandAttrs = \Illuminate\Support\Facades\DB::table('attributes as a')
                ->join('attribute_language as al', 'a.id', '=', 'al.attribute_id')
                ->where('al.language_id', $this->language)
                ->where('a.attribute_catalogue_id', $brandCatId)
                ->select('a.id', 'a.image', 'al.name', 'al.canonical')
                ->get();

            $descendantIds = $allCatalogues->filter(function($item) use ($productCatalogue) {
                return $item->lft >= $productCatalogue->lft && $item->rgt <= $productCatalogue->rgt;
            })->pluck('id')->toArray();

            foreach ($brandAttrs as $bAttr) {
                $prodCount = \Illuminate\Support\Facades\DB::table('products as p')
                    ->join('product_catalogue_product as pcp', 'p.id', '=', 'pcp.product_id')
                    ->whereIn('pcp.product_catalogue_id', $descendantIds)
                    ->where(function($query) use ($brandCatId, $bAttr) {
                        $query->whereJsonContains('p.attribute->' . $brandCatId, (string)$bAttr->id)
                              ->orWhereJsonContains('p.attribute->' . $brandCatId, (int)$bAttr->id);
                    })
                    ->count();

                if ($prodCount > 0) {
                    $brands->push((object)[
                        'id' => $bAttr->id,
                        'name' => $bAttr->name,
                        'canonical' => $bAttr->canonical,
                        'image' => $bAttr->image,
                        'logo' => brand_logo($bAttr),
                        'count' => $prodCount
                    ]);
                }
            }
        }

        // dd($productCatalogues);
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'featured-products'],
            ['keyword' => 'product-category', 'children' => true],
            ['keyword' => 'product-category-highlight', 'object' => true],
            ['keyword' => 'about-us-2'],
        ], $this->language);
        $config = $this->config();
        $system = $this->system;
        $seo = seo($productCatalogue, $page);
        $schema = $this->schema($productCatalogue, $products, $breadcrumb);
        $template = 'frontend.product.catalogue.index';
        return view($template, compact(
            'descendantTrees',
            'config',
            'seo',
            'system',
            'breadcrumb',
            'productCatalogue',
            'products',
            'filters',
            'widgets',
            'schema',
            'productCatalogues',
            'subCategories',
            'chungLoaiList',
            'iphoneList',
            'samsungList',
            'allCategories',
            'brands'
        ));
    }

    private function combineProductValues($products)
    {
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
            $products = $this->productService->combineProductRelation($products);
        }

        return $products;
    }

    private function filter($productCatalogue)
    {
        $filters = null;
        $children = $this->productCatalogueRepository->getChildren($productCatalogue);
        $groupedAttributes = [];
        foreach ($children as $child) {
            if (isset($child->attribute) && !is_null($child->attribute) && count($child->attribute)) {
                foreach ($child->attribute as $key => $value) {
                    if (!isset($groupedAttributes[$key])) {
                        $groupedAttributes[$key] = [];
                    }
                    $groupedAttributes[$key][] = $value;
                }
            }
        }
        foreach ($groupedAttributes as $key => $value) {
            $groupedAttributes[$key] = array_merge(...$value);
        }

        if (isset($groupedAttributes) && !is_null($groupedAttributes) && count($groupedAttributes)) {
            $filters = $this->productCatalogueService->getFilterList($groupedAttributes, $this->language);
        }
        return $filters;
    }


    public function search(Request $request)
    {
        $keyword = trim((string) $request->input('keyword'));

        // Search covers products and posts. Only the active tab is paginated; the
        // other tab shows its total so a visitor can see there are results there.
        $type = ($request->input('type') === 'post') ? 'post' : 'product';

        $products = null;
        $posts = null;

        if ($type === 'product') {
            $products = $this->productRepository->search($keyword, $this->language);
            $productId = $products->pluck('id')->toArray();
            if (count($productId)) {
                $products = $this->productService->combineProductAndPromotion($productId, $products);
            }
            $productTotal = $products->total();
            $postTotal = $this->postRepository->search($keyword, $this->language, 1)->total();
        } else {
            $posts = $this->postRepository->search($keyword, $this->language);
            $postTotal = $posts->total();
            $productTotal = $this->productRepository->search($keyword, $this->language)->total();
        }

        $config = $this->config();

        $system = $this->system;

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'news-outstanding', 'object' => true],
        ], $this->language);

        $seo = [
            'meta_title' => 'Tìm kiếm cho từ khóa: ' . $keyword,
            'meta_keyword' => '',
            // A real description helps the SERP snippet even on a noindex page.
            'meta_description' => 'Kết quả tìm kiếm cho "' . $keyword . '" tại '
                . system_brand($system) . ': ' . $productTotal . ' sản phẩm, ' . $postTotal . ' bài viết.',
            'meta_image' => '',
            'canonical' => write_url('tim-kiem'),
            // Search result pages should not be indexed.
            'follow' => 'noindex,follow',
        ];

        // There is no resources/views/mobile directory, so the mobile branch that
        // used to be here threw "View [mobile.product.catalogue.search] not found"
        // for every phone. The frontend template is responsive; serve it to all
        // devices.
        $template = 'frontend.product.catalogue.search';


        return view($template, compact(
            'config',
            'seo',
            'system',
            'products',
            'posts',
            'widgets',
            'keyword',
            'type',
            'productTotal',
            'postTotal'
        ));
    }

    public function wishlist(Request $request)
    {
        $wishlistItems = Cart::instance('wishlist')->content();
        $ids = $wishlistItems->pluck('id')->map(function ($id) {
            return (int)$id;
        })->filter()->values()->toArray();

        $products = collect();
        if (!empty($ids)) {
            $products = $this->productRepository->findByIds($ids, $this->language);
            $products = $this->productService->combineProductAndPromotion($ids, $products);
            $products = $products->sortBy(function ($product) use ($ids) {
                return array_search($product->id, $ids);
            })->values();
        }

        $perPage = 8;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $wishlistProducts = new LengthAwarePaginator(
            $products->forPage($page, $perPage),
            $products->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $config = $this->config();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Danh sách yêu thích',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('yeu-thich')
        ];
        $wishlistCount = Cart::instance('wishlist')->count();

        return view('frontend.product.catalogue.wishlist', [
            'config' => $config,
            'seo' => $seo,
            'system' => $system,
            'products' => $wishlistProducts,
            'wishlistCount' => $wishlistCount,
        ]);
    }

    public function compare(Request $request)
    {
        $comparePayload = $this->compareService->getPayload($this->language);

        $config = $this->config();
        $system = $this->system;
        $seo = [
            'meta_title' => 'So sánh sản phẩm',
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => write_url('so-sanh'),
        ];

        return view('frontend.product.catalogue.compare', array_merge($comparePayload, [
            'config' => $config,
            'seo' => $seo,
            'system' => $system,
            'maxCompareItems' => CompareService::MAX_ITEMS,
        ]));
    }

    /**
     * Structured data for a product listing. Rebuilt on App\Support\Schema; the
     * hand-written JSON it replaced was syntactically invalid.
     */
    private function schema($productCatalogue, $products, $breadcrumb)
    {
        $language = optional($productCatalogue->languages->first())->pivot;

        $trail = [['name' => 'Trang chủ', 'url' => config('app.url')]];
        foreach (($breadcrumb ?? []) as $item) {
            $itemLanguage = optional($item->languages->first())->pivot;
            if (!$itemLanguage) {
                continue;
            }
            $trail[] = ['name' => $itemLanguage->name, 'url' => write_url($itemLanguage->canonical)];
        }
        // Drop the trailing url: the last crumb is the page being viewed.
        if (count($trail) > 1) {
            unset($trail[count($trail) - 1]['url']);
        }

        $items = [];
        foreach ($products as $product) {
            $items[] = [
                'name' => $product->name ?? '',
                'url' => write_url($product->canonical ?? ''),
            ];
        }

        return Schema::script([
            Schema::organization($this->system),
            Schema::webSite($this->system),
            Schema::breadcrumb($trail),
            Schema::collectionPage([
                'name' => $language->name ?? '',
                'url' => write_url($language->canonical ?? ''),
                'description' => $language->description ?? '',
            ], $items),
        ]);
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'externalJs' => [
                '//code.jquery.com/ui/1.11.4/jquery-ui.js'
            ],
            'css' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
                'frontend/resources/css/custom.css',
            ],
            'js' => [
                'frontend/core/library/filter.js',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'frontend/resources/library/js/carousel.js',
            ],

        ];
    }
}
