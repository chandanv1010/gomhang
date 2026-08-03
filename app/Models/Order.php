<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Order extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'code',
        'fullname',
        'phone',
        'email',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'description',
        'promotion',
        'cart',
        'customer_id',
        'guest_cookie',
        'method',
        'confirm',
        'payment',
        'delivery',
        'shipping',
        'seller_id',
        'point_value',
        'point_added',
        'point_used',
        'point_used_deducted',
    ];

    protected $casts = [
        'cart' => 'json',
        'promotion' => 'json'
    ];

    protected $table = 'orders';

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id')
            ->withPivot(
                'uuid',
                'name',
                'qty',
                'price',
                'priceOriginal',
                'option',
            );
    }

    public function order_payments()
    {
        return $this->hasMany(OrderPayment::class, 'order_id', 'id');
    }

    public function provinces()
    {
        return $this->hasMany(Province::class, 'code', 'province_id');
    }

    public function buyers()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function sellers()
    {
        return $this->belongsTo(Customer::class, 'seller_id', 'id');
    }

    public function voucher_orders()
    {
        return $this->belongsToMany(Voucher::class, 'voucher_orders', 'order_id', 'voucher_id');
    }

    public function voucher_usages()
    {
        return $this->belongsToMany(Voucher::class, 'voucher_usages', 'order_id', 'voucher_id')->withPivot('customer_id');
    }

    public function order_products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }
}
