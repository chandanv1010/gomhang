<?php

namespace App\Repositories\Core;

use App\Models\Introduce;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class IntroduceRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Introduce $model
    ){
        $this->model = $model;
    }


}
