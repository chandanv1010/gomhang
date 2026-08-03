<?php

namespace App\Repositories\User;

use App\Models\Permission;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class PermissionRepository extends BaseRepository 
{
    protected $model;

    public function __construct(
        Permission $model
    ){
        $this->model = $model;
    }


}
