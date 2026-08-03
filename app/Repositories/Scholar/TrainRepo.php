<?php   
namespace App\Repositories\Scholar;
use App\Repositories\BaseRepository;
use App\Models\ScholarTrain;

class TrainRepo extends BaseRepository {
    protected $model;

    public function __construct(
        ScholarTrain $model
    )   
    {
        $this->model = $model;
    }

}