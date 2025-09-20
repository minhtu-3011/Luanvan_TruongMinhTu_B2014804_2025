<?php

namespace App\Traits;



trait QueryScopes
{



    public function scopeKeyword($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', '%' . $keyword . '%');
        }

        return $query;
    }

    public function scopePublish($query, $publish)
    {
        if ($publish !== null && $publish != -1) {   // chỉ bỏ qua khi null
            $query->where('publish', '=',  $publish);
        }
    }

    public function scopeCustomWhere($query, $where = [])
    {
        if (!empty($where) && is_array($where)) {
            foreach ($where as $val) {
                $query->where($val[0], $val[1], $val[2]);
            }
        }
        return $query;
    }


    public function scopeCustomeWhereRaw($query, $rawQuery = [])
    {
        if (isset($rawQuery['whereRaw']) && count($rawQuery['whereRaw'])) {
            foreach ($rawQuery['whereRaw'] as $key => $val) {
                $query->whereRaw($val[0], $val[1]);
            }
        }
    }

    public function scopeRelationCount($query, $relations)
    {
        if (!empty($relations)) {
            foreach ($relations as $relation) {
                $query->withCount($relation);
            }
        }
        return $query;
    }


    public function scopeRelation($query, $relations)
    {
        if (!empty($relations)) {
            foreach ($relations as $relation) {
                $query->with($relation);
            }
        }
        return $query;
    }

    public function scopeCustomeJoin($query, $join)
    {
        if (!empty($join)) {
            foreach ($join as $key => $val) {
                $query->join($val[0], $val[1], $val[2], $val[3]);
            }
        }
        return $query;
    }

    public function scopeCustomeGroupBy($query, $groupBy)
    {
        if (!empty($groupBy['groupBy'])) {
            $query->groupBy($groupBy['groupBy']);
        }
        return $query;
    }

    public function scopeCustomeOrderBy($query, $orderBy)
    {
        if (!empty($orderBy)) {
            $query->orderBy($orderBy[0], $orderBy[1]);
        }
        return $query;
    }
}
