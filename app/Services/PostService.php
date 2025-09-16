<?php

namespace App\Services;

use App\Services\Interfaces\PostServiceInterface;
use App\Services\BaseService;
// use App\Repositories\PostRepository;
use App\Repositories\Interfaces\PostRepositoryInterface as PostRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class PostService extends BaseService implements PostServiceInterface
{
    protected $postRepository;
    public function __construct(
        PostRepository $postRepository
    ) {
        $this->postRepository = $postRepository;
    }

    public function paginate($request)
    {



        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish', -1);
        $perpage = $request->integer('perpage', 10);
        $post = $this->postRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'post/catalogue/index'],
            [
                'post.id',
                'DESC',
            ],
            [
                [
                    'post_language as tb2',
                    'tb2.post_id',
                    '=',
                    'posts.id'
                ]
            ],
            [],

        );

        // dd(
        //     $post
        // );
        return $post;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {

            $payload = $request->only($this->payload());
            $payload['user_id'] = Auth::id();
            $payload['album'] = json_encode($payload['album']);
            // dd($payload['album']);
            // dd($payload);
            $post = $this->postRepository->create($payload);

            if ($post->id > 0) {
                $payloadlanguage = $request->only($this->payloadLanguage());
                $payloadlanguage['canonical'] = Str::slug($payloadlanguage['canonical']);
                // dd($payloadlanguage);
                $payloadlanguage['language_id'] = $this->currentLanguage();
                $payloadlanguage['post_catalogue_id'] = $post->id;

                $language = $this->postRepository->createLanguagePivot($post, $payloadlanguage);
                // dd($language);
            }





            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function update($id, $request)
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->findById($id);


            $payload = $request->only($this->payload());
            $payload['album'] = json_encode($payload['album']);

            $flag = $this->postRepository->update($id, $payload);
            if ($flag == true) {
                $payloadlanguage = $request->only($this->payloadLanguage());
                $payloadlanguage['canonical'] = Str::slug($payloadlanguage['canonical']);
                $payloadlanguage['language_id'] = $this->currentLanguage();
                $payloadlanguage['post_catalogue_id'] = $id;

                $post->languages()->detach([$payloadlanguage['language_id'], $id]);
                $response = $this->postRepository->createLanguagePivot($post, $payloadlanguage);
            }



            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $post = $this->postRepository->forceDelete($id);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatus($post = [])
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = (($post['value'] == 1) ? 0 : 1);

            $post = $this->postRepository->update($post['modelId'], $payload);
            // $this->changeLangueStatus($post, $payload[$post['field']]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }

    public function updateStatusAll($post)
    {
        DB::beginTransaction();
        try {
            $payload[$post['field']] = $post['value'];

            $flag = $this->postRepository->updateByWhereIn('id', $post['id'], $payload);
            // $this->changeLangueStatus($post, $post['value']);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();
            die();
            return false;
        }
    }



    private function paginateSelect()
    {
        return [
            'posts.id',
            'posts.publish',
            'posts.image',
            'posts.level',
            'posts.order',

            'tb2.name',
            'tb2.canonical',

        ];
    }

    private function payload()
    {
        return [
            'follow',
            'publish',
            'image',
            'album'
        ];
    }

    private function payloadLanguage()
    {
        return [
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }
}
