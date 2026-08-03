<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\V1\Core\DistributionAreaService;
use App\Repositories\Core\DistributionAreaRepository;
use App\Http\Requests\DistributionArea\StoreDistributionAreaRequest;

class DistributionAreaController extends Controller
{
    protected $distributionAreaService;
    protected $distributionAreaRepository;

    public function __construct(
        DistributionAreaService $distributionAreaService,
        DistributionAreaRepository $distributionAreaRepository
    ){
        $this->distributionAreaService = $distributionAreaService;
        $this->distributionAreaRepository = $distributionAreaRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'distribution.area.index');
        $areas = $this->distributionAreaService->paginate($request);
        $config = $this->config();
        $config['seo'] = __('messages.distributionArea');
        $template = 'backend.distribution_area.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'areas',
        ));
    }

    public function create(){
        $this->authorize('modules', 'distribution.area.create');
        $parents = $this->distributionAreaRepository->findAreaByParentId(0);
        $config = $this->config();
        $config['seo'] = __('messages.distributionArea');
        $config['method'] = 'create';
        $template = 'backend.distribution_area.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'parents',
        ));
    }

    public function store(StoreDistributionAreaRequest $request){
        if($this->distributionAreaService->create($request)){
            return redirect()->route('distribution.area.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('distribution.area.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'distribution.area.update');
        $area = $this->distributionAreaRepository->findById($id);
        $parents = $this->distributionAreaRepository->findAreaByParentId(0);
        $config = $this->config();
        $config['seo'] = __('messages.distributionArea');
        $config['method'] = 'edit';
        $template = 'backend.distribution_area.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'area',
            'parents',
        ));
    }

    public function update($id, StoreDistributionAreaRequest $request){
        if($this->distributionAreaService->update($id, $request)){
            return redirect()->route('distribution.area.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('distribution.area.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'distribution.area.destroy');
        $config['seo'] = __('messages.distributionArea');
        $area = $this->distributionAreaRepository->findById($id);
        $template = 'backend.distribution_area.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'area',
            'config',
        ));
    }

    public function destroy($id){
        if($this->distributionAreaService->destroy($id)){
            return redirect()->route('distribution.area.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('distribution.area.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    public function getArea(Request $request){
        $parentId = $request->integer('parent_id');
        $areas = $this->distributionAreaRepository->findAreaByParentId($parentId);
        return response()->json($areas);
    }

    private function config(){
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/css/plugins/switchery/switchery.css',
            ],
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/library/location.js',
            ],
            'model' => 'DistributionArea'
        ];
    }
}
