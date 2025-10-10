<?php

namespace App\Repositories;

use App\Models\MenuCatalogue;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class MenuCatalogueRepository
 * @package App\Repositories
 */
class MenuCatalogueRepository extends BaseRepository implements MenuCatalogueRepositoryInterface
{
    protected $model;

    public function __construct(MenuCatalogue $model)
    {
        $this->model = $model;
    }
}
