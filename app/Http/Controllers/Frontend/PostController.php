<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Support\Schema;
use Illuminate\Http\Request;
use App\Repositories\Post\PostCatalogueRepository;
use App\Services\V1\Post\PostCatalogueService;
use App\Services\V1\Post\PostService;
use App\Repositories\Post\PostRepository;
use App\Services\V1\Core\WidgetService;

use Jenssegers\Agent\Facades\Agent;
use App\Models\Post;
use App\View\Components\TableOfContents;

class postController extends FrontendController
{
    protected $language;
    protected $system;
    protected $postCatalogueRepository;
    protected $postCatalogueService;
    protected $postService;
    protected $postRepository;
    protected $widgetService;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService,
        PostRepository $postRepository,
        WidgetService $widgetService,
    ){
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->widgetService = $widgetService;
        parent::__construct(); 
    }


    public function index($id, $request){
        $language = $this->language;
        $post = $this->postRepository->getPostById($id, $this->language, config('apps.general.defaultPublish'));
        $viewed = $post->viewed;
        $updateViewed = Post::where('id', $id)->update(['viewed' => $viewed + 1]); 
        if(is_null($post)){
            abort(404);
        }
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($post->post_catalogue_id, $this->language);
        if($postCatalogue->id == 22 || $postCatalogue->id == 24 || $postCatalogue->id === 44){
            $postCatalogue->children = $this->postCatalogueRepository->findByCondition(
                [
                    ['publish' , '=', 2],
                    ['parent_id', '=', 21]
                ],
                true,
                [],
                ['order', 'desc']
            );
        }

        // dd(123);

        $breadcrumb = $this->postCatalogueRepository->breadcrumb($postCatalogue, $this->language);

        $asidePost = $this->postService->paginate(
            $request, 
            $this->language, 
            $postCatalogue, 
            1,
            ['path' => $postCatalogue->canonical],
        );


        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'featured-products'],
            ['keyword' => 'product-category', 'children' => true],
            ['keyword' => 'product-category-highlight', 'object' => true],
            ['keyword' => 'about-us-2'],
            ['keyword' => 'featured-project'],
            ['keyword' => 'homepage-news'],
            ['keyword' => 'homepage-video'],
        ], $this->language);

        /* ------------------- */
        
        $config = $this->config();
        $system = $this->system;
        $seo = seo($post, 1, 'article');
        
        $lastestNews = Post::with(['languages'])->orderBy('order', 'desc')->orderBy('id', 'desc')->where(['publish' => 2])->limit(8)->get();


        $template = 'frontend.post.post.index';

        $schema = $this->schema($post, $postCatalogue, $breadcrumb);
        $content = $post->languages->first()->pivot->content;
        // dd($content);
        // dd($content, $cont);
        $items = TableOfContents::extract($content);
        $contentWithToc = null;
        $contentWithToc = TableOfContents::injectIds($content, $items);
        // dd($contentWithToc);

        return view($template, compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'postCatalogue',
            'post',
            'asidePost',
            'widgets',
            'schema',
            'contentWithToc',
            'lastestNews'
        ));
    }

    /**
     * Structured data for an article. Rebuilt on App\Support\Schema; the
     * hand-written JSON it replaced was syntactically invalid.
     */
    private function schema($post, $postCatalogue, $breadcrumb)
    {
        $language = optional($post->languages->first())->pivot;

        $trail = [['name' => 'Trang chủ', 'url' => config('app.url')]];
        foreach (($breadcrumb ?? []) as $item) {
            $itemLanguage = optional($item->languages->first())->pivot;
            if (!$itemLanguage) {
                continue;
            }
            $trail[] = ['name' => $itemLanguage->name, 'url' => write_url($itemLanguage->canonical)];
        }
        $trail[] = ['name' => $language->name ?? ''];

        return Schema::script([
            Schema::organization($this->system),
            Schema::breadcrumb($trail),
            Schema::article([
                'headline' => $language->name ?? '',
                'url' => write_url($language->canonical ?? ''),
                'image' => image($post->image),
                'description' => $language->description ?? '',
                'datePublished' => $post->created_at ?? null,
                'dateModified' => $post->updated_at ?? null,
                'section' => optional(optional($postCatalogue)->languages->first())->pivot->name ?? '',
            ], $this->system),
        ]);
    }

    private function config(){
        return [
            'language' => $this->language,
            'js' => [
                'frontend/core/library/cart.js',
                'frontend/core/library/product.js',
                'frontend/core/library/review.js',
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.js'
            ],
            'css' => [
                'frontend/core/css/product.css',
                'https://prohousevn.com/scripts/fancybox-3/dist/jquery.fancybox.min.css'
            ]
        ];
    }

}
