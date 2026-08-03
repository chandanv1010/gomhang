<?php  
namespace App\Repositories\School;
use App\Models\SchoolArea;
use App\Repositories\BaseRepository;

class AreaRepo  extends BaseRepository{

    protected $model;

    public function __construct(
        SchoolArea $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}