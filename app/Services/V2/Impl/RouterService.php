<?php   
namespace App\Services\V2\Impl;

use App\Services\V2\BaseService;
use App\Repositories\Core\RouterRepository;
use Illuminate\Support\Facades\Auth;

class RouterService extends BaseService {

    protected $repository;
    protected $fillable;

    public function __construct(
        RouterRepository $repository
    )
    {
        $this->repository = $repository;
    }
    
    public function prepareModelData(): static {
        $request = $this->context['request'] ?? null;
        if(!is_null($request)){
            $this->fillable = $this->repository->getFillable();
            $this->modelData = $request->only($this->fillable);
        }
        return $this;
    }

}