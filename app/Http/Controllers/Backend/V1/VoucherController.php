<?php

namespace App\Http\Controllers\Backend\V1;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Product\VoucherService;
use App\Repositories\Product\VoucherRepository;

use App\Http\Requests\Voucher\StoreVoucherRequest;
use App\Http\Requests\Voucher\UpdateVoucherRequest;

class VoucherController extends Controller
{

    protected $voucherService;
    protected $voucherRepository;

    public function __construct(
        VoucherService $voucherService,
        VoucherRepository $voucherRepository
    ){
        $this->voucherService = $voucherService;
        $this->voucherRepository = $voucherRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'voucher.index');
        $vouchers = $this->voucherService->paginate($request);
        $config = $this->config();
        $config['seo'] = __('messages.voucher');
        $template = 'backend.voucher.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'vouchers',
        ));
    }

    public function create(){
        $this->authorize('modules', 'voucher.create');
        $config = $this->config();
        $config['seo'] = __('messages.voucher');
        $config['method'] = 'create';
        $config['method'] = 'create';
        $template = 'backend.voucher.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreVoucherRequest $request){
        if($this->voucherService->create($request)){
            return redirect()->route('voucher.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('voucher.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'voucher.update');
        $voucher = $this->voucherRepository->findById($id);
        $object = null;
        if(isset($voucher->voucher_products)){
            foreach($voucher->voucher_products as $k => $v){
                $object['id'][$k] = $v->id;
                $object['name'][$k] = $v->languages->first()->pivot->name;
            }
        }
        $config = $this->config();
        $config['seo'] = __('messages.voucher');
        $config['method'] = 'edit';
        $template = 'backend.voucher.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'voucher',
            'object'
        ));
    }

    public function update($id, UpdateVoucherRequest $request){
        if($this->voucherService->update($id, $request)){
            return redirect()->route('voucher.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('voucher.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'voucher.destroy');
        $config['seo'] = __('messages.voucher');
        $config['method'] = 'delete';
        $voucher = $this->voucherRepository->findById($id);
        $template = 'backend.voucher.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'voucher',
            'config',
        ));
    }

    public function destroy($id){
        if($this->voucherService->destroy($id)){
            return redirect()->route('voucher.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('voucher.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function config(){
        return [
            'css' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.min.css'
            ],
            'js' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/datetimepicker-master/build/jquery.datetimepicker.full.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/voucher.js',
            ],
            'model' => 'Voucher'
        ];
    }

}
