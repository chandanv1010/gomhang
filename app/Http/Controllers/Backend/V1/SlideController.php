<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Core\SlideService;
use App\Repositories\Core\SlideRepository;

use App\Models\Language;
use App\Http\Requests\Slide\StoreSlideRequest;
use App\Http\Requests\Slide\UpdateSlideRequest;

class SlideController extends Controller
{
    protected $slideService;
    protected $slideRepository;
    protected $language;

    public function __construct(
        SlideService $slideService,
        SlideRepository $slideRepository,
    ){
        $this->slideService = $slideService;
        $this->slideRepository = $slideRepository;
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request){
        $this->authorize('modules', 'slide.index');
        $slides = $this->slideService->paginate($request);
        // dd($slides);
      
        $config = [
            'extendJs' => true,
            'model' => 'Slide'
        ];
        $config['seo'] = __('messages.slide');
        $config['language'] = $this->language;
        $template = 'backend.slide.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slides'
        ));
    }

    public function create(){
        $this->authorize('modules', 'slide.create');
        $config = $this->config();
        $config['seo'] = __('messages.slide');
        $config['method'] = 'create';
        $template = 'backend.slide.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreSlideRequest $request){
        if($this->slideService->create($request, $this->language)){
            return redirect()->route('slide.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'slide.edit');
        $slide = $this->slideRepository->findById($id);
        // item là cột json; slide chưa có ảnh nào thì cột này NULL, truy cập
        // thẳng [$this->language] trên null làm sập màn hình sửa slide.
        $slideItem = $this->slideService->converSlideArray(
            is_array($slide->item) ? ($slide->item[$this->language] ?? []) : []
        );


        $config = $this->config();
        $config['seo'] = __('messages.slide');
        $config['method'] = 'edit';
        $template = 'backend.slide.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'slide',
            'slideItem',
        ));
    }

    public function update($id, UpdateSlideRequest $request){
        if($this->slideService->update($id, $request, $this->language)){
            return redirect()->route('slide.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'slide.destroy');
        $config['seo'] = __('messages.slide');
        $slide = $this->slideRepository->findById($id);
        $template = 'backend.slide.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'slide',
            'config',
        ));
    }

    public function destroy($id){
        if($this->slideService->destroy($id)){
            return redirect()->route('slide.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('slide.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function config(){
        return [
            'extendJs' => true,
        ];
    }

}
