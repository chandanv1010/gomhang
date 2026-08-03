<?php  
namespace App\Repositories\School;
use App\Models\SchoolCatalogue;
use App\Repositories\BaseRepository;

class SchoolCatalogueRepo  extends BaseRepository{

    protected $model;

    public function __construct(
        SchoolCatalogue $model
    )
    {
        $this->model = $model;
        parent::__construct($model);
    }
}