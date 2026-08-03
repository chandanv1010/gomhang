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
        
        $seo = [
            'meta_title' => 'Về Chúng Tôi',
            'meta_description' => 'Tìm hiểu thêm về VIREX - thương hiệu thiết bị, vật tư công trình chất lượng hàng đầu Việt Nam.',
            'meta_keyword' => 'virex, ong nuoc virex, ong inox virex, thiet bi ve sinh, vat tu cong trinh',
            'meta_image' => '',
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
