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

    public function findByCondition(
        $condition = [],
        $flag = false,
        $relation = [],
        array $orderBy = ['id', 'desc'],
        array $param = [],
        array $withCount = [],
    ) {

        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }
        if (isset($param['whereIn'])) {
            $query->whereIn($param['whereInField'], $param['whereIn']);
        }

        $query->with($relation);
        $query->withCount($withCount);
        $query->orderBy($orderBy[0], $orderBy[1]);
        return ($flag == false) ? $query->first() : $query->get();
    }

    public function create(array $payload = [])
    {
        $model = $this->model->create($payload);
        return $model->fresh();
    }



    public function createBatch(array $payLoad = [])
    {
        return $this->model->insert($payLoad);
    }




    public function update(int $id = 0, array $payload = [])
    {

        $model = $this->findById($id);
        $model->fill($payload);
        $model->save();
        return $model;
    }


    public function updateOrInsert(array $payload = [], array $condition = [])
    {
        return $this->model->updateOrInsert($payload, $condition);
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


    public function forceDeleteByCondition(array $condition = [])
    {
        $query = $this->model->newQuery();
        foreach ($condition as $key => $val) {
            $query->where($val[0], $val[1], $val[2]);
        }

        return $query->forceDelete();
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


    public function findByWhereHas(array $condition = [], string $relation = '', string $alias = '')
    {
        return $this->model->with('languages')->whereHas($relation, function ($query) use ($condition, $alias) {
            foreach ($condition as $key => $val) {
                $query->where($alias . '.' . $key, $val);
            }
        })->first();
    }

    public function findWidgetItem(array $condition = [], int $language_id = 5, string $alias = '')
    {
        return $this->model->with([
            'languages' => function ($query) use ($language_id) {
                $query->where('language_id', $language_id);
            }
        ])
            ->whereHas('languages', function ($query) use ($condition, $alias) {
                foreach ($condition as $key => $val) {
                    $query->where($alias . '.' . $val[0], $val[1], $val[2]);
                }
            })->get();
    }
}
