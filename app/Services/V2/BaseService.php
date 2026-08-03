<?php  
namespace App\Services\V2;
use Illuminate\Http\Request;
use App\Traits\HasTransaction;
use App\Traits\HasQueryBuilder;

use App\Services\V2\Interfaces\BaseServiceInterface;

abstract class BaseService implements BaseServiceInterface{

    use HasTransaction, HasQueryBuilder;

    protected $repository;
    protected $model;
    protected $modelData;
    protected $result;

    protected $context;


    protected $defaultSort = ['id', 'desc'];
    protected $perpage = 20;
    protected $searchField = ['name'];
    protected $simpleFilters = ['publish']; // DEFAULT
    protected $complexFilters = [];

    protected $with = [];

    public function __construct(
        $repository
    ){
        $this->repository = $repository;
    }

    public function findById($id){
        // $fillable = $this->repository->getFillable();
        return $this->repository->findById($id, ['*'], $this->with);
    }

    protected abstract function prepareModelData(): static;

    private function setContext($context = null): static{
        $this->context = $context;
        return $this;
    }

    private function getContext(){
        return $this->context;
    }


    public function pagination(Request $request){
        $specifications = $this->specifications($request);
        $this->result = $this->repository->customPagination($specifications);
        return $this->result;
    }


    public function save(Request $request,  string $action = 'store', ?int $id = null){
        $context = ['action' => $action, 'id' => $id, 'request' => $request, 'languageId' => config('app.language_id')];
        return $this->beginTransaction()
        ->setContext($context)
        ->prepareModelData()
        ->beforeSave()
        ->saveModel()
        ->withRelation()
        ->afterSave()
        ->commit()
        ->getResult();
    }

    protected function saveModel(?array $agrs = []): static{
        $action = $this->context['action'];
        $id = $this->context['id'];
        $this->model = match ($action) {
            'store' => $this->repository->create($this->modelData),
            'update' => $this->repository->update($id, $this->modelData),
        };
        $this->result = $this->model;
        return $this;
    }


    public function destroy($id){
        return $this->beginTransaction()
        ->beforeDestroy()
        ->destroyModel($id)
        ->commit()
        ->afterDestroy()
        ->getResult();
    }

    protected function destroyModel($id): static {
        $this->result = $this->repository->delete($id);
        return $this;
    }


    public function getResult(){
        return $this->result;
    }

    public function all(array $relation = [], string $selectRaw = ''){
        return $this->repository->all($relation, $selectRaw);
    }

    public function convertDateSelectBox(){
        $temp = [];
        $data =  $this->repository->all(['languages']);
        if(!empty($data)){
            foreach($data as $item){
                $temp[$item->id] = $item->languages->first()->pivot->name;
            }
        }
        return $temp;
    }

    public function getCatalogueChildren($catalogue = null, $request){
        $customRequest = $request->merge(
            [
                'lft' => [
                    'gte' => $catalogue->lft,
                ],
                'rgt' => [
                    'lte' => $catalogue->rgt
                ],
                'type' => 'all'
            ]
        );


        $children = $this->pagination($customRequest);

        return $children;
    }

}