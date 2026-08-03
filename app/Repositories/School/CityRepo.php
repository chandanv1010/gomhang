<?php  
namespace App\Repositories\School;
use App\Models\SchoolCity;
use App\Repositories\BaseRepository;

class CityRepo  extends BaseRepository{

    protected $model;

    public function __construct(
        SchoolCity $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}