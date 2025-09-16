<?php

namespace App\Repositories\Interfaces;

/**
 * Interface PostRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface PostRepositoryInterface extends BaseRepositoryInterface
{
    public function findById(int $id, array $column = ['*'], array $relation = []);
    public function getPostById(int $id = 0, $language_id = 0);
    // public function createTranslatePivot($model, array $payload = []);
}
