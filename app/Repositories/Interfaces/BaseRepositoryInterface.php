<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserServiceInterface
 * @package App\Services\Interfaces
 */
interface BaseRepositoryInterface
{
    public function all(array $relation);
    public function forceDeleteByCondition(array $condition = []);
    public function findById(int $id, array $column = ['*'], array $relation = []);
    public function create(array $payload = []);
    public function update(int $id = 0, array $payload = []);
    public function delete(int $id = 0);
    public function forceDelete(int $id = 0);
    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perpage = 10,
        array $extends = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = [],



    );
    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = []);
    public function createLanguagePivot($model, array $payload = []);
    public function createPivot($model, array $payload = [], string $relation = '');
    public function updateByWhere($condition = [], array $payload = []);
    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        array $orderBy = ['id', 'desc'],
        array $param = [],
        array $withCount = [],
    );
    public function createBatch(array $payLoad = []);
    public function updateOrInsert(array $payload = [], array $condition = []);
    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '');
    public function findWidgetItem(array $condition = [], int $language_id = 5, string $alias = '');
}
