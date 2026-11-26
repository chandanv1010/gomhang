<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $table = 'provinces';
    protected $primaryKey = 'code'; 
    public $incrementing = false;

    public function districts()
    {
        return $this->hasMany(District::class, 'province_code', 'code');
    }

    // Bo dia gioi sau sap nhap (2025): tinh -> phuong/xa, khong con cap huyen.
    public function wards()
    {
        return $this->hasMany(Ward::class, 'province_code', 'code')->orderBy('name');
    }

    public function provinces(){
        return $this->hasMany(Order::class, 'province_id', 'code');
    }
    
}
