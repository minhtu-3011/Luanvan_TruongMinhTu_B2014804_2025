<?php


namespace App\Repositories;

use App\Repositories\Interfaces\GenerateRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\Generate;

class GenerateRepository extends BaseRepository implements GenerateRepositoryInterface
{
    protected $model;

    public function __construct(
        Generate $model
    ) {
        $this->model = $model;
    }
}
