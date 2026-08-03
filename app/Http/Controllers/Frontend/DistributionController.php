<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use App\Models\Distribution;
use App\Models\DistributionArea;

class DistributionController extends FrontendController
{
    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $regions = DistributionArea::where('parent_id', 0)->where('publish', 2)->get();
        
        $selectedRegion = $request->integer('province_id');
        $selectedCity = $request->integer('district_id');
        
        $cities = collect();
        if ($selectedRegion > 0) {
            $cities = DistributionArea::where('parent_id', $selectedRegion)->where('publish', 2)->get();
        }

        $query = Distribution::where('publish', 2);
        if ($selectedRegion > 0) {
            $query->where('province_id', $selectedRegion);
        }
        if ($selectedCity > 0) {
            $query->where('district_id', $selectedCity);
        }
        $distributors = $query->orderBy('name', 'ASC')->get();

        $config = [
            'language' => $this->language,
            'css' => [],
            'js' => []
        ];
        $system = $this->system;
        
        $seo = [
            'meta_title' => 'Hệ Thống Phân Phối',
            'meta_description' => 'Tìm kiếm và xem bản đồ hệ thống đại lý, nhà phân phối chính thức của Tazen trên toàn quốc.',
            'meta_keyword' => 'tazen, he thong phan phoi, dai ly tazen, nha phan phoi tazen',
            'meta_image' => '',
            'canonical' => route('frontend.distribution.index')
        ];

        $template = 'frontend.distribution.index';

        return view($template, compact(
            'regions',
            'cities',
            'distributors',
            'selectedRegion',
            'selectedCity',
            'config',
            'seo',
            'system'
        ));
    }

    public function getProvinces(Request $request)
    {
        $parentId = $request->integer('parent_id');
        $areas = DistributionArea::where('parent_id', $parentId)->where('publish', 2)->orderBy('name', 'ASC')->get();
        return response()->json($areas);
    }
}
