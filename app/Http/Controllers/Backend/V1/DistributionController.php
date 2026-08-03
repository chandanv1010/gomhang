<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\V1\Core\DistributionService;
use App\Repositories\Core\DistributionRepository;
use App\Repositories\Core\DistributionAreaRepository;
use App\Http\Requests\Distribution\StoreDistributionRequest;

class DistributionController extends Controller
{
    protected $distributionService;
    protected $distributionRepository;
    protected $distributionAreaRepository;

    public function __construct(
        DistributionService $distributionService,
        DistributionRepository $distributionRepository,
        DistributionAreaRepository $distributionAreaRepository
    ){
        $this->distributionService = $distributionService;
        $this->distributionRepository = $distributionRepository;
        $this->distributionAreaRepository = $distributionAreaRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'distribution.index');
        $distributions = $this->distributionService->paginate($request);
        $regions = $this->distributionAreaRepository->findAreaByParentId(0);
        $config = $this->config();
        $config['seo'] = __('messages.distribution');
        $template = 'backend.distribution.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'distributions',
            'regions',
        ));
    }

    public function create(){
        $this->authorize('modules', 'distribution.create');
        $regions = $this->distributionAreaRepository->findAreaByParentId(0);
        $config = $this->config();
        $config['seo'] = __('messages.distribution');
        $config['method'] = 'create';
        $template = 'backend.distribution.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'regions',
        ));
    }

    public function store(StoreDistributionRequest $request){
        if($this->distributionService->create($request)){
            return redirect()->route('distribution.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('distribution.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'distribution.update');
        $distribution = $this->distributionRepository->findById($id);
        $regions = $this->distributionAreaRepository->findAreaByParentId(0);
        $areas = $this->distributionAreaRepository->findAreaByParentId($distribution->province_id);
        $config = $this->config();
        $config['seo'] = __('messages.distribution');
        $config['method'] = 'edit';
        $template = 'backend.distribution.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'distribution',
            'regions',
            'areas',
        ));
    }

    public function update($id, StoreDistributionRequest $request){
        if($this->distributionService->update($id, $request)){
            return redirect()->route('distribution.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('distribution.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'distribution.destroy');
        $config['seo'] = __('messages.distribution');
        $distribution = $this->distributionRepository->findById($id);
        $template = 'backend.distribution.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'distribution',
            'config',
        ));
    }

    public function destroy($id){
        if($this->distributionService->destroy($id)){
            return redirect()->route('distribution.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('distribution.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
            ],
            'model' => 'Distribution'
        ];
    }
}
