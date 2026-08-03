<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\Introduce;


use App\Services\V1\Core\IntroduceService;
use App\Repositories\Core\IntroduceRepository;

use App\Models\Language;

class IntroduceController extends Controller
{
    protected $introduceLibrary;
    protected $introduceService;
    protected $introduceRepository;
    protected $language;

    public function __construct(
        Introduce $introduceLibrary,
        IntroduceService $introduceService,
        IntroduceRepository $introduceRepository,

    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
        $this->introduceLibrary = $introduceLibrary;
        $this->introduceService = $introduceService;
        $this->introduceRepository = $introduceRepository;
    }

    public function index(){
        
        $introduceConfig = $this->introduceLibrary->config();
        $introduces = convert_array($this->introduceRepository->findByCondition(
            [
                ['language_id', '=', $this->language]
            ], TRUE
        ), 'keyword', 'content');
        
        $config = $this->config();
        $config['seo'] = __('messages.introduce');
        $template = 'backend.introduce.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'introduceConfig',
            'introduces',
        ));
    }

    public function store(Request $request){
        if($this->introduceService->save($request, $this->language)){
            return redirect()->route('introduce.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('introduce.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function translate($languageId = 0){

        $introduceConfig = $this->introduceLibrary->config();
        $introduces = convert_array($this->introduceRepository->findByCondition(
            [
                ['language_id', '=', $languageId]
            ], TRUE
        ), 'keyword', 'content');
        $config = $this->config();
        $config['seo'] = __('messages.introduce');
        $config['method'] = 'translate';
        $template = 'backend.introduce.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'introduceConfig',
            'languageId',
            'introduces',
        ));
    }

    public function saveTranslate(Request $request, $languageId){
        if($this->introduceService->save($request, $languageId)){
            return redirect()->route('introduce.translate', ['languageId' => $languageId])->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('introduce.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }
    
    private function config(){
        return [
            'extendJs' => true
        ];
    }

}

