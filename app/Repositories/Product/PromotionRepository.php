<?php

namespace App\Repositories\Product;

use App\Models\Promotion;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
/**
 * Class UserService
 * @package App\Services
 */
class PromotionRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Promotion $model
    ){
        $this->model = $model;
    }

    public function findByProduct(array $productId = []){
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
            'promotions.endDate',
            'products.id as product_id',
            'products.price as product_price',
        )
        ->selectRaw(
            "
                MAX(
                    IF(promotions.maxDiscountValue != 0,
                        LEAST(
                            CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN products.price * discountValue / 100
                            ELSE 0
                            END,
                            promotions.maxDiscountValue 
                        ),
                        CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN products.price * discountValue / 100
                        ELSE 0
                        END
                    )
                ) as discount
            "
        )
        ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
        ->join('products', 'products.id', '=', 'ppv.product_id')
        ->where('products.publish', 2)
        ->where('promotions.publish', 2)
        ->whereIn('ppv.product_id', $productId)
        ->whereDate('promotions.endDate', '>', now())
        ->whereDate('promotions.startDate', '<', now())
        ->groupBy('ppv.product_id')
        ->get();
    }

    public function findPromotionByVariantUuid($uuid = ''){
        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
        )
        ->selectRaw(
            "
                MAX(
                    IF(promotions.maxDiscountValue != 0,
                        LEAST(
                            CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN pv.price * discountValue / 100
                            ELSE 0
                            END,
                            promotions.maxDiscountValue 
                        ),
                        CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN pv.price * discountValue / 100
                        ELSE 0
                        END
                    )
                ) as discount
            "
        )
        ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
        ->join('product_variants as pv', 'pv.uuid', '=', 'ppv.variant_uuid')
        ->where('promotions.publish', 2)
        ->where('ppv.variant_uuid', $uuid)
        ->whereDate('promotions.endDate', '>', now())
        ->whereDate('promotions.startDate', '<', now())
        ->orderByDesc('discount') 
        ->first();
    }

    public function getPromotionByCartTotal()
    {
        return $this->model
            ->where('promotions.publish', 2)
            ->where('promotions.method', 'order_amount_range')
            ->whereDate('promotions.endDate', '>=', now())
            ->whereDate('promotions.startDate', '<=', now())
            ->get();
    }
    
    public function getPromotionTakeGiftBuyProduct($method, $id = null){
        $promotionIds = $this->model->join('promotion_rules as tb2', 'tb2.promotion_id', '=', 'promotions.id')
        ->where('tb2.product_id', $id)
        ->pluck('promotions.id');
        return $this->model->select(
            'promotions.*',
            'tb4.product_id as pd_id',
            'tb4.name as pd_name',
            'tb4.canonical as pd_canonical',
            'tb2.quantity as pd_quantity',
            'tb7.product_id as pdg_id',
            'tb7.name as pdg_name',
            'tb7.canonical as pdg_canonical',
            'tb5.quantity as pdg_quantity',
        )
        ->leftJoin('promotion_rules as tb2', 'tb2.promotion_id', '=', 'promotions.id')
        ->leftJoin('products as tb3', 'tb3.id', '=', 'tb2.product_id')
        ->leftJoin('product_language as tb4', 'tb4.product_id', '=', 'tb3.id')
        ->leftJoin('promotion_gifts as tb5', 'tb5.promotion_id', '=', 'promotions.id')
        ->leftJoin('products as tb6', 'tb6.id', '=', 'tb5.product_id')
        ->leftJoin('product_language as tb7', 'tb7.product_id', '=', 'tb6.id')
        ->where('promotions.method', $method)
        ->whereIn('promotions.id', $promotionIds)
        ->get();
    }

    
}
