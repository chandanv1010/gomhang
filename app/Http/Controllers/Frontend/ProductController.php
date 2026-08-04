<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\Product\ProductCatalogueRepository;
use App\Services\V1\Product\ProductCatalogueService;
use App\Services\V1\Product\ProductService;
use App\Services\V1\Product\VoucherService;
use App\Services\V1\Product\PromotionService;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Core\ReviewRepository;
use App\Repositories\Product\VoucherRepository;
use App\Repositories\Core\OrderRepository;
use App\Services\V1\Core\WidgetService;


use Illuminate\Support\Facades\Auth;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Support\Schema;

class ProductController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productCatalogueRepository;
    protected $productCatalogueService;
    protected $productService;
    protected $voucherService;
    protected $promotionService;
    protected $productRepository;
    protected $reviewRepository;
    protected $voucherRepository;
    protected $widgetService;
    protected $customerRepository;
    protected $orderRepository;

    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        ProductCatalogueService $productCatalogueService,
        ProductService $productService,
        ProductRepository $productRepository,
        ReviewRepository $reviewRepository,
        VoucherRepository $voucherRepository,
        WidgetService $widgetService,
        VoucherService $voucherService,
        PromotionService $promotionService,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository,
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->reviewRepository = $reviewRepository;
        $this->voucherRepository = $voucherRepository;
        $this->widgetService = $widgetService;
        $this->voucherService = $voucherService;
        $this->promotionService = $promotionService;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }

    private function promotionLeft($product = null){
        if(empty($product->promotions)){
            return;
        }
        $promo = $product->promotions;
        if ($promo instanceof \Illuminate\Support\Collection || $promo instanceof \Illuminate\Database\Eloquent\Collection) {
            $promo = $promo->first();
        }
        // Open ended campaigns have no deadline, so there are no days left to show.
        if (!$promo || empty($promo->endsAt)) {
            return;
        }
        $end = Carbon::parse($promo->endsAt);
        $now = Carbon::now();
        $dayLefts = $now->diffInDays($end, false);
        return $dayLefts;
    }


    public function index($id, $request)
    {
        $language = $this->language;
        $product = $this->productRepository->getProductById($id, $this->language, config('apps.general.defaultPublish'));
        if (is_null($product)) {
            abort(404);
        }
        $product = $this->productService->combineProductAndPromotion([$id], $product, true);
       
        $promotion_gifts = null;
        $promotion_gifts = $this->promotionService->getProTakeGiftBuyProduct($id);
        $product['promotion_gifts'] = $promotion_gifts;
        $seller = null;
        if (!is_null($product->seller_id)) {
            $seller = $this->customerRepository->findById($product->seller_id);
        }

        $promotionLeft = $this->promotionLeft($product) ?? null;
        $productCatalogue = $this->productCatalogueRepository->getProductCatalogueById($product->product_catalogue_id, $this->language);
        $parent = null;
        $children = null;
        if ($productCatalogue->parent_id != 0) {
            $parent = $this->productCatalogueRepository->getParent($productCatalogue, $this->language);
            $children = $this->productCatalogueRepository->getChildren($parent);
        } else {
            $children = $this->productCatalogueRepository->getChildren($productCatalogue);
        }

        $breadcrumb = $this->productCatalogueRepository->breadcrumb($productCatalogue, $this->language);
        /* ------------------- */
        $product = $this->productService->getAttribute($product, $this->language);
        $category = recursive(
            $this->productCatalogueRepository->all([
                'languages' => function ($query) use ($language) {
                    $query->where('language_id', $language);
                }
            ], categorySelectRaw('product'))
        );

        $wishlist = Cart::instance('wishlist')->content();

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'featured-products'],
            ['keyword' => 'product-category', 'children' => true],
            ['keyword' => 'product-category-highlight', 'object' => true],
            ['keyword' => 'about-us-2'],
            ['keyword' => 'featured-project'],
            ['keyword' => 'homepage-news'],
        ], $this->language);

        $productSeen = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'qty' => 1,
            'options' => [
                'canonical' => $product->languages->first()->pivot->canonical,
                'image' => $product->image,
            ]
        ];

        $productRelated = $this->productRepository->getRelated(6, $product->product_catalogue_id, $product->id);
        Cart::instance('seen')->add($productSeen);
        $cartSeen = Cart::instance('seen')->content();
        $carts = Cart::instance('shopping')->content() ?? null;
        $config = $this->config();
        $customer = Auth::guard('customer')->user();
        $voucher_product = (!is_null($customer)) ? $this->voucherService->getVoucherForProduct($id, $carts, $customer->id) : null;
        $system = $this->system;
        $seo = seo($product, 1, 'product');
        $schema = $this->schema($product, $productCatalogue, $breadcrumb);
        $template = 'frontend.product.product.index';

        return view($template, compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'productCatalogue',
            'customer',
            'voucher_product',
            'product',
            'category',
            'widgets',
            'wishlist',
            'cartSeen',
            'seller',
            'carts',
            'schema',
            'productRelated',
            'children',
            'promotionLeft',
        ));
    }

    /**
     * Structured data for the product page.
     *
     * Rebuilt on App\Support\Schema: the hand-written JSON here was invalid (two
     * top level objects with no array around them), named a brand from another
     * company, sent an empty offers.price and emitted aggregateRating with zero
     * reviews - any one of which voids the Product rich result.
     */
    private function schema($product, $productCatalogue, $breadcrumb)
    {
        $language = $product->languages->first()->pivot ?? null;
        $priceInfo = getProductPriceInfo($product);

        // Breadcrumb: home, then the catalogue trail, then this product.
        $trail = [['name' => 'Trang chủ', 'url' => config('app.url')]];
        foreach (($breadcrumb ?? []) as $item) {
            $itemLanguage = $item->languages->first()->pivot ?? null;
            if (!$itemLanguage) {
                continue;
            }
            $trail[] = [
                'name' => $itemLanguage->name,
                'url' => write_url($itemLanguage->canonical),
            ];
        }
        $trail[] = ['name' => $language->name ?? ''];

        $approvedReviews = $product->reviews->where('status', 1);

        // Brand comes from the product's own brand attribute, not a constant.
        $brandName = '';
        $brandCatalogueId = (int) config('apps.general.brandAttributeCatalogueId');
        foreach (($product->attributeCatalogue ?? []) as $attributeCatalogue) {
            if ((int) ($attributeCatalogue->id ?? 0) !== $brandCatalogueId) {
                continue;
            }
            $brandName = optional(collect($attributeCatalogue->attributes ?? [])->first())->name ?? '';
            break;
        }

        return Schema::script([
            Schema::organization($this->system),
            Schema::breadcrumb($trail),
            Schema::product([
                'name' => $language->name ?? '',
                'url' => write_url($language->canonical ?? ''),
                'image' => image($product->image),
                'description' => $language->description ?? '',
                'sku' => $product->code ?? '',
                'brand' => $brandName,
                'category' => optional($productCatalogue->languages->first())->pivot->name ?? '',
                'price' => $priceInfo['priceSale'],
                'priceValidUntil' => $priceInfo['endDate'],
                'inStock' => ((int) ($product->stock ?? 0)) > 0,
                'ratingValue' => $approvedReviews->avg('score'),
                'reviewCount' => $approvedReviews->count(),
                'reviews' => $approvedReviews->map(fn ($review) => [
                    'score' => $review->score,
                    'author' => $review->fullname,
                    'body' => $review->description,
                    'date' => $review->created_at,
                ])->all(),
            ], $this->system),
        ]);
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'js' => [
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.js',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'frontend/core/library/cart.js',
                'frontend/core/library/product.js',
                'frontend/core/library/review.js',
                'frontend/resources/library/js/carousel.js',
            ],
            'css' => [
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
                'frontend/core/css/product.css',
                'frontend/resources/css/custom.css'
            ]
        ];
    }

}