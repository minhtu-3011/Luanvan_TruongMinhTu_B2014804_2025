<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all();

    public function findById(int $id, array $column = ['*'], array $relation = []);
    public function create(array $payload = []);
    public function update(int $id = 0, array $payload = []);
    public function delete(int $id = 0);
    public function forceDelete(int $id = 0);
    public function pagination(
        array $column = ['*'],
        array $condition = [],
        array $join = [],
        array $extends = [],
        int $perpage = 10,
        array $relations = [],
        array $orderBy = ['id', 'DESC'],


    );
    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []);
    public function createLanguagePivot($model, array $payload = []);
}
