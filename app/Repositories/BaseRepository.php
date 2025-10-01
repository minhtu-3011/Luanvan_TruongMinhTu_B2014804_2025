<?php


namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use App\Models\Base;
use Illuminate\Support\Arr;
use PhpParser\Node\Expr\Cast\Array_;

use function PHPUnit\Framework\isArray;

class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct(
        Model $model
    ) {
        $this->model = $model;
    }

    public function all(array $relation = [])
    {
        return $this->model->with($relation)->get();
    }

    public function findById(
        int $modelId,
        array $column = ['*'],
        array $relation = []
    ) {
        return $this->model->select($column)->with($relation)->findOrFail($modelId);
    }

    public function findByCondition($condition = [])
    {
        $query  = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }

        return $query->first();
    }

    public function create(array $payload = [])
    {
        $model = $this->model->create($payload);
        return $model->fresh();
    }

    public function update(int $id = 0, array $payload = [])
    {

        $model = $this->findById($id);

        return $model->update($payload);
    }

    public function updateByWhere($condition = [], array $payload = [])
    {
        $query  = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }

        return $query->update($payload);
    }

    public function updateByWhereIn(string $whereInField = '', array $whereIn = [], array $payload = [])
    {
        return $this->model->whereIn($whereInField, $whereIn)->update($payload);
    }


    public function delete(int $id = 0)
    {
        return $this->findById($id)->delete();
    }

    public function forceDelete(int $id = 0)
    {
        return $this->findById($id)->forceDelete();
    }
    public function pagination(
        array $column = ['*'],
        array $condition = [],
        int $perpage = 10,
        array $extends = [],
        array $orderBy = ['id', 'DESC'],
        array $join = [],
        array $relations = [],
        array $rawQuery = [],



    ) {
        $table = $this->model->getTable();
        $query = $this->model->select($column)->distinct();

        // dd($perpage, get_class($languages));

        return $query->keyword($condition['keyword'] ?? null)
            ->publish($condition['publish'] ?? null)
            ->relationCount($relations ?? null)
            ->customWhere($condition['where'] ?? null)        // ✅ đúng
            ->customeWhereRaw($rawQuery ?? null)              // ✅ đúng
            ->customeJoin($join ?? null)                      // ✅ đúng
            ->customeGroupBy($extends ?? null)                // ✅ đúng
            ->customeOrderBy($orderBy ?? null)                // ✅ đúng
            ->paginate($perpage)
            ->withQueryString()
            ->withPath(url('/' . ($extends['path'] ?? '')));
    }

    public function createLanguagePivot($model, array $payload = [])
    {
        return $model->languages()->attach($model->id, $payload);
    }

    public function createPivot($model, array $payload = [], string $relation = '')
    {
        // dd($payload);
        return $model->{$relation}()->attach($model->id, $payload);
    }
}
