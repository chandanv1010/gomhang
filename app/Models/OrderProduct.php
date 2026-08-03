<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected $table = 'order_product';

    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'uuid',
        'name',
        'qty',
        'price',
        'priceOriginal',
        'option',
    ];

    protected $casts = [
        'option' => 'json',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
