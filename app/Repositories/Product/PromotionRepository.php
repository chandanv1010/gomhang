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
    /** Value the admin form posts for "no end date". */
    public const NEVER_ENDS = 'accept';

    protected $model;

    public function __construct(
        Promotion $model
    ){
        $this->model = $model;
    }

    /**
     * Every campaign that has not finished yet for the given products - both the
     * ones running right now and the ones scheduled to start later - with the
     * discount each one yields for that product.
     *
     * Unlike findByProduct() this returns one row per (product, campaign) pair
     * instead of aggregating with MAX(). Aggregating there mixed columns from
     * different campaigns: the discount came from the best campaign while
     * promotion_id/endDate came from an arbitrary one, so a countdown could show
     * another campaign's deadline. Picking the best campaign and chaining the
     * rest is done in PHP where the ordering rules are explicit.
     *
     * Times are compared as full timestamps. The old code used whereDate(),
     * which only compares the date part and kept a campaign "running" for the
     * rest of the day after it had already ended.
     */
    public function findActiveAndUpcomingByProducts(array $productId = [])
    {
        if (empty($productId)) {
            return collect();
        }

        $discountExpression = "
            CASE
                WHEN promotions.discountType = 'cash' THEN promotions.discountValue
                WHEN promotions.discountType = 'percent' THEN products.price * promotions.discountValue / 100
                ELSE 0
            END
        ";

        return $this->model->select(
            'promotions.id as promotion_id',
            'promotions.name as promotion_name',
            'promotions.discountValue',
            'promotions.discountType',
            'promotions.maxDiscountValue',
            'promotions.startDate',
            'promotions.endDate',
            'promotions.neverEndDate',
            'products.id as product_id',
            'products.price as product_price',
        )
        ->selectRaw(
            "IF(promotions.maxDiscountValue != 0,
                LEAST({$discountExpression}, promotions.maxDiscountValue),
                {$discountExpression}
            ) as discount"
        )
        ->join('promotion_product_variant as ppv', 'ppv.promotion_id', '=', 'promotions.id')
        ->join('products', 'products.id', '=', 'ppv.product_id')
        ->whereNull('promotions.deleted_at')
        ->whereNull('products.deleted_at')
        ->where('products.publish', 2)
        ->where('promotions.publish', 2)
        ->whereIn('ppv.product_id', $productId)
        // Not finished yet: either open ended ('accept' is the sentinel the
        // admin form posts), or the deadline is still ahead.
        ->where(function ($query) {
            $query->where('promotions.neverEndDate', self::NEVER_ENDS)
                ->orWhere('promotions.endDate', '>', now());
        })
        ->orderByDesc('discount')
        ->orderBy('promotions.endDate')
        ->get();
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
        ->whereNull('promotions.deleted_at')
        ->where('promotions.publish', 2)
        ->where('ppv.variant_uuid', $uuid)
        // Compare full timestamps: whereDate() only compares the date part, so a
        // campaign that ended this morning stayed "live" for the rest of the day.
        ->where('promotions.startDate', '<=', now())
        ->where(function ($query) {
            $query->where('promotions.neverEndDate', self::NEVER_ENDS)
                ->orWhere('promotions.endDate', '>', now());
        })
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
