<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Services\V1\Core\WidgetService;
use App\Services\V1\Core\SlideService;

use App\Models\Introduce;

class AboutController extends FrontendController
{
    protected $language;
    protected $system;
    protected $widgetService;
    protected $slideService;

    public function __construct(
        WidgetService $widgetService,
        SlideService $slideService
    ) {
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'homepage-news', 'object' => true],
            ['keyword' => 'featured-project', 'object' => true],
            ['keyword' => 'feedback', 'object' => true],
            ['keyword' => 'about-us', 'object' => true],
        ], $this->language);

        $config = $this->config();
        $system = $this->system;
        
        // SEO trang này trước đây ghi cứng nội dung của VIREX (ống nước inox,
        // vật tư công trình) - thương hiệu và ngành hàng hoàn toàn khác, sót lại
        // từ template gốc. Dựng từ tên thương hiệu trong admin.
        $brand = system_brand($system);

        $seo = [
            'meta_title' => 'Về Chúng Tôi - ' . $brand,
            'meta_description' => trim((string) ($system['seo_meta_description'] ?? ''))
                ?: 'Tìm hiểu thêm về ' . $brand . ' - địa chỉ mua sỉ lẻ phụ kiện công nghệ chính hãng.',
            'meta_keyword' => trim((string) ($system['seo_meta_keyword'] ?? '')),
            'meta_image' => $system['homepage_logo'] ?? '',
            'canonical' => write_url('gioi-thieu')
        ];

        $template = 'frontend.about.index';

        $slides = $this->slideService->getSlide(
            ['main-slide'],
            $this->language
        );

        $introduces = convert_array(Introduce::where('language_id', $this->language)->get(), 'keyword', 'content');

        return view($template, compact(
            'widgets',
            'config',
            'seo',
            'system',
            'slides',
            'introduces'
        ));
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [],
            'js' => []
        ];
    }
}
