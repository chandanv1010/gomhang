<?php

namespace App\Repositories\User;

use App\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Province;
/**
 * Class ProvinceService
 * @package App\Services
 */
class ProvinceRepository extends BaseRepository
{
    protected $model;
   
    public function __construct(
        Province $model
    ){
        $this->model = $model;
    }
   
}
