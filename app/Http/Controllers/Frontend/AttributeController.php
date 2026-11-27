<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use App\Services\V1\Product\ProductService;
use App\Repositories\Product\ProductCatalogueRepository;
use App\Support\Schema;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

/**
 * Trang thương hiệu, ví dụ /xiaomi.html.
 *
 * Admin tạo thuộc tính trong nhóm "Thương hiệu" thì hệ thống ghi đường dẫn vào
 * bảng routers với controller này, nhưng controller chưa từng được viết nên mọi
 * trang thương hiệu trả lỗi 500. Trang dùng lại giao diện danh mục sản phẩm:
 * danh sách sản phẩm của thương hiệu, sắp xếp, lọc giá, phân trang.
 */
class AttributeController extends FrontendController
{
    protected $language;
    protected $system;
    protected $productService;
    protected $productCatalogueRepository;

    public function __construct(
        ProductService $productService,
        ProductCatalogueRepository $productCatalogueRepository,
    ) {
        $this->productService = $productService;
        $this->productCatalogueRepository = $productCatalogueRepository;
        parent::__construct();
    }

    public function index($id, $request, $page = 1)
    {
        $brandCatalogueId = (int) config('apps.general.brandAttributeCatalogueId');

        $brand = DB::table('attributes as a')
            ->join('attribute_language as al', 'al.attribute_id', '=', 'a.id')
            ->where('a.id', $id)
            ->where('al.language_id', $this->language)
            ->where('a.publish', 2)
            ->whereNull('a.deleted_at')
            ->select(
                'a.id', 'a.image', 'a.attribute_catalogue_id',
                'al.name', 'al.canonical', 'al.description', 'al.content',
                'al.meta_title', 'al.meta_keyword', 'al.meta_description'
            )
            ->first();

        // Chỉ thuộc tính nhóm thương hiệu mới có trang riêng; màu sắc, dung
        // lượng... không có trang danh sách.
        if (is_null($brand) || (int) $brand->attribute_catalogue_id !== $brandCatalogueId) {
            abort(404);
        }

        $page = max(1, (int) $page);
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });

        // Lọc theo thương hiệu bằng bộ lọc ?brand= sẵn có của ProductService.
        // Dùng bản sao request để tham số này không lọt vào link sắp xếp/lọc giá.
        $listRequest = $request->duplicate();
        $listRequest->merge(['brand' => $brand->id]);
        $products = $this->productService->paginate(
            $listRequest,
            $this->language,
            null,
            $page,
            ['path' => $brand->canonical],
        );
        $productId = $products->pluck('id')->toArray();
        if (count($productId)) {
            $products = $this->productService->combineProductAndPromotion($productId, $products);
            $products = $this->productService->combineProductRelation($products);
        }

        // View danh mục cần một đối tượng "danh mục": dựng từ thương hiệu.
        // parent_id = -1 để không rơi vào nhánh danh mục gốc; id = 0 để không
        // trùng danh mục nào trong ô "Danh mục khác".
        $productCatalogue = (object) [
            'id' => 0,
            'parent_id' => -1,
            'name' => 'Thương hiệu ' . $brand->name,
            'canonical' => $brand->canonical,
            'description' => $brand->description,
            'content' => $brand->content,
        ];

        $allCategories = $this->productCatalogueRepository->all(['languages'])
            ->filter(function ($cat) {
                return $cat->publish == 2;
            })
            ->sortBy('order')
            ->values();

        $system = $this->system;
        $seo = seo((object) [
            'canonical' => $brand->canonical,
            'name' => $productCatalogue->name,
            'meta_title' => $brand->meta_title,
            'meta_keyword' => $brand->meta_keyword,
            'meta_description' => $brand->meta_description
                ?: 'Phụ kiện ' . $brand->name . ' chính hãng tại ' . system_brand($system) . '.',
            'description' => $brand->description,
            'content' => $brand->content,
            'image' => brand_logo($brand) ?? '',
        ], $page);

        $items = [];
        foreach ($products as $product) {
            $items[] = ['name' => $product->name ?? '', 'url' => write_url($product->canonical ?? '')];
        }
        $schema = Schema::script([
            Schema::organization($system),
            Schema::webSite($system),
            Schema::breadcrumb([
                ['name' => 'Trang chủ', 'url' => config('app.url')],
                ['name' => $productCatalogue->name],
            ]),
            Schema::collectionPage([
                'name' => $productCatalogue->name,
                'url' => write_url($brand->canonical),
                'description' => $brand->description ?? '',
            ], $items),
        ]);

        return view('frontend.product.catalogue.index', [
            'config' => $this->config(),
            'seo' => $seo,
            'system' => $system,
            'breadcrumb' => null,
            'productCatalogue' => $productCatalogue,
            'products' => $products,
            'filters' => null,
            'widgets' => [],
            'schema' => $schema,
            'productCatalogues' => [],
            'subCategories' => collect(),
            'chungLoaiList' => collect(),
            'iphoneList' => collect(),
            'samsungList' => collect(),
            'allCategories' => $allCategories,
            'brands' => collect(),
        ]);
    }

    private function config()
    {
        return [
            'language' => $this->language,
            'css' => [
                'frontend/resources/css/custom.css',
            ],
            'js' => [],
        ];
    }
}
