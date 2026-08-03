<?php  
namespace App\Repositories\School;
use App\Models\School;
use App\Repositories\BaseRepository;

class SchoolRepo  extends BaseRepository{

    protected $model;

    public function __construct(
        School $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}