<?php

namespace App\Repositories\Interfaces;

/**
 * Interface LanguageRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface LanguageRepositoryInterface extends BaseRepositoryInterface
{
    public function findById(int $id, array $column = ['*'], array $relation = []);
}
