<?php

namespace App\Repositories\Customer;

use App\Models\CustomerCatalogue;
use App\Repositories\BaseRepository;

/**
 * Class CustomerService
 * @package App\Services
 */
class CustomerCatalogueRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        CustomerCatalogue $model
    ){
        $this->model = $model;
    }
    
}
