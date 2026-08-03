<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\QueryScopes;

class Distribution extends Model
{
    use HasFactory, SoftDeletes, QueryScopes;

    protected $table = 'distributions';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'image',
        'map',
        'province_id', // Region ID (distribution_areas parent)
        'district_id', // City/District ID (distribution_areas child)
        'publish',
    ];

    public function region()
    {
        return $this->belongsTo(DistributionArea::class, 'province_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo(DistributionArea::class, 'district_id', 'id');
    }
}
