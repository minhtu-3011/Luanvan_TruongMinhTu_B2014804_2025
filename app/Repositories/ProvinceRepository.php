<?php


namespace App\Repositories;

use App\Repositories\Interfaces\ProvinceRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Province;

class ProvinceRepository extends BaseRepository implements ProvinceRepositoryInterface
{
    protected $model;

    public function __construct(
        Province $model
    ) {
        $this->model = $model;
    }
}
