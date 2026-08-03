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
        if (!$promo || empty($promo->endDate)) {
            return;
        }
        $end = Carbon::parse($promo->endDate);
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
        $seo = seo($product);
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

    private function schema($product, $productCatalogue, $breadcrumb)
    {
        $image = $product->image;
        $name = $product->languages->first()->pivot->name;
        $totalReviews = $product->reviews()->where('status', 1)->count();
        $totalRate = number_format($product->reviews()->where('status', 1)->avg('score'), 1);
        $description = strip_tags($product->languages->first()->pivot->description);
        $cat_name = $productCatalogue->languages->first()->pivot->name;
        $cat_canonical = write_url($productCatalogue->languages->first()->pivot->canonical);
        $reviewListElements = '';
        foreach ($product->reviews as $review) {
            $rating = generateStar($review->score);
            $created_at = convertDateTime($review->created_at);
            $reviewListElements .= "
                {
                    \"@type\": \"Review\",
                    \"reviewRating\": {
                        \"@type\": \"Rating\",
                        \"ratingValue\": \"" . $rating . "\",
                        \"bestRating\": \"5\"
                    },
                    \"author\": {
                        \"@type\": \"Person\",
                        \"name\": \"" . $review->fullname . "\"
                    },
                    \"reviewBody\": \"" . $review->description . "\",
                    \"datePublished\": \"" . $created_at . "\"
                },";
        }

        $reviewListElements = rtrim($reviewListElements, ',');

        $itemBreadcrumbElements = '';

        $positionBreadcrumb = 2;

        foreach ($breadcrumb as $key => $item) {
            $name = $item->languages->first()->pivot->name;
            $canonical = write_url($item->languages->first()->pivot->canonical);
            $itemBreadcrumbElements .= "
                {
                    \"@type\": \"ListItem\",
                    \"position\": $positionBreadcrumb,
                    \"name\": \"" . $name . "\",
                    \"item\": \"" . $canonical . "\",
                },";
            $positionBreadcrumb++;
        }

        $itemBreadcrumbElements = rtrim($itemBreadcrumbElements, ',');

        $schema = "
            <script type=\"application/ld+json\">
                {
                    \"@type\": \"BreadcrumbList\",
                    \"itemListElement\": [
                        {
                            \"@type\": \"ListItem\",
                            \"position\": 1,
                            \"name\": \" Trang chủ  \",
                            \"item\": \" " . config('app.url') . " \"
                        },
                        $itemBreadcrumbElements
                    ]
                },
                {
                    \"@context\": \"https://schema.org\",
                    \"@type\": \"Product\",
                    \"name\": \" " . $name . " \",
                    \"description\": \"  " . $description . "  \",
                    \"image\": \"  " . $image . "  \",
                    \"brand\": {
                        \"@type\": \"Brand\",
                        \"name\": \"An Hưng\"
                    },
                    \"manufacturer\": {
                        \"@type\": \"Organization\",
                        \"name\": \"An Hưng\",
                        \"url\": \" " . config('app.url') . "\"
                    },
                    \"material\": \" " . $cat_name . " \",
                    \"category\": \" " . $cat_canonical . " \",
                    \"sku\": \"\",
                    \"mpn\": \"\",
                    \"offers\": {
                        \"@type\": \"Offer\",
                        \"price\": \"\",
                        \"priceCurrency\": \"\",
                        \"availability\": \"\",
                        \"seller\": {
                            \"@type\": \"Organization\",
                            \"name\": \"An Hưng\"
                        },
                        \"priceValidUntil\": \"\",
                        \"itemCondition\": \"https://schema.org/NewCondition\"
                    },
                    \"aggregateRating\": {
                        \"@type\": \"AggregateRating\",
                        \"ratingValue\": \" " . $totalRate . "  \",
                        \"reviewCount\": \" " . $totalReviews . "\"
                    },
                    \"review\": [
                        $reviewListElements
                    ]
                }
            </script>
        ";

        return $schema;

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