<?php

namespace App\Repositories;

use App\Models\{$class};
use App\Repositories\Interfaces\{$class}RepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class {$class}Repository
 * @package App\Repositories
 */
class {$class}Repository extends BaseRepository implements {$class}RepositoryInterface
{
    protected $model;

    public function __construct({$class} $model)
    {
        $this->model = $model;
    }

    public function get{$class}ById(int $id = 0, $language_id = 0)
    {
        return $this->model->select([
            '{module}s.id',
            '{module}s.{module}_catalogue_id',
            '{module}s.image',
            '{module}s.icon',
            '{module}s.album',
            '{module}s.publish',
            '{module}s.follow',
            'tb2.name',          // nếu cần lấy name từ bảng language
            'tb2.description',   // nếu có
            'tb2.content',          // nếu cần lấy name từ bảng language
            'tb2.meta_title',   // nếu có
            'tb2.meta_keyword',          // nếu cần lấy name từ bảng language
            'tb2.meta_description',
            'tb2.canonical',   // nếu có
            // nếu có
        ])
            ->join('{module}_language as tb2', 'tb2.{module}_id', '=', '{module}s.id')
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }
}
