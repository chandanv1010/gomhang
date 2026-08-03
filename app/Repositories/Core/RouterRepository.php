<?php

namespace App\Repositories\Core;

use App\Repositories\BaseRepository;
use App\Models\Router;
/**
 * Class RouterService
 * @package App\Services
 */
class RouterRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Router $model
    ){
        $this->model = $model;
    }

}
