<?php

namespace App\Repositories\Menu;

use App\Repositories\BaseRepository;
use App\Models\MenuCatalogue;
/**
 * Class MenuCatalogueService
 * @package App\Services
 */
class MenuCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        MenuCatalogue $model
    ){
        $this->model = $model;
    }


   
}
