<?php

namespace App\Repositories;

use App\Models\Attribute;
use App\Repositories\Interfaces\AttributeRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class AttributeRepository
 * @package App\Repositories
 */
class AttributeRepository extends BaseRepository implements AttributeRepositoryInterface
{
    protected $model;

    public function __construct(Attribute $model)
    {
        $this->model = $model;
    }

    public function getAttributeById(int $id = 0, $language_id = 0)
    {
        return $this->model->select([
            'attributes.id',
            'attributes.attribute_catalogue_id',
            'attributes.image',
            'attributes.icon',
            'attributes.album',
            'attributes.publish',
            'attributes.follow',
            'tb2.name',          // nếu cần lấy name từ bảng language
            'tb2.description',   // nếu có
            'tb2.content',          // nếu cần lấy name từ bảng language
            'tb2.meta_title',   // nếu có
            'tb2.meta_keyword',          // nếu cần lấy name từ bảng language
            'tb2.meta_description',
            'tb2.canonical',   // nếu có
            // nếu có
        ])
            ->join('attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id')
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }

    public function searchAttributes(string $keyword = '', array $option = [], int $languageId)
    {
        return $this->model->whereHas('attribute_catalogues', function ($query) use ($option) {
            $query->where('attribute_catalogue_id', $option['attributeCatalogueId']);
        })->whereHas('attribute_language', function ($query) use ($keyword) {
            $query->where('name', 'Like', '%' . $keyword . '%');
        })->get();
    }

    public function findAttributeByIdArray(array $attributeArray = [], int $languageId = 0)
    {
        return $this->model->select([
            'attributes.id',
            'tb2.name'
        ])
            ->join('attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id')
            ->where('tb2.language_id', '=', $languageId)
            ->whereIn('attributes.id', $attributeArray)
            ->get();
    }
}
