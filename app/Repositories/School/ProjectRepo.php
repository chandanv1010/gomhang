<?php  
namespace App\Repositories\School;
use App\Models\SchoolProject;
use App\Repositories\BaseRepository;

class ProjectRepo  extends BaseRepository{

    protected $model;

    public function __construct(
        SchoolProject $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}