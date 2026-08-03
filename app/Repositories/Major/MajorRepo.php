<?php  
namespace App\Repositories\Major;
use App\Models\Major;
use App\Repositories\BaseRepository;

class MajorRepo  extends BaseRepository{

    protected $model;

    public function __construct(
        Major $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}