<?php

namespace App\Repositories\User;

use App\Models\UserCatalogue;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class UserCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        UserCatalogue $model
    ){
        $this->model = $model;
    }
    
}
