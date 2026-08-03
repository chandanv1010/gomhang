<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Introduce extends Model
{
    use HasFactory;

    protected $table = 'introduces';


    public function languages(){
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
