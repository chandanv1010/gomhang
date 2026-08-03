<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Core\LecturerService;
use App\Repositories\Core\LecturerRepository;

use App\Http\Requests\Lecturer\StoreLecturerRequest;
use App\Http\Requests\Lecturer\UpdateLecturerRequest;

class LecturerController extends Controller
{
    protected $lecturerService;
    protected $lecturerRepository;

    public function __construct(
        LecturerService $lecturerService,
        LecturerRepository $lecturerRepository,
    ){
        $this->lecturerService = $lecturerService;
        $this->lecturerRepository = $lecturerRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'lecturer.index');
        $lecturers = $this->lecturerService->paginate($request);
        $config = $this->config();
        $config['seo'] = __('messages.lecturer');
        $template = 'backend.lecturer.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'lecturers',
        ));
    }

    public function create(){
        $this->authorize('modules', 'lecturer.create');
        $config = $this->config();
        $config['seo'] = __('messages.lecturer');
        $config['method'] = 'create';
        $template = 'backend.lecturer.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
        ));
    }

    public function store(StoreLecturerRequest $request){
        if($this->lecturerService->create($request)){
            return redirect()->route('lecturer.index')->with('success','Thêm mới bản ghi thành công');
        }
        return redirect()->route('lecturer.index')->with('error','Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'lecturer.update');
        $lecturer = $this->lecturerRepository->findById($id);
        $config = $this->config();
        $config['seo'] = __('messages.lecturer');
        $config['method'] = 'edit';
        $template = 'backend.lecturer.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'lecturer',
        ));
    }

    public function update($id, UpdateLecturerRequest $request){
        if($this->lecturerService->update($id, $request)){
            return redirect()->route('lecturer.index')->with('success','Cập nhật bản ghi thành công');
        }
        return redirect()->route('lecturer.index')->with('error','Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function delete($id){
        $this->authorize('modules', 'lecturer.destroy');
        $config['seo'] = __('messages.lecturer');
        $lecturer = $this->lecturerRepository->findById($id);
        $template = 'backend.lecturer.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'lecturer',
            'config',
        ));
    }

    public function destroy($id){
        if($this->lecturerService->destroy($id)){
            return redirect()->route('lectuter.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('lectuter.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
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
                'backend/plugins/ckeditor/ckeditor.js',
            ],
            'model' => 'Lecturer'
        ];
    }

}


