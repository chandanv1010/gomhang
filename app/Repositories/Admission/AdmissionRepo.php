<?php   
namespace App\Repositories\Admission;
use App\Repositories\BaseRepository;
use App\Models\Admission;

class AdmissionRepo extends BaseRepository {
    protected $model;

    public function __construct(
        Admission $model
    )
    {
        $this->model = $model;
    }

}