<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use Jenssegers\Agent\Facades\Agent;
use Illuminate\Support\Facades\DB;
use App\Models\Contact;
use App\Services\V1\Core\SlideService;

use App\Services\V1\Core\WidgetService;

class ContactController extends FrontendController
{
    protected $language;
    protected $system;
    protected $widgetService;
    protected $slideService;

    public function __construct(
        WidgetService $widgetService,
        SlideService $slideService
    ){
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        parent::__construct(); 
    }


    public function index(Request $request){
        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'showroom-system','object' => true],
            ['keyword' => 'news-outstanding','object' => true],
        ], $this->language);
        $config = $this->config();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Liên Hệ',
            'meta_description' => 'Liên Hệ '.$system['homepage_company'],
            'meta_keyword' => '',
            'meta_image' => '',
            'canonical' => write_url('lien-he')
        ];
        $template = 'frontend.contact.index';
        
        $slides = $this->slideService->getSlide(
            ['main-slide'],
            $this->language
        );
        return view($template, compact(
            'widgets',
            'config',
            'seo',
            'system',
            'slides'
        ));
    }

    public function save(Request $request){
        try {
            DB::beginTransaction();
            $payload = $request->only(['email', 'name', 'phone', 'address', 'message']);
            Contact::create($payload);
            DB::commit();
            return response()->json([
                'message' => 'success',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        
    }

    public function saveContact(Request $request){
        try {
            DB::beginTransaction();
            $payload = $request->only(['email', 'name', 'phone', 'address', 'message']);
            Contact::create($payload);
            DB::commit();
            return redirect()->back()->with('success', 'Gửi đăng ký thành công. Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        
    }

    private function config(){
        return [
            'language' => $this->language,
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/location.js',
                'frontend/core/library/cart.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
            ]
        ];
    }

}
