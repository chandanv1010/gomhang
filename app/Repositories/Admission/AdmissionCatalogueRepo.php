<?php   
namespace App\Repositories\Admission;
use App\Repositories\BaseRepository;

use App\Models\AdmissionCatalogue;

class AdmissionCatalogueRepo extends BaseRepository {
    protected $model;

    public function __construct(
        AdmissionCatalogue $model
    )
    {
        $this->model = $model;
    }

}