<?php  
namespace App\Services\V2\Interfaces;

interface BaseServiceInterface {
    public function getCatalogueChildren($catalogue = null, $request);
}