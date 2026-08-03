<?php  
namespace App\Repositories\Major;
use App\Models\MajorGroup;
use App\Repositories\BaseRepository;

class MajorGroupRepo  extends BaseRepository{

    protected $model;

    public function __construct(
        MajorGroup $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}