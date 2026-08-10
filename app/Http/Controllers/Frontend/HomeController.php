<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;

use App\Repositories\Core\SystemRepository;
use App\Services\V1\Core\SlideService;
use App\Enums\SlideEnum;
use App\Services\V1\Core\WidgetService;
use Illuminate\Http\Request;

class HomeController extends FrontendController
{
    protected $systemRepository;
    protected $slideService;
    protected $widgetService;
    protected $scholarService;

    public function __construct(
        SlideService $slideService,
        SystemRepository $systemRepository,
        WidgetService $widgetService,
    ) {
        $this->slideService = $slideService;
        $this->systemRepository = $systemRepository;
        $this->widgetService = $widgetService;
        
        parent::__construct(
            $systemRepository,
        );
    }


    public function index()
    {
        $config = $this->config();

        $slides = $this->slideService->getSlide(
            [SlideEnum::MAIN, SlideEnum::TECHSTAFF, SlideEnum::PARTNER, 'policy-slides'],
            $this->language
        );

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'about-us'],
            ['keyword' => 'solution'],
            [
                // Không cần 'children': khối danh mục ngoài trang chủ hiện đúng
                // những danh mục được tick trong widget, không đổ ra danh mục con.
                'keyword' => 'solution-product',
            ],
            [
                'keyword' => 'si-le-cong-nghe',
                // This is the widget that renders product cards with prices, so it
                // needs promotion pricing attached. Without it the homepage showed
                // full price while the catalogue showed the discount.
                'promotion' => true
            ],
            ['keyword' => 'featured-project'],
            ['keyword' => 'homepage-news'],
            ['keyword' => 'homepage-video'],
        ], $this->language);

        $allCategories = \Illuminate\Support\Facades\DB::table('product_catalogues')
            ->join('product_catalogue_language as cl', 'cl.product_catalogue_id', '=', 'product_catalogues.id')
            ->where('cl.language_id', '=', $this->language)
            ->where('product_catalogues.publish', '=', 2)
            ->whereNull('product_catalogues.deleted_at')
            ->select('product_catalogues.id', 'cl.name', 'cl.canonical')
            ->orderBy('cl.name', 'ASC')
            ->get();

        $system = $this->system;
        // The seo_meta_* keys do not exist in the systems table, so ?? never fired
        // and the homepage shipped an empty <title> and no description at all.
        // Fall back through the keys that do exist, then to the brand name.
        $brand = system_brand($this->system);
        $homeTitle = trim((string) ($this->system['seo_meta_title'] ?? ''));
        if ($homeTitle === '') {
            $slogan = trim((string) ($this->system['homepage_slogan'] ?? ''));
            $homeTitle = $slogan !== ''
                ? $brand . ' - ' . $slogan
                : $brand . ' - Sỉ lẻ phụ kiện công nghệ chính hãng';
        }

        $homeDescription = trim((string) ($this->system['seo_meta_description'] ?? ''));
        if ($homeDescription === '') {
            $homeDescription = trim((string) ($this->system['homepage_short_intro'] ?? ''));
        }
        if ($homeDescription === '') {
            $homeDescription = $brand . ' - phụ kiện điện thoại, sạc, cáp, tai nghe chính hãng. '
                . 'Bảo hành 12 tháng, đổi mới 8 ngày đầu, giao hàng toàn quốc, '
                . 'kiểm tra sản phẩm trước khi thanh toán.';
        }

        $seo = [
            'meta_title' => $homeTitle,
            'meta_keyword' => trim((string) ($this->system['seo_meta_keyword'] ?? '')),
            'meta_description' => $homeDescription,
            'meta_image' => ($this->system['seo_meta_images'] ?? '') ?: ($this->system['homepage_logo'] ?? ''),
            'canonical' => config('app.url'),
            'og_type' => 'website',
        ];
        $schema = $this->schema($seo);
        $template = 'frontend.homepage.home.index';
        return view($template, compact(
            'config',
            'slides',
            'seo',
            'system',
            'schema',
            'widgets',
            'allCategories',
        ));
    }

    /**
         * @param array $seo
         * @return string
         */
        public function schema(array $seo = []): string
        {
            $schema = "<script type='application/ld+json'>
                {
                    \"@context\": \"https://schema.org\",
                    \"@type\": \"WebSite\",
                    \"name\": \"" . ($seo['meta_title'] ?? '') . "\",
                    \"url\": \"" . ($seo['canonical'] ?? '') . "\",
                    \"description\": \"" . ($seo['meta_description'] ?? '') . "\",
                    \"publisher\": {
                        \"@type\": \"Organization\",
                        \"name\": \"" . ($seo['meta_title'] ?? '') . "\"
                    },
                    \"potentialAction\": {
                        \"@type\": \"SearchAction\",
                        \"target\": {
                            \"@type\": \"EntryPoint\",
                            \"urlTemplate\": \"" . ($seo['canonical'] ?? '') . "search?q={search_term_string}\"
                        },
                        \"query-input\": \"required name=search_term_string\"
                    }
                }
            </script>";

            return $schema;
        }

    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [
                '__frontend/resources/style.css'
            ],
            'js' => []
        ];
    }



}