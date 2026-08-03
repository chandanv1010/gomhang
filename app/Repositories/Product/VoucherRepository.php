<?php

namespace App\Repositories\Product;
use App\Models\Voucher;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;
use App\Enums\VoucherEnum;

use function PHPSTORM_META\map;

/**
 * Class UserService
 * @package App\Services
 */
class VoucherRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Voucher $model
    ){
        $this->model = $model;
    }

    public function voucherPagination(
        array $column = ['*'], 
        array $condition = [], 
        int $perPage = 1,
        array $extend = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
    ){
        $query = $this->model->select($column)->where(function($query) use ($condition){
            if(isset($condition['keyword']) && !empty($condition['keyword'])){
                $query->where('code', 'LIKE', '%'.$condition['keyword'].'%')
                ->orWhere('name', 'LIKE', '%'.$condition['keyword'].'%');
            }
            if(isset($condition['publish']) && $condition['publish'] != 0){
                $query->where('publish', '=', $condition['publish']);
            }
            return $query;
        });
        if(!empty($join)){
            $query->join(...$join);
        }
        return $query->paginate($perPage)
        ->withQueryString()->withPath(env('APP_URL').$extend['path']);
    }

    public function getVoucherForProduct($id = 0, $customerId = 0){
        return $this->model->select(
            'vouchers.*',
            'products.price'
        )
        ->selectRaw(
            "
                IF(vouchers.max_discount_amount != 0,
                    LEAST(
                        CASE 
                            WHEN discount_type = 'FIXED' THEN discount_value
                            WHEN discount_type = 'PERCENTAGE' THEN products.price * discount_value / 100
                        ELSE 0
                        END,
                        vouchers.max_discount_amount 
                    ),
                    CASE 
                        WHEN discount_type = 'FIXED' THEN discount_value
                        WHEN discount_type = 'PERCENTAGE' THEN products.price * discount_value / 100
                    ELSE 0
                    END
                )
                as discount
            "
        )
        ->leftJoin('voucher_products as vp', function($join) use ($id) {
            $join->on('vp.voucher_id', '=', 'vouchers.id')
                ->where('vp.product_id', $id); 
        })
        ->leftJoin('products', function($join) use ($id) {
            $join->on('products.id', '=', 'vp.product_id') 
                ->orWhere(function($query) use($id) {
                    $query->whereNull('vp.product_id') 
                        ->where('vouchers.product_scope', 'ALL_PRODUCTS') 
                        ->where('products.id', $id); 
                });
        })
        ->leftJoin('voucher_usages', function($join) use ($customerId){
            $join->on('voucher_usages.voucher_id','=','vouchers.id')
                ->where('voucher_usages.customer_id', $customerId);
        })
        ->where(function($query) use ($id) {
            $query->where('vouchers.product_scope', 'ALL_PRODUCTS')
                ->orWhere('vp.product_id', $id);
        })
        ->where(function($query) use($customerId){
            $query
            ->orWhereRaw(
                '
                    (
                        SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id
                        AND
                        voucher_usages.customer_id = ?
                    ) < vouchers.max_usage_per_user
                ',[$customerId]
            );
        })
        ->where('vouchers.used_quantity', '<' , DB::raw('vouchers.total_quantity'))
        ->whereDate('vouchers.start_date' , '<=' , now())
        ->whereDate('vouchers.end_date', '>=', now())
        ->get();
    }

    public function findVouchersCart($productsIDs = [], $customerId = 0){
        return $this->model->select(
            'vouchers.*',
            'tb2.min_order_value','tb2.max_order_value',
            'tb3.min_shipping_value','tb3.max_shipping_value',
        )->where(function($query) use ($productsIDs){
            $query->where('product_scope', '=', VoucherEnum::TOTAL_ORDERS)
                ->orWhere('product_scope', '=', VoucherEnum::SHIPPING_ORDERS)
                ->orWhere('product_scope', '=', VoucherEnum::ALL_PRODUCTS);
            if(count($productsIDs)){
                $query->orWhereHas('voucher_products', function($subQuery) use ($productsIDs){
                    $subQuery->whereIn('voucher_products.product_id', $productsIDs);
                });
            }
        })
        ->selectRaw(
            "vouchers.*,
                IF(vouchers.max_discount_amount != 0, 
                    LEAST(
                        CASE 
                            WHEN discount_type = 'FIXED' THEN discount_value
                            WHEN discount_type = 'PERCENTAGE' THEN 
                                CASE 
                                    WHEN vouchers.product_scope = '".VoucherEnum::TOTAL_ORDERS."' 
                                    THEN COALESCE(tb2.min_order_value, 0) * discount_value / 100
                                    WHEN vouchers.product_scope = '".VoucherEnum::SHIPPING_ORDERS."' 
                                    THEN COALESCE(tb3.min_shipping_value, 0) * discount_value / 100
                                    WHEN vouchers.product_scope = '".VoucherEnum::SPECIFIC_PRODUCTS."' 
                                    THEN COALESCE(tb5.price, 0) * discount_value / 100
                                    ELSE 0
                                END
                            ELSE 0
                        END,
                        vouchers.max_discount_amount
                    ),
                    CASE 
                        WHEN discount_type = 'FIXED' THEN discount_value
                        WHEN discount_type = 'PERCENTAGE' THEN 
                            CASE 
                                WHEN vouchers.product_scope = '".VoucherEnum::TOTAL_ORDERS."' 
                                THEN COALESCE(tb2.min_order_value, 0) * discount_value / 100
                                WHEN vouchers.product_scope = '".VoucherEnum::SHIPPING_ORDERS."' 
                                THEN COALESCE(tb3.min_shipping_value, 0) * discount_value / 100
                                WHEN vouchers.product_scope = '".VoucherEnum::SPECIFIC_PRODUCTS."' 
                                THEN COALESCE(tb5.price, 0) * discount_value / 100
                                ELSE 0
                            END
                        ELSE 0
                    END
                ) as discount
            "
        )
        ->leftJoin('voucher_order_conditions as tb2', 'tb2.voucher_id', '=', 'vouchers.id')
        ->leftJoin('voucher_shipping_conditions as tb3', 'tb3.voucher_id', '=', 'vouchers.id')
        ->leftJoin('voucher_products as tb4','tb4.voucher_id','=','vouchers.id')
        ->leftJoin('products as tb5','tb5.id','=','tb4.product_id')
        ->leftJoin('voucher_usages', function($join) use ($customerId){
            $join->on('voucher_usages.voucher_id','=','vouchers.id')
                ->where('voucher_usages.customer_id', $customerId);
        })
        ->where(function($query) use($customerId){
            $query
            ->orWhereRaw(
                '
                    (
                        SELECT COUNT(*) FROM voucher_usages WHERE voucher_usages.voucher_id = vouchers.id
                        AND
                        voucher_usages.customer_id = ?
                    ) < vouchers.max_usage_per_user
                ',[$customerId]
            );
        })
        ->where('vouchers.used_quantity', '<' , Db::raw('vouchers.total_quantity'))
        ->whereDate('vouchers.start_date' , '<=' , now())
        ->whereDate('vouchers.end_date', '>=', now())
        ->groupBy('vouchers.id')
        ->distinct()
        ->get();
    }

    public function findVoucherByCondition($voucher_scope){
        return $this->model->with(['voucher_order_conditions'])
        ->where('vouchers.product_scope',$voucher_scope)
        ->where('vouchers.used_quantity', '<' , DB::raw('vouchers.total_quantity'))
        ->whereDate('vouchers.start_date' , '<=' , now())
        ->whereDate('vouchers.end_date', '>=', now())
        ->get();
    }

    public function getDiscountVoucherTotal($total = 0 , $id){
        return $this->model
        ->selectRaw(
            "
                IF(vouchers.max_discount_amount != 0,
                    LEAST(
                        CASE 
                            WHEN discount_type = 'FIXED' THEN discount_value
                            WHEN discount_type = 'PERCENTAGE' THEN {$total} * discount_value / 100
                        ELSE 0
                        END,
                        vouchers.max_discount_amount 
                    ),
                    CASE 
                        WHEN discount_type = 'FIXED' THEN discount_value
                        WHEN discount_type = 'PERCENTAGE' THEN {$total} * discount_value / 100
                    ELSE 0
                    END
                )
                as discount
            "
        )
        ->where('vouchers.id', $id)
        ->first()
        ->toArray();
    }

    public function getDiscountVoucherForCart($total = 0 , $id){
        return $this->model->select('tb2.min_order_value')
        ->selectRaw(
            "
                IF(vouchers.max_discount_amount != 0,
                    LEAST(
                        CASE 
                            WHEN discount_type = 'FIXED' THEN discount_value
                            WHEN discount_type = 'PERCENTAGE' THEN {$total} * discount_value / 100
                        ELSE 0
                        END,
                        vouchers.max_discount_amount 
                    ),
                    CASE 
                        WHEN discount_type = 'FIXED' THEN discount_value
                        WHEN discount_type = 'PERCENTAGE' THEN {$total} * discount_value / 100
                    ELSE 0
                    END
                )
                as discount
            "
        )
        ->where('vouchers.id', $id)
        ->leftJoin('voucher_order_conditions as tb2','tb2.voucher_id','=','vouchers.id')
        ->first()
        ->toArray();
    }

    public function getProductByVoucher($voucherId = 0){
        return $this->model->select('tb3.id')
        ->join('voucher_products as tb2','tb2.voucher_id','=','vouchers.id')
        ->join('products as tb3','tb3.id','=','tb2.product_id')
        ->where('vouchers.id', $voucherId)
        ->get();
    }

}