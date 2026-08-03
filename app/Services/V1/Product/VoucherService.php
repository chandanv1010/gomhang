<?php

namespace App\Services\V1\Product;
use App\Services\V1\BaseService;
use App\Repositories\Product\VoucherRepository;



use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Enums\VoucherEnum;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserService
 * @package App\Services
 */
class VoucherService extends BaseService
{
    protected $voucherRepository;

    public function __construct(
        VoucherRepository $voucherRepository
    ){
        $this->voucherRepository = $voucherRepository;
    }

    public function paginate($request){
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish')
        ];
        $perPage = $request->integer('perpage');
        $vouchers = $this->voucherRepository->voucherPagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage, 
            ['path' => 'voucher/index'], 
        );
        return $vouchers;
    }

    private function request($request){
        $payload = $request->only(
            'name',
            'code', 
            'description' ,
            'total_quantity' , 
            'max_usage_per_user' , 
            'combine_promotion' , 
            'start_date' , 
            'end_date' , 
            'method', 
            'product_scope'
        );
        $type = $request->input('method');
        $payload['code'] = (empty($payload['code'])) ? time() : $payload['code'];
        $payload['discount_value'] = convert_price($request->input($type.'.discount_value'));
        $payload['max_discount_amount'] = convert_price($request->input($type.'.max_discount_amount'));
        $payload['discount_type'] = $request->input($type.'.discount_type');
        $payload['start_date'] = Carbon::createFromFormat('d/m/Y H:i', $payload['start_date']);
        $payload['end_date'] = Carbon::createFromFormat('d/m/Y H:i', $payload['end_date']);
        if(!isset($payload['combine_promotion'])){
            $payload['combine_promotion'] = 0;
        }
        if($type == VoucherEnum::PRODUCT_VOUCHER){
            $payload['product_scope'] = $request->input('product_scope');
        }
        return $payload;
    }

    public function create($request){
        DB::beginTransaction();
        try{
            $payload = $this->request($request);
            $voucher = $this->voucherRepository->create($payload);
            if($voucher->id > 0){
                $this->handleRelation($request, $voucher, $method = 'create');
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{
            $payload = $this->request($request);
            $voucher = $this->voucherRepository->update($id, $payload);
            $this->handleRelation($request, $voucher, 'update');
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $voucher = $this->voucherRepository->forceDelete($id);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    private function handleRelation($request, $voucher, $method){
        $type = $request->input('method');
        if($type == VoucherEnum::PRODUCT_VOUCHER){
            $this->handleVoucherProduct($request, $voucher, $method);
        }else{
            $this->handleVoucherOrder($request, $voucher, $type , $method);
        }
    }

    private function handleVoucherProduct($request , $voucher , $method = 'create'){
        $object = $request->input('object');
        $payload = [];
        if(!is_null($object)){
            foreach($object['id'] as $key => $val){
                $payload[] = [
                    'voucher_id' => $voucher->id,
                    'product_id' => $val,
                ];
            }
        }
        if($method == 'update'){
            $voucher->voucher_products()->detach();
        }
        $voucher->voucher_products()->sync($payload);
    }

    private function handleVoucherOrder($request , $voucher , $type,  $method = 'create'){
        $type_voucher = ($type == VoucherEnum::TOTAL_ORDERS) ? 'order' : 'shipping';
        $payload = [
            'voucher_id' => $voucher->id,
            'min_'.$type_voucher.'_value' => convert_price($request->input($type.'.min_'.$type_voucher.'_value')),
            'max_'.$type_voucher.'_value' => convert_price($request->input($type.'.max_'.$type_voucher.'_value'))
        ];
        if($method == 'create'){
            ($type == VoucherEnum::TOTAL_ORDERS) ? $voucher->voucher_order_conditions()->insert($payload) : $voucher->voucher_shipping_conditions()->insert($payload);
        }else{
            $voucher->voucher_shipping_conditions()->where('voucher_id', $voucher->id)->forceDelete();
            $voucher->voucher_order_conditions()->where('voucher_id', $voucher->id)->forceDelete();
            ($type == VoucherEnum::TOTAL_ORDERS) ? $voucher->voucher_order_conditions()->insert($payload) : $voucher->voucher_shipping_conditions()->insert($payload);
        }
    }

    public function listVoucher($cartTotal , $totalShippingCost = 0, $carts){

        $customer = Auth::guard('customer')->user();

        $productsIDs = $carts->pluck('id');

        $all_voucher = $this->voucherRepository->findVouchersCart($productsIDs, $customer->id);

        $vouchersActive = [];

        if($carts){
            foreach($carts as $k => $v){
                if(!is_null($v->options->voucher)){
                    $vouchersActive[] = [
                        'id' => $v->options->voucher['id'],
                        'product_id' => $v->id
                    ];
                }
            }
        }
        if($all_voucher){
            foreach($all_voucher as $k => $v){
                $is_active = false;
                foreach($vouchersActive as $voucherActive){
                    if($v['id'] == $voucherActive['id']){
                        $is_active = true;
                        break;
                    }
                }
                $v->is_active = $is_active;
                if(!is_null($v->min_order_value) && !is_null($v->max_order_value)){
                    $v->coupon_use = ($cartTotal >= $v->min_order_value ) ? true : false;
                }else{
                    $v->coupon_use = ($totalShippingCost >= $v->min_shipping_value) ? true : false;
                }
            }
        }

        return $all_voucher;
    }

    public function getVoucherForProduct($id, $carts = null, $customerId){
        $voucher_product = $this->voucherRepository->getVoucherForProduct($id, $customerId);
        $vouchersActive = [];
        if(!is_null($carts)){
            foreach($carts as $k => $v){
                if(!is_null($v->options->voucher)){
                    $vouchersActive[] = [
                        'id' => $v->options->voucher['id'],
                        'product_id' => $v->id
                    ];
                }
            }
        }
        foreach($voucher_product as $item){
            $is_active = false;
            foreach($vouchersActive as $voucherActive){
                if($item['id'] == $voucherActive['id'] && $id == $voucherActive['product_id']){
                    $is_active = true;
                    break;
                }
            }
            $item->is_active = $is_active;
        }
        return $voucher_product;
    }

    private function paginateSelect(){
        return [
            'id',
            'code',
            'name',
            'description',
            'start_date',
            'end_date',
            'discount_value',
            'discount_type',
            'total_quantity',
            'used_quantity',
            'method'
        ];
    }
}
