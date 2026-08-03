<?php

namespace App\Repositories\Product;

use App\Models\ProductVariantAttribute;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class ProductVariantAttributeRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        ProductVariantAttribute $model
    ){
        $this->model = $model;
    }

    
}
