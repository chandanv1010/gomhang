<?php

namespace App\Repositories\Product;

use App\Models\ProductVariantLanguage;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class ProductVariantLanguageRepository extends BaseRepository 
{
    protected $model;

    public function __construct(
        ProductVariantLanguage $model
    ){
        $this->model = $model;
    }

    
}
