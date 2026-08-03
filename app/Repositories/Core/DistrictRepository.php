<?php

namespace App\Repositories\Core;

use App\Repositories\BaseRepository;
use App\Models\District;
/**
 * Class DistrictService
 * @package App\Services
 */
class DistrictRepository extends BaseRepository 
{
    protected $model;

    public function __construct(
        District $model
    ){
        $this->model = $model;
    }

    public function findDistrictByProvinceId(int $province_id = 0){
        return $this->model->where('province_code','=', $province_id)->get();
    }
   
}
