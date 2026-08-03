<?php

namespace App\Repositories\Core;

use App\Models\Lecturer;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class LecturerRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Lecturer $model
    ){
        $this->model = $model;
    }

    public function lecturerPagination(
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
                $query->where('name', 'LIKE', '%'.$condition['keyword'].'%');
            }
            if(isset($condition['publish']) && $condition['publish'] != 0){
                $query->where('publish', '=', $condition['publish']);
            }
            return $query;
        });
        if(!empty($join)){
            $query->join(...$join);
        }
        return $query->paginate($perPage)
        ->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }

}