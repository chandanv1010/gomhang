<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Contact extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $fillable = [
        'id',
        'name',
        'phone',
        // Cột email đã có trong bảng contacts nhưng thiếu ở đây, nên email do
        // form liên hệ gửi lên bị mass assignment bỏ qua âm thầm.
        'email',
        'address',
        'product_id',
        'post_id',
        'publish',
        'created_at',
        'type',
        'message'
    ];

    protected $table = 'contacts';

    public function products(){
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function posts(){
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

}