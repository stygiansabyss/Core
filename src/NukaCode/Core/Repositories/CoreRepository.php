<?php namespace NukaCode\Core\Repositories;

abstract class CoreRepository {

    protected $model;

    public function find($id)
    {
        return $this->model->find($id);
    }
} 