<?php   
namespace App\Repositories\Scholar;
use App\Repositories\BaseRepository;

use App\Models\Scholar;

class ScholarRepo extends BaseRepository {
    protected $model;

    public function __construct(
        Scholar $model
    )
    {
        $this->model = $model;
    }

}