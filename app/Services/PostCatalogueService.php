<?php

namespace App\Services;

use App\Services\Interfaces\PostCatalogueServiceInterface;
use App\Services\BaseService;
// use App\Repositories\PostCatalogueRepository;
use App\Repositories\Interfaces\PostCatalogueRepositoryInterface as PostCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Classes\Nestedsetbie;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class PostCatalogueService extends BaseService implements PostCatalogueServiceInterface
{
    protected $postCatalogueRepository;
    protected $routereRepository;

    protected $nestedsetbie;
    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        RouterRepository $routereRepository
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->routereRepository = $routereRepository;
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->currentLanguage(),
        ]);
    }

    public function paginate($request)
    {



        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish', -1);
        $perpage = $request->integer('perpage', 10);
        $postCatalogue = $this->postCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'post/catalogue/index'],
            [
                'post_catalogues.lft',
                'ASC',
            ],
            [
                [
                    'post_catalogue_language as tb2',
                    'tb2.post_catalogue_id',
                    '=',
                    'post_catalogues.id'
                ]
            ],
            [],

        );

        // dd(
        //     $postCatalogue
        // );
        return $postCatalogue;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {

            $payload = $request->only($this->payload());
            $payload['user_id'] = Auth::id();
            // Lấy album từ request, mặc định [] nếu không có
            $album = $request->input('album', []);
            $payload['album'] = json_encode($album);
            $postCatalogue = $this->postCatalogueRepository->create($payload);

            if ($postCatalogue->id > 0) {
                $payloadlanguage = $request->only($this->payloadLanguage());
                $payloadlanguage['canonical'] = Str::slug($payloadlanguage['canonical']);
                $payloadlanguage['language_id'] = $this->currentLanguage();
                $payloadlanguage['post_catalogue_id'] = $postCatalogue->id;

                $language = $this->postCatalogueRepository->createPivot($postCatalogue, $payloadlanguage, 'languages');


                $router = [
                    'canonical' => $payloadlanguage['canonical'],
                    'module_id' => $postCatalogue->id,
                    'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
                ];

                $this->routereRepository->create($router);
            }

            $this->nestedsetbie->Get('level ASC, order ASC');
            $this->nestedsetbie->Recursive(0, $this->nestedsetbie->Set());
            $this->nestedsetbie->Action();




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
            $postCatalogue = $this->postCatalogueRepository->findById($id);


            $payload = $request->only($this->payload());

            // Lấy album từ request, mặc định [] nếu không có
            $album = $request->input('album', []);
            $payload['album'] = json_encode($album);


            $flag = $this->postCatalogueRepository->update($id, $payload);
            if ($flag == true) {
                $payloadlanguage = $request->only($this->payloadLanguage());
                $payloadlanguage['canonical'] = Str::slug($payloadlanguage['canonical']);
                $payloadlanguage['language_id'] = $this->currentLanguage();
                $payloadlanguage['post_catalogue_id'] = $id;

                $postCatalogue->languages()->detach([$payloadlanguage['language_id'], $id]);
                $response = $this->postCatalogueRepository->createPivot($postCatalogue, $payloadlanguage, 'languages');;


                $payloadRouter = [
                    'canonical' => $payloadlanguage['canonical'],
                    'module_id' => $postCatalogue->id,
                    'controllers' => 'App\Http\Controllers\Frontend\PostCatalogueController',
                ];

                $condition = [
                    ['module_id', '=', $id],
                    ['controllers', '=', 'App\Http\Controllers\Frontend\PostCatalogueController'],
                ];
                $router = $this->routereRepository->findByCondition($condition);
                // dd($router);
                $this->routereRepository->update($router->id, $payloadRouter);



                $this->nestedsetbie->Get('level ASC, order ASC');
                $this->nestedsetbie->Recursive(0, $this->nestedsetbie->Set());
                $this->nestedsetbie->Action();
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
            $postCatalogue = $this->postCatalogueRepository->forceDelete($id);
            $this->nestedsetbie->Get('level ASC, order ASC');
            $this->nestedsetbie->Recursive(0, $this->nestedsetbie->Set());
            $this->nestedsetbie->Action();

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

            $postCatalogue = $this->postCatalogueRepository->update($post['modelId'], $payload);
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

            $flag = $this->postCatalogueRepository->updateByWhereIn('id', $post['id'], $payload);
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
            'post_catalogues.id',
            'post_catalogues.publish',
            'post_catalogues.image',
            'post_catalogues.level',
            'post_catalogues.order',

            'tb2.name',
            'tb2.canonical',

        ];
    }

    private function payload()
    {
        return [
            'parent_id',
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
