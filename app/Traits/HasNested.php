<?php  
namespace App\Traits;
use App\Classes\Nestedsetbie;

trait HasNested {

    protected function initNestedset(string $table = '', string $key = ''){
        $this->nestedset = new Nestedsetbie([
            'table' => $table,
            'foreignkey' => $key,
            'language_id' =>  config('app.language_id') ,
        ]);
    }

    protected function nestedSet(){
        $this->nestedset->Get('level ASC, order ASC');
        $this->nestedset->Recursive(0, $this->nestedset->Set());
        $this->nestedset->Action();
    }
}