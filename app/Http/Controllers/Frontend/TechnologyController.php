<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Repositories\Core\IntroduceRepository;

class TechnologyController extends FrontendController
{
    protected $introduceRepository;

    public function __construct(
        IntroduceRepository $introduceRepository
    ) {
        $this->introduceRepository = $introduceRepository;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $introduces = convert_array($this->introduceRepository->findByCondition([
            ['language_id', '=', $this->language]
        ], true), 'keyword', 'content');

        $config = [
            'language' => $this->language,
            'css' => [],
            'js' => []
        ];
        $system = $this->system;

        $seo = [
            'meta_title' => $introduces['block_1_banner_title'] ?? 'Công Nghệ Độc Quyền TexGuard',
            'meta_description' => $introduces['block_1_banner_desc'] ?? 'Tìm hiểu công nghệ độc quyền TexGuard của Tazen.',
            'meta_keyword' => 'tazen, cong nghe, texguard',
            'meta_image' => $introduces['block_1_banner_image'] ?? '',
            'canonical' => route('frontend.technology.index')
        ];

        $template = 'frontend.technology.index';

        return view($template, compact(
            'introduces',
            'config',
            'seo',
            'system'
        ));
    }
}
