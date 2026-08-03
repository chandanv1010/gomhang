<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Core\WidgetService;
use App\Repositories\Core\WidgetRepository;
use App\Repositories\Core\LanguageRepository;

use App\Http\Requests\Widget\StoreWidgetRequest;
use App\Http\Requests\Widget\UpdateWidgetRequest;

use App\Models\Language;
use Illuminate\Support\Collection;

class WidgetController extends Controller
{
    protected $widgetService;
    protected $widgetRepository;
    protected $languageRepository;
    protected $language;

    public function __construct(
        WidgetService $widgetService,
        WidgetRepository $widgetRepository,
        LanguageRepository $languageRepository,
    ){
        $this->widgetService = $widgetService;
        $this->widgetRepository = $widgetRepository;
        $this->languageRepository = $languageRepository;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request){
        $this->authorize('modules', 'widget.index');
        $widgets = $this->widgetService->paginate($request);
      
        $config = [
            'model' => 'Widget',
            'extendJs' => true,
        ];
        $config['seo'] = __('messages.widget');
        $template = 'backend.widget.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widgets'
        ));
    }

    public function create(){
        $this->authorize('modules', 'widget.create');
        $config = $this->config();
        $config['seo'] = __('messages.widget');
        $config['method'] = 'create';
        $template = 'backend.widget.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            
        ));
    }

    public function store(StoreWidgetRequest $request){
        if($this->widgetService->create($request, $this->language)){
            return redirect()->route('widget.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    private function menuItemAgrument(array $whereIn = []){
        $language = $this->language;
        return [
            'condition' => [],
            'flag' => true,
            'relation' => [
                'languages' => function($query) use ($language) {
                    $query->where('language_id',  $language);
                }
            ],
            'orderBy' => ['id', 'desc'],
            'param' => [
                'whereIn' => $whereIn,
                'whereInField' => 'id'
            ]
        ];
    }

    public function edit($id){
        $this->authorize('modules', 'widget.update');
        $widget = $this->widgetRepository->findById($id);
        $widget->description = $widget->description[$this->language];
        $modelClass = loadClass($widget->model);

        $widgetItem = [];

        if($widget->model_id != null){
            $widgetItem = convertArrayByKey($modelClass->findByCondition(
                ...array_values($this->menuItemAgrument($widget->model_id))
            ), ['id','name.languages', 'image']);
        }

        
        $config = $this->config();

        $config['seo'] = __('messages.widget');
        $config['method'] = 'edit';
        $template = 'backend.widget.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'widget',
            'widgetItem',
        ));
    }

    public function update($id, UpdateWidgetRequest $request){
        if($this->widgetService->update($id, $request, $this->language)){
            return redirect()->route('widget.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'widget.destroy');
        $config['seo'] = __('messages.widget');
        $widget = $this->widgetRepository->findById($id);
        $template = 'backend.widget.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'widget',
            'config',
        ));
    }

    public function destroy($id){
        if($this->widgetService->destroy($id)){
            return redirect()->route('widget.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('widget.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function translate($languageId, $widgetId){
        $this->authorize('modules', 'widget.translate');
        $widget = $this->widgetRepository->findById($widgetId);
        $widget->jsonDescription = $widget->description;
        $widget->description = $widget->description[$this->language];

        $widgetTranslate = new \stdClass;
        $widgetTranslate->description = ($widget->jsonDescription[$languageId]) ?? '';


        $translate = $this->languageRepository->findById($languageId);
        $config = $this->config();
        $config['seo'] = __('messages.widget');
        $config['method'] = 'create';
        $template = 'backend.widget.translate';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'translate',
            'widget',
            'widgetTranslate',
        ));
    }

    public function saveTranslate(Request $request){
        if($this->widgetService->saveTranslate($request, $this->language)){
            return redirect()->route('widget.index')->with('success','Tạo bản dịch thành công');
        }
        return redirect()->route('widget.index')->with('error','Tạo bản dịch không thành công. Hãy thử lại');
    }

    private function config(){
        return [
            'extendJs' => true
        ];
    }

}
