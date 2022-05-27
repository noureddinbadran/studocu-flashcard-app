<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class BaseService
{
    // Constructor to bind model to repo
    public function __construct(protected Model $model) {}

    // magic method for undefined methods to redirect for repo
    public function __call($method, $args)
    {
        return $this->model->$method(...$args);
    }
}

