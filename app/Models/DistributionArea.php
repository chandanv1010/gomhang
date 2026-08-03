<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\QueryScopes;

class DistributionArea extends Model
{
    use HasFactory, QueryScopes;

    protected $table = 'distribution_areas';

    protected $fillable = [
        'name',
        'parent_id',
        'publish',
    ];

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
}
