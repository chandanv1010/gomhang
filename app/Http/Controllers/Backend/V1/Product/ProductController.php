<?php

namespace App\Http\Controllers\Backend\V1\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\V1\Product\ProductService;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Attribute\AttributeRepository;
use App\Repositories\Attribute\AttributeCatalogueRepository;
use App\Repositories\Core\LecturerRepository;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Classes\Nestedsetbie;
use App\Models\Language;

class ProductController extends Controller
{
    protected $productService;
    protected $productRepository;
    protected $languageRepository;
    protected $language;
    protected $attributeCatalogue;
    protected $attributeRepository;
    protected $lecturerRepository;
    protected $nestedset;

    public function __construct(
        ProductService $productService,
        ProductRepository $productRepository,
        AttributeCatalogueRepository $attributeCatalogue,
        AttributeRepository $attributeRepository,
        LecturerRepository $lecturerRepository,
    ){
        $this->middleware(function($request, $next){
            $locale = app()->getLocale(); // vn en cn
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });

        $this->productService = $productService;
        $this->productRepository = $productRepository;
        $this->attributeCatalogue = $attributeCatalogue;
        $this->attributeRepository = $attributeRepository;
        $this->lecturerRepository = $lecturerRepository;
        $this->initialize();
        
    }

    private function initialize(){
        $this->nestedset = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' =>  $this->language,
        ]);
    } 

    public function index(Request $request){
        $this->authorize('modules', 'product.index');
        $products = $this->productService->paginate($request, $this->language);
        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $template = 'backend.product.product.index';
        $dropdown  = $this->nestedset->Dropdown();
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'products'
        ));
    }

    public function create(){
        $this->authorize('modules', 'product.create');
        $attributeCatalogue = $this->attributeCatalogue->getAll($this->language);
        $lecturers = $this->lecturerRepository->all();
        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $config['method'] = 'create';
        $dropdown  = $this->nestedset->Dropdown();
        $template = 'backend.product.product.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'dropdown',
            'config',
            'attributeCatalogue',
            'lecturers'
        ));
    }
    
    public function store(StoreProductRequest $request)
    {
        $success = $this->productService->create($request, $this->language);

        if ($success) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()->back()->with('success', 'Thêm mới bản ghi thành công');
            }
            return redirect()->route('product.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->back()->with('error', 'Thêm mới bản ghi không thành công. Hãy thử lại');
    }


    public function edit($id, Request $request){
        $this->authorize('modules', 'product.update');
        $product = $this->productRepository->getProductById($id, $this->language);
        $lecturers = $this->lecturerRepository->all();
        $attributeCatalogue = $this->attributeCatalogue->getAll($this->language);
        $queryUrl = $request->getQueryString();
        $config = $this->configData();
        $config['seo'] = __('messages.product');
        $config['method'] = 'edit';
        $dropdown  = $this->nestedset->Dropdown();
        $album = json_decode($product->album);
        $template = 'backend.product.product.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'dropdown',
            'product',
            'album',
            'attributeCatalogue',
            'queryUrl',
            'lecturers'
        ));
    }

    public function update($id, UpdateProductRequest $request)
    {
        $queryString = base64_decode($request->getQueryString());

        if ($this->productService->update($id, $request, $this->language)) {
            if ($request->input('send') == 'send_and_stay') {
                return redirect()
                    ->route('product.edit', [$id, 'query' => base64_encode($queryString)])
                    ->with('success', 'Cập nhật bản ghi thành công');
            }

            return redirect()
                ->route('product.index', $queryString)
                ->with('success', 'Cập nhật bản ghi thành công');
        }

        return redirect()
            ->back()
            ->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }


    public function delete($id){
        $this->authorize('modules', 'product.destroy');
        $config['seo'] = __('messages.product');
        $product = $this->productRepository->getProductById($id, $this->language);
        $template = 'backend.product.product.delete';
        return view('backend.dashboard.layout', compact(
            'template',
            'product',
            'config',
        ));
    }

    public function destroy($id){
        if($this->productService->destroy($id, $this->language)){
            return redirect()->route('product.index')->with('success','Xóa bản ghi thành công');
        }
        return redirect()->route('product.index')->with('error','Xóa bản ghi không thành công. Hãy thử lại');
    }

    private function configData(){
        return [
            'extendJs' => true,
            'model' => 'Product'
        ];
    }

   

}
