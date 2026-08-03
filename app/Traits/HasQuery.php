<?php  
namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

trait HasQuery {

    // public function scopeTake($query, $take = null){
    //     if(!is_null($take)){
    //         $query->take($take);
    //     }
    // }

    public function scopeKeyword($query, array $keyword = []){
        if(!is_null($keyword['q'])){
            foreach($keyword['searchFields'] as $field){
                $query->orWhere($field, 'LIKE', '%'.$keyword['q'].'%');
            }
        }
        return $query;
    }

    public function scopeSimpleFilter($query, array $filter = []){
        if(!is_null($filter) && count($filter)){
            foreach($filter as $key => $val){
                if($val !== 0 && !empty($val) && !is_null($val)){
                    $query->where($key, $val);
                }
            }
        }
        return $query;
    }

    public function scopeComplexFilter($query, array $filter = []){
        if(!is_null($filter) && count($filter)){
            foreach($filter as $field => $condition){
                foreach($condition as $operator => $val){
                    switch ($operator) {
                        case 'gt':
                            $query->where($field, '>', $val);
                            break;
                        case 'gte':
                            $query->where($field, '>=', $val);
                            break;
                        case 'lt':
                            $query->where($field, '<', $val);
                            break;
                        case 'lte':
                            $query->where($field, '<=', $val);
                            break;
                        case 'eq':
                            $query->where($field, '=', $val);
                            break;
                        case 'in':
                            $in = explode(',', $val);
                            if(count($in)){
                                $query->whereIn($field, $in);
                            }
                            break;
                        case 'between':
                            [$min, $max] = explode(',', $val);
                            $query->whereBetween($field, [$min, $max]);
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
            }
        }
        return $query;
    }

    public function scopeDateFilter($query, array $filter = []){
        if(!is_null($filter) && count($filter)){
            foreach($filter as $field => $condition){
                foreach($condition as $operator => $date){
                    $explodeDate = explode(',', $date);
                    if(count($explodeDate) === 1){
                        $dateFormat = Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                    }

                    
                    switch ($operator) {
                        case 'gt':
                            $query->where($field, '>', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'gte':
                            $query->where($field, '>=', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'lt':
                            $query->where($field, '<', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'lte':
                            $query->where($field, '<=', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'eq':
                            $query->where($field, '=', Carbon::parse($dateFormat)->startOfDay());
                            break;
                        case 'between':
                            [$startDate, $endDate] = explode(',', $date); 
                            $startDate =  Carbon::createFromFormat('d-m-Y', $startDate)->format('Y-m-d');
                            $endDate =  Carbon::createFromFormat('d-m-Y', $endDate)->format('Y-m-d');
                            
                            $query->whereBetween($field, [
                                Carbon::parse($startDate)->startOfDay(),
                                Carbon::parse($endDate)->endOfDay()
                            ]);
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
            }
        }
        return $query;
    }

    public function scopeCatalogueFilter($query, $condition){
        if(isset($condition) && is_array($condition) && count($condition)){
            foreach($condition as $key => $val){
                $query->whereHas($val['name'], function($subQuery) use ($val) {
                    if(count($val['id'])){
                        $subQuery->whereIn($val['field'], $val['id']);
                    }
                });
            }
        }
        return $query;
    }
    


}