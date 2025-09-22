<?php

namespace App\Repositories\Interfaces;

/**
 * Interface PermissionRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface PermissionRepositoryInterface extends BaseRepositoryInterface
{
    public function all(array $relation = []);
}
