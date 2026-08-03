<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Core\ReviewService;
use App\Repositories\Core\ReviewRepository;

class ReviewController extends Controller
{
    protected $reviewService;
    protected $reviewRepository;

    public function __construct(
        ReviewService $reviewService,
        ReviewRepository $reviewRepository,
    ){
        $this->reviewService = $reviewService;
        $this->reviewRepository = $reviewRepository;
    }

    public function index(Request $request){
        $this->authorize('modules', 'review.index');
        $reviews = $this->reviewService->paginate($request);
        $config = [
            'js' => [
                'backend/js/plugins/switchery/switchery.js',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js'
            ],
            'css' => [
                'backend/css/plugins/switchery/switchery.css',
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'model' => 'Review'
        ];
        $config['seo'] = __('messages.review');
        $template = 'backend.review.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'reviews'
        ));
    }

    public function delete($id){
        $this->authorize('modules', 'review.destroy');
        $config['seo'] = __('messages.review');
        $review = $this->reviewRepository->findById($id);
        $template = 'backend.review.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'review',
            'config',
        ));
    }

    public function destroy($id){
        if($this->reviewService->destroy($id)){
            return redirect()->route('review.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('review.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    
    private function config(){
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js',
                'backend/plugins/ckfinder_2/ckfinder.js',
                'backend/library/finder.js',
                'backend/library/widget.js',
                'backend/plugins/ckeditor/ckeditor.js',
            ]
        ];
    }

}
