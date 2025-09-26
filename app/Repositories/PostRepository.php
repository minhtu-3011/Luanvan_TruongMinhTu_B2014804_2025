<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use App\Repositories\BaseRepository;

/**
 * Class PostRepository
 * @package App\Repositories
 */
class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(Post $model)
    {
        $this->model = $model;
    }

    public function getPostById(int $id = 0, $language_id = 0)
    {
        return $this->model->select([
            'posts.id',
            'posts.post_catalogue_id',
            'posts.image',
            'posts.icon',
            'posts.album',
            'posts.publish',
            'posts.follow',
            'tb2.name',          // nếu cần lấy name từ bảng language
            'tb2.description',   // nếu có
            'tb2.content',          // nếu cần lấy name từ bảng language
            'tb2.meta_title',   // nếu có
            'tb2.meta_keyword',          // nếu cần lấy name từ bảng language
            'tb2.meta_description',
            'tb2.canonical',   // nếu có
            // nếu có
        ])
            ->join('post_language as tb2', 'tb2.post_id', '=', 'posts.id')
            ->where('tb2.language_id', '=', $language_id)
            ->find($id);
    }
}
