<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;
use App\Traits\QueryScopes;

class Product extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'image',
        'album',
        'publish',
        'follow',
        'order',
        'user_id',
        'product_catalogue_id',
        'price',
        'stock',
        'made_in',
        'code',
        'attributeCatalogue',
        'attribute',
        'variant',
        'qrcode',
        'warranty',
        'check',
        'seller_id',
        'total_lesson',
        'duration',
        'lecturer_id',
        'lession_content',
        'chapter',
        'iframe'
    ];

    protected $casts = [
        'attribute' => 'json',
        'chapter' => 'json'
    ];


    protected $table = 'products';

    public function languages()
    {
        return $this->belongsToMany(Language::class, 'product_language', 'product_id', 'language_id')
            ->withPivot(
                'name',
                'canonical',
                'meta_title',
                'meta_keyword',
                'meta_description',
                'description',
                'content',
                'url',
            )->withTimestamps();
    }


    public function product_catalogues()
    {
        return $this->belongsToMany(ProductCatalogue::class, 'product_catalogue_product', 'product_id', 'product_catalogue_id');
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product_variant', 'product_id', 'promotion_id')
            ->withPivot(
                'variant_uuid',
                'model',
            )->withTimestamps();
    }


    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_product', 'product_id', 'order_id')
            ->withPivot(
                'uuid',
                'name',
                'qty',
                'price',
                'priceOriginal',
                'option',
            );
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function sellers()
    {
        return $this->belongsTo(Customer::class, 'seller_id', 'id');
    }

    public function lecturers()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id', 'id');
    }

    public function getImageAttribute($value)
    {
        if (app()->runningInConsole() || request()->segment(1) === 'admin' || request()->segment(1) === 'seller' || request()->segment(1) === 'api') {
            return $value;
        }
        return !empty($value) ? $value : 'https://gomhang.vn/wp-content/uploads/2026/08/072dcae0-adf1-40fb-a738-6bb864078920-400x533.png';
    }
}
