<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Promotion extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'code',
        'description',
        'method',
        'discountInformation',
        'discountValue',
        'discountType',
        'maxDiscountValue',
        'neverEndDate',
        'startDate',
        'endDate',
        'publish',
        'order',
    ];

    protected $casts = [
        'discountInformation' => 'json',
    ];

    protected $table = 'promotions';


    public function products(){
        return $this->belongsToMany(Promotion::class, 'promotion_product_variant' , 'promotion_id', 'product_id')
        ->withPivot(
            'variant_uuid',
            'model',
        )->withTimestamps();
    }

    public function promotion_rules(){
        return $this->belongsToMany(Product::class,'promotion_rules','promotion_id','product_id')->withPivot('quantity');
    }

    public function promotion_gifts(){
        return $this->belongsToMany(Product::class,'promotion_gifts','promotion_id','product_id')->withPivot('quantity');
    }

    public function promotion_combos()
    {
        return $this->hasOne(Combo::class, 'promotion_id', 'id');
    }

}
