<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerPointHistory extends Model
{
    use HasFactory;

    protected $table = 'customer_point_history';

    protected $fillable = [
        'customer_id',
        'order_id',
        'points',
        'type',
        'description',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
