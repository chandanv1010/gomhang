<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'product_scope',
        'total_quantity',
        'used_quantity',
        'start_date',
        'end_date',
        'max_usage_per_user',
        'combine_promotion',
        'method'
    ];

    protected $table = 'vouchers';

    public function voucher_order_conditions()
    {
        return $this->hasOne(VoucherOrderCondition::class);
    }

    public function voucher_shipping_conditions()
    {
        return $this->hasOne(VoucherShippingCondition::class);
    }

    public function voucher_products(){
        return $this->belongsToMany(Product::class,'voucher_products','voucher_id','product_id');
    }

    
    
}
