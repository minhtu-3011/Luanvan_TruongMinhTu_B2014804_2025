<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class UserService
 * @package App\Services
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    protected $model;

    public function __construct(
        User $model
    ) {
        $this->model = $model;
    }

    public function pagination(
        array $column = ['*'],
        array $condition = [],
        array $join = [],
        array $extends = [],
        int $perpage = 10,
        array $relations = [],
        array $orderBy = ['id', 'DESC'],


    ) {
        $query = $this->model->select($column)->where(function ($query) use ($condition) {
            if (isset($condition['keyword']) && !empty($condition['keyword'])) {
                $query->where('name', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('email', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('address', 'LIKE', '%' . $condition['keyword'] . '%')
                    ->orWhere('phone', 'LIKE', '%' . $condition['keyword'] . '%');
            }

            if (isset($condition['publish']) && $condition['publish'] != -1) {
                $query->where('publish', '=', $condition['publish']);
            }

            return $query;
        })->with('user_catalogues');
        if (!empty($join)) {
            $query->join(...$join);
        }

        return $query->paginate($perpage)
            ->withQueryString()
            ->withPath(url('/' . $extends['path']));
    }
}
