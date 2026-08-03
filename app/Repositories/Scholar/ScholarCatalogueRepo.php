<?php   
namespace App\Repositories\Scholar;
use App\Repositories\BaseRepository;

use App\Models\ScholarCatalogue;

class ScholarCatalogueRepo extends BaseRepository {
    protected $model;

    public function __construct(
        ScholarCatalogue $model
    )
    {
        $this->model = $model;
    }

}