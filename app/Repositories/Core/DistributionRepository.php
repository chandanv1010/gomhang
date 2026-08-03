<?php

namespace App\Repositories\Core;

use App\Models\Distribution;
use App\Repositories\BaseRepository;

class DistributionRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Distribution $model
    ){
        $this->model = $model;
    }

    public function distributionPagination(
        array $column = ['*'], 
        array $condition = [], 
        int $perPage = 1,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
    ){
        $query = $this->model->select($column)->where(function($query) use ($condition){
            if(isset($condition['keyword']) && !empty($condition['keyword'])){
                $query->where('name', 'LIKE', '%'.$condition['keyword'].'%')
                      ->orWhere('phone', 'LIKE', '%'.$condition['keyword'].'%')
                      ->orWhere('address', 'LIKE', '%'.$condition['keyword'].'%');
            }
            if(isset($condition['publish']) && $condition['publish'] != 0){
                $query->where('publish', '=', $condition['publish']);
            }
            if(isset($condition['province_id']) && $condition['province_id'] != 0){
                $query->where('province_id', '=', $condition['province_id']);
            }
            if(isset($condition['district_id']) && $condition['district_id'] != 0){
                $query->where('district_id', '=', $condition['district_id']);
            }
            return $query;
        });
        if(!empty($join)){
            $query->join(...$join);
        }
        if(!empty($relations)){
            $query->with($relations);
        }
        $query->orderBy($orderBy[0], $orderBy[1]);
        return $query->paginate($perPage)
            ->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }
}
