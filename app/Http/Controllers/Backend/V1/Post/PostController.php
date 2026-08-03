<?php

namespace App\Http\Controllers\Backend\V1\Post;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Post\PostService;
use App\Repositories\Post\PostRepository;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class PostController extends Controller
{
    protected $postService;
    protected $postRepository;
    protected $languageRepository;
    protected $language;
    protected $nestedset;

    public function __construct(
        PostService $postService,
        PostRepository $postRepository,
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->initialize();
        
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 

    public function index(Request $request){
        $this->authorize('modules', 'post.index');
        $posts = $this->postService->paginate($request, $this->language);
        $config = [
            'extendJs' => true,
            'model' => 'Post'
        ];
        $config['seo'] = __('messages.post');
        $template = 'backend.post.post.index';
        $dropdown  = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'posts'
        ));
    }

    public function create(){
        $this->authorize('modules', 'post.create');
        $config = $this->configData();
        $config['seo'] = __('messages.post');
        $config['method'] = 'create';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'dropdown',
            'config',
        ));
    }

    public function store(StorePostRequest $request)
    {
        $success = $this->postService->create($request, $this->language);

        if ($success) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()->back()->with('success', 'Thêm mới bản ghi thành công');
            }
            return redirect()->route('post.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->back()->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }

    public function edit($id){
        $this->authorize('modules', 'post.update');
        $post = $this->postRepository->getPostById($id, $this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.post');
        $config['method'] = 'edit';
        $dropdown  = $this->nestedset->Dropdown();
        $album = json_decode($post->album);
        $template = 'backend.post.post.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'post',
            'album',
        ));
    }

    public function update($id, UpdatePostRequest $request)
    {
        $queryString = base64_decode($request->getQueryString());

        if ($this->postService->update($id, $request, $this->language)) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()
                    ->route('post.edit', [$id, 'query' => base64_encode($queryString)])
                    ->with('success', 'Cập nhật bản ghi thành công');
            }

            return redirect()
                ->route('post.index', $queryString)
                ->with('success', 'Cập nhật bản ghi thành công');
        }

        return redirect()
            ->back()
            ->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    public function delete($id){
        $this->authorize('modules', 'post.destroy');
        $config['seo'] = __('messages.post');
        $post = $this->postRepository->getPostById($id, $this->language);
        $template = 'backend.post.post.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'post',
            'config',
        ));
    }

    public function destroy($id){
        if($this->postService->destroy($id)){
            return redirect()->route('post.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('post.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData(){
        return [
           'extendJs' => true
        ];
    }

   

}
