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
    protected $routerRepository;
    protected $language;
    protected $nestedsetbie;
    protected $controllerName = 'PostCatalogueController';
    public function __construct(
        PostCatalogueRepository $postCatalogueRepository,
        RouterRepository $routerRepository
    ) {
        $this->postCatalogueRepository = $postCatalogueRepository;
        $this->routerRepository = $routerRepository;
        $this->language = session('app_locale');
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'post_catalogues',
            'foreignkey' => 'post_catalogue_id',
            'language_id' => $this->currentLanguage(),
        ]);
    }


    public function paginate($request, $languageId)
    {

        // dd($this->language);
        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish', -1);
        $perpage = $request->integer('perpage', 10);
        $condition['where'] = [
            ['tb2.language_id', '=', $languageId],
        ];
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
            [
                ['tb2.language_id', '=', $languageId]
            ]


        );


        return $postCatalogue;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {

            $postCatalogue = $this->createCatalogue($request);
            if ($postCatalogue->id > 0) {
                $this->updateLanguageForCatalogue($postCatalogue, $request, $languageId);
                dd($request['canonical']);
                $this->createRouter($postCatalogue, $request, $this->controllerName, $languageId);
                $this->nestedset();
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

    public function update($id, $request, $languageId)
    {
        DB::beginTransaction();
        try {

            $postCatalogue = $this->postCatalogueRepository->findById($id);

            $flag = $this->updateCatalogue($postCatalogue, $request);

            if ($flag == true) {

                $this->updateLanguageForCatalogue($postCatalogue, $request, $languageId);

                $this->updateRouter($postCatalogue, $request, $this->controllerName, $languageId);

                $this->nestedsetbie = new Nestedsetbie([
                    'table' => 'post_catalogues',
                    'foreignkey' => 'post_catalogue_id',
                    'language_id' => $languageId,
                ]);
                $this->nestedset();
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

    public function destroy($id, $languageId)
    {
        DB::beginTransaction();
        try {
            $postCatalogue = $this->postCatalogueRepository->forceDelete($id);
            $this->routerRepository->deletedByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\PostCatalogueController'],

            ]);
            $this->nestedsetbie = new Nestedsetbie([
                'table' => 'post_catalogues',
                'foreignkey' => 'post_catalogue_id',
                'language_id' => $languageId,
            ]);
            $this->nestedset();

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




    private function createCatalogue($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $postCatalogue = $this->postCatalogueRepository->create($payload);
        return $postCatalogue;
    }

    private function updateLanguageForCatalogue($postCatalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($postCatalogue, $request);
        $postCatalogue->languages()->detach([$languageId, $postCatalogue->id]);
        $language = $this->postCatalogueRepository->createPivot($postCatalogue, $payload, 'languages');


        return $language;
    }
    private function updateCatalogue($postCatalogue, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->postCatalogueRepository->update($postCatalogue->id, $payload);
        return $flag;
    }

    private function formatLanguagePayload($postCatalogue, $request)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $this->currentLanguage();
        $payload['post_catalogue_id'] = $postCatalogue->id;
        // dd($payload);

        return $payload;
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
            'album',
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
