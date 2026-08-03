<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Combo extends Model
{
    use HasFactory, QueryScopes, SoftDeletes;

    protected $fillable = [
        'id',
        'promotion_id',
        'price'
    ];

    protected $table = 'promotion_combos';

    public function promotions()
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    public function combo_products(){
        return $this->belongsToMany(Product::class,'combo_products','combo_id','product_id')->withPivot('uuid','quantity');
    }

}
