<?php namespace NukaCode\Core\Repositories;

use NukaCode\Core\Repositories\Contracts\UserRepositoryInterface;

class UserRepository extends CoreRepository implements UserRepositoryInterface {

    public function __construct(\User $user)
    {
        $this->model = $user;
    }

    public function orderByName()
    {
        return $this->model->orderByNameAsc()->get();
    }
}