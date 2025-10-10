<?php

namespace App\Repositories\Interfaces;

/**
 * Interface {Model}RepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface PostCatalogueRepositoryInterface
{
    public function getPostCatalogueById(int $id = 0, $language_id = 0);
}
