<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Support\Schema;
use App\Repositories\Post\PostCatalogueRepository;
use App\Services\V1\Post\PostCatalogueService;
use App\Services\V1\Post\PostService;
use App\Services\V1\Core\WidgetService;
use App\Services\V1\Core\SlideService;

use App\Models\System;
use App\Enums\SlideEnum;
use Jenssegers\Agent\Facades\Agent;
use App\Models\Introduce;
use App\Models\Post;

class PostCatalogueController extends FrontendController
{
    protected $language;
    protected $system;
    protected $postCatalogueRepository;
    protected $postCatalogueService;
    protected $postService;
    protected $widgetService;
    protected $slideService;

    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        PostCatalogueService $postCatalogueService,
        PostService $postService,
        WidgetService $widgetService,
        SlideService $slideService,
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->postCatalogueService = $postCatalogueService;
        $this->postService = $postService;
        $this->widgetService = $widgetService;
        $this->slideService = $slideService;
        parent::__construct();
    }


    public function index($id, $request, $page = 1)
    {
        $postCatalogue = $this->postCatalogueRepository->getPostCatalogueById($id, $this->language);
        if ($postCatalogue && $postCatalogue->canonical === 've-chung-toi') {
            abort(404);
        }
        $postCatalogue->children = $this->postCatalogueRepository->findByCondition(
            [
                ['publish', '=', 2],
                ['parent_id', '=', $postCatalogue->id]
            ],
            true,
            [],
            ['order', 'desc']
        );
        
        $breadcrumb = $this->postCatalogueRepository->breadcrumb($postCatalogue, $this->language);
        $posts = $this->postService->paginate(
            $request,
            $this->language,
            $postCatalogue,
            $page,
            ['path' => $postCatalogue->canonical],
            ['posts.recommend', 'desc']
        );

        // dd($posts->toArray());

        $featuredPost = $this->postCatalogueRepository->getFeaturedPost($postCatalogue);

        $widgets = $this->widgetService->getWidget([
            ['keyword' => 'featured-products'],
            ['keyword' => 'product-category', 'children' => true],
            ['keyword' => 'product-category-highlight', 'object' => true],
            ['keyword' => 'about-us-2'],
            ['keyword' => 'featured-project'],
            ['keyword' => 'homepage-news'],
            ['keyword' => 'homepage-video'],
        ], $this->language);

        $slides = $this->slideService->getSlide(
            [SlideEnum::MAIN],
            $this->language
        );
        $lastestNews = Post::with(['languages'])->orderBy('order', 'desc')->orderBy('id', 'desc')->where(['publish' => 2])->limit(8)->get();
        // dd($lastestNews);

        if($postCatalogue->canonical === 've-chung-toi'){
            $template = 'frontend.post.catalogue.intro';
        }else{
            $template = 'frontend.post.catalogue.index';
        }

        $config = $this->config();
        $system = $this->system;
        $seo = seo($postCatalogue, $page);
        $introduce = convert_array(Introduce::where('language_id', $this->language)->get(), 'keyword', 'content');
        $schema = $this->schema($postCatalogue, $posts, $breadcrumb);
        return view($template, compact(
            'config',
            'seo',
            'system',
            'breadcrumb',
            'postCatalogue',
            'posts',
            'widgets',
            'schema',
            'slides',
            'introduce',
            'lastestNews'
        ));
    }

    /**
     * Structured data for a post listing. Rebuilt on App\Support\Schema; the
     * hand-written JSON it replaced was syntactically invalid.
     */
    private function schema($postCatalogue, $posts, $breadcrumb)
    {
        $language = optional($postCatalogue->languages->first())->pivot;

        $trail = [['name' => 'Trang chủ', 'url' => config('app.url')]];
        foreach (($breadcrumb ?? []) as $item) {
            $itemLanguage = optional($item->languages->first())->pivot;
            if (!$itemLanguage) {
                continue;
            }
            $trail[] = ['name' => $itemLanguage->name, 'url' => write_url($itemLanguage->canonical)];
        }
        if (count($trail) > 1) {
            unset($trail[count($trail) - 1]['url']);
        }

        $items = [];
        foreach ($posts as $post) {
            $postLanguage = optional($post->languages->first())->pivot;
            $items[] = [
                'name' => $postLanguage->name ?? ($post->name ?? ''),
                'url' => write_url($postLanguage->canonical ?? ($post->canonical ?? '')),
            ];
        }

        return Schema::script([
            Schema::organization($this->system),
            Schema::breadcrumb($trail),
            Schema::collectionPage([
                'name' => $language->name ?? '',
                'url' => write_url($language->canonical ?? ''),
                'description' => $language->description ?? '',
            ], $items),
        ]);
    }

   


    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.carousel.min.css',
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/assets/owl.theme.default.min.css',
                'frontend/resources/css/custom.css'
            ],
            'js' => [
                'frontend/resources/plugins/OwlCarousel2-2.3.4/dist/owl.carousel.min.js',
                'frontend/resources/library/js/carousel.js',
                'https://getuikit.com/v2/src/js/components/sticky.js'
            ]
        ];
    }

}