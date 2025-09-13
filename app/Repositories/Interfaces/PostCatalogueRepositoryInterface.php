<?php

namespace App\Repositories\Interfaces;

/**
 * Interface PostCatalogueRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface PostCatalogueRepositoryInterface extends BaseRepositoryInterface
{
    public function findById(int $id, array $column = ['*'], array $relation = []);
    public function getPostCatalogueById(int $id = 0, $language_id = 0);
    // public function createTranslatePivot($model, array $payload = []);
}
