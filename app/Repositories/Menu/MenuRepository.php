<?php

namespace App\Repositories\Menu;

use App\Repositories\BaseRepository;
use App\Models\Menu;
/**
 * Class MenuService
 * @package App\Services
 */
class MenuRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Menu $model
    ){
        $this->model = $model;
    }


   
}
