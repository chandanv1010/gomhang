<?php  
namespace App\Traits;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Router;

trait HasRouter {
    public function createRouterPayload(string $canonical = '', int $modelId, int $languageId, string $controllerName = ''){
        return  [
            'canonical' => Str::slug($canonical),
            'module_id' => $modelId,
            'language_id' => $languageId,
            'controllers' => 'App\Http\Controllers\Frontend\\'.$controllerName.'',
        ];
    }

    public function generatePayloadLanguage(){
        $request = $this->context['request'];
        $payload = [
            $this->context['languageId'] => [
                'name' => $request->input('name'),
                'canonical' => Str::snake($request->input('canonical')),
                'description' => $request->input('description'),
                'content' => $request->input('content'),
                'meta_title' => $request->input('meta_title'),
                'meta_keyword' => $request->input('meta_keyword'),
                'meta_description' => $request->input('meta_description'),
            ]
        ];
        $request->merge(['languages' => $payload]);
    }

    public function generatePayloadRelation(string $relation = '', array $value = []){
        $request = $this->context['request'];
        $request->merge([$relation => $value]);
       
    }

    public function handleRouter(string $controller = ''): void{
        $request = $this->context['request'];
        // $action = $this->context['action'];
        $languageId = $this->context['languageId'];
        $payload = $this->createRouterPayload($request->canonical, $this->model->id, $languageId, $controller);
        $routerRequest = new Request();
        $routerRequest->merge($payload);
        Router::where(['language_id' => $languageId, 'module_id' => $this->context['id'], 'controllers' => $payload['controllers']])->forceDelete();
        $this->routerService->save($routerRequest, 'store');
    }
}