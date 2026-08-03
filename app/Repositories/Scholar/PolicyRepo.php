<?php   
namespace App\Repositories\Scholar;
use App\Repositories\BaseRepository;
use App\Models\ScholarPolicy;

class PolicyRepo extends BaseRepository {
    protected $model;

    public function __construct(
        ScholarPolicy $model
    )   
    {
        $this->model = $model;
    }

}