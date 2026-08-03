<?php

namespace App\Repositories\Core;

use App\Models\Contact;
use App\Repositories\BaseRepository;

class ContactRepository extends BaseRepository
{
    protected $model;

    public function __construct(
        Contact $model
    ){
        $this->model = $model;
    }

}