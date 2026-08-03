<?php

namespace App\Repositories\Core;

use App\Models\Slide;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class SlideRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Slide $model
    ){
        $this->model = $model;
    }

}
