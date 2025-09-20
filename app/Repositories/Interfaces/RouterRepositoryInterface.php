<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface RouterRepositoryInterface
{
    public function findById(int $id, array $column = ['*'], array $relation = []);
    public function create(array $payload = []);
    public function update(int $id = 0, array $payload = []);
    public function findByCondition($condition = []);
}
