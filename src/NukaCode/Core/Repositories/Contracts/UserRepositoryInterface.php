<?php namespace NukaCode\Core\Repositories\Contracts;


interface UserRepositoryInterface {

    public function orderByName();

    public function find($id);
} 