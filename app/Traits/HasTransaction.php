<?php  
namespace App\Traits;
use Illuminate\Support\Facades\DB;

trait HasTransaction {

    protected function beginTransaction(): static{
        DB::beginTransaction();
        return $this;
    }


    protected function commit(): static {
        DB::commit();
        return $this;
    }

    protected function rollback(): static {
        DB::rollBack();
        return $this;
    }

    protected function beforeSave(): static {
        return $this;
    }

    protected function afterSave(): static {
        return $this;
    }

    protected function withRelation(): static {
        $request = $this->context['request'];
        $relation = $this->model->getRelationable();
        if(count($relation)){
            foreach($relation as $key => $val){
                if($request->has($val)){
                    $this->model->{$val}()->sync($request->{$val});
                }
            }
        }
        return $this;
    }
    
    protected function beforeDestroy(): static {
        return $this;
    }

    protected function afterDestroy(): static {
        return $this;
    }


}