<?php

namespace App\Repositories\Core;

use App\Models\System;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class SystemRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        System $model
    ){
        $this->model = $model;
    }


}
