<?php

namespace App\Services;

use App\Services\Interfaces\AttributeCatalogueServiceInterface;
use App\Services\BaseService;
// use App\Repositories\AttributeCatalogueRepository;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
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
class AttributeCatalogueService extends BaseService implements AttributeCatalogueServiceInterface
{
    protected $attributeCatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $nestedsetbie;
    protected $controllerName = 'AttributeCatalogueController';
    public function __construct(
        AttributeCatalogueRepository $attributeCatalogueRepository,
        RouterRepository $routerRepository
    ) {
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
        $this->routerRepository = $routerRepository;
        $this->language = session('app_locale');
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
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
        $attributeCatalogue = $this->attributeCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'attribute/catalogue/index'],
            [
                'attribute_catalogues.lft',
                'ASC',
            ],
            [
                [
                    'attribute_catalogue_language as tb2',
                    'tb2.attribute_catalogue_id',
                    '=',
                    'attribute_catalogues.id'
                ]
            ],
            [],
            [
                ['tb2.language_id', '=', $languageId]
            ]


        );


        return $attributeCatalogue;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            $attributeCatalogue = $this->createCatalogue($request);
            if ($attributeCatalogue->id > 0) {
                $this->updateLanguageForCatalogue($attributeCatalogue, $request, $languageId);
                $this->createRouter($attributeCatalogue, $request, $this->controllerName, $languageId);
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
            $attributeCatalogue = $this->attributeCatalogueRepository->findById($id);
            $flag = $this->updateCatalogue($attributeCatalogue, $request);
            if ($flag == TRUE) {
                $this->updateLanguageForCatalogue($attributeCatalogue, $request, $languageId);
                $this->updateRouter($attributeCatalogue, $request, $this->controllerName, $languageId);
                $this->nestedsetbie = new Nestedsetbie([
                    'table' => 'attribute_catalogues',
                    'foreignkey' => 'attribute_catalogue_id',
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
            $attributeCatalogue = $this->attributeCatalogueRepository->forceDelete($id);
            $this->nestedsetbie = new Nestedsetbie([
                'table' => 'attribute_catalogues',
                'foreignkey' => 'attribute_catalogue_id',
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

    public function updateStatus($attribute = [])
    {
        DB::beginTransaction();
        try {
            $payload[$attribute['field']] = (($attribute['value'] == 1) ? 0 : 1);

            $attributeCatalogue = $this->attributeCatalogueRepository->update($attribute['modelId'], $payload);
            // $this->changeLangueStatus($attribute, $payload[$attribute['field']]);
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

    public function updateStatusAll($attribute)
    {
        DB::beginTransaction();
        try {
            $payload[$attribute['field']] = $attribute['value'];

            $flag = $this->attributeCatalogueRepository->updateByWhereIn('id', $attribute['id'], $payload);
            // $this->changeLangueStatus($attribute, $attribute['value']);
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
        $attributeCatalogue = $this->attributeCatalogueRepository->create($payload);
        return $attributeCatalogue;
    }

    private function updateLanguageForCatalogue($attributeCatalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($attributeCatalogue, $request);
        // dd($payload);
        $attributeCatalogue->languages()->detach([$languageId, $attributeCatalogue->id]);
        // dd($this->language);
        $language = $this->attributeCatalogueRepository->createPivot($attributeCatalogue, $payload, 'languages');

        return $language;
    }
    private function updateCatalogue($attributeCatalogue, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->attributeCatalogueRepository->update($attributeCatalogue->id, $payload);
        return $flag;
    }

    private function formatLanguagePayload($attributeCatalogue, $request)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $this->currentLanguage();
        $payload['attribute_catalogue_id'] = $attributeCatalogue->id;

        return $payload;
    }











    private function paginateSelect()
    {
        return [
            'attribute_catalogues.id',
            'attribute_catalogues.publish',
            'attribute_catalogues.image',
            'attribute_catalogues.level',
            'attribute_catalogues.order',

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
