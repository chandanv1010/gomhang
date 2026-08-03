<?php

namespace App\Repositories\Core;

use App\Models\Language;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class LanguageRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Language $model
    ){
        $this->model = $model;
    }


}
