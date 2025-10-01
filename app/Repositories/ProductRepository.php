<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class ProductRepository
 * @package App\Repositories
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected $model;

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function getProductById(int $id = 0, $language_id = 0)
    {
        return $this->model->select([
            'products.id',
            'products.product_catalogue_id',
            'products.image',
            'products.icon',
            'products.album',
            'products.publish',
            'products.follow',
            'tb2.name',          // nếu cần lấy name từ bảng language
            'tb2.description',   // nếu có
            'tb2.content',          // nếu cần lấy name từ bảng language
            'tb2.meta_title',   // nếu có
            'tb2.meta_keyword',          // nếu cần lấy name từ bảng language
            'tb2.meta_description',
            'tb2.canonical',   // nếu có
            // nếu có
        ])
            ->join('product_language as tb2', 'tb2.product_id', '=', 'products.id')
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }
}
