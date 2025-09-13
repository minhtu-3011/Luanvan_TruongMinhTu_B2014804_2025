<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function findById(int $id, array $column = ['*'], array $relation = []);
}
