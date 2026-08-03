<?php

namespace App\Repositories\Core;

use App\Repositories\BaseRepository;
use App\Models\DistributionArea;

class DistributionAreaRepository extends BaseRepository 
{
    protected $model;

    public function __construct(
        DistributionArea $model
    ){
        $this->model = $model;
    }

    public function findAreaByParentId(int $parentId = 0){
        return $this->model->where('parent_id', '=', $parentId)->where('publish', '=', 2)->get();
    }
}
