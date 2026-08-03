<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\V2\Impl\RouterService;
use App\Traits\HasRouter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CrawlerController extends Controller
{
  
    use HasRouter;

    protected $routerService;

    public function __construct(
        RouterService $routerService
    ){
        $this->routerService = $routerService;

    }

    public function crawl(){
        $items = file_get_contents("http://duchanhhandicraft.com/homepage/home/getData");
        $items = json_decode($items, TRUE);
        

        $payload = [];
        $payloadLanguage = [];
        $router = [];
        $catalogue = [];
        if(count($items)){
            foreach($items as $key => $val){
                $payload[] = [
                    'id' => $val['id'],
                    'product_catalogue_id' => $val['cataloguesid'],
                    'order' => $val['order'],
                    'image' => $val['images'],
                    'code' => $val['code'],
                    'price' => $val['price'],
                    'order' => $val['order'],
                    'publish' => 2,
                    'user_id' => 4504,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];


                $catalogueO = json_decode($val['catalogues']);

                foreach($catalogueO as $c){
                    $catalogue[] = [
                        'product_id' => $val['id'],
                        'product_catalogue_id' => $c 
                    ];
                }

                $canonical = empty($val['canonical']) ? Str::slug($val['title']) : $val['canonical'];
                $payloadLanguage[] = [
                    'product_id' => $val['id'],
                    'language_id' => 1,
                    'name' => $val['title'],
                    'canonical' => $canonical,
                    'description' => $val['description'],
                    'content' => $val['content'] ?? null,
                    'meta_title' => $val['meta_title'],
                    'meta_description' => $val['meta_description'],
                    'meta_keyword' => $val['meta_keyword'],
                ];
                

                $router[] = $this->createRouterPayload($canonical, $val['id'], 1, 'ProductController');
            }
        }
        

        // dd($router);
        // DB::beginTransaction();
        // try {
        //     DB::table('products')->insert($payload);
        //     DB::table('product_language')->insert($payloadLanguage);
        //     DB::table('product_catalogue_product')->insert($catalogue);
        //     DB::table('routers')->insert($router);
        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        //     dd($th->getMessage());
        // }
        
    }
  

}
