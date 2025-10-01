<?php

namespace App\Services;

use App\Services\Interfaces\{$class}CatalogueServiceInterface;
use App\Services\BaseService;
// use App\Repositories\{$class}CatalogueRepository;
use App\Repositories\Interfaces\{$class}CatalogueRepositoryInterface as {$class}CatalogueRepository;
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
class {$class}CatalogueService extends BaseService implements {$class}CatalogueServiceInterface
{
    protected ${module}CatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $nestedsetbie;
    protected $controllerName = '{$class}CatalogueController';
    public function __construct(
        {$class}CatalogueRepository ${module}CatalogueRepository,
        RouterRepository $routerRepository
    ) {
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
        $this->routerRepository = $routerRepository;
        $this->language = session('app_locale');
        $this->nestedsetbie = new Nestedsetbie([
            'table' => '{module}_catalogues',
            'foreignkey' => '{module}_catalogue_id',
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
        ${module}Catalogue = $this->{module}CatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => '{module}/catalogue/index'],
            [
                '{module}_catalogues.lft',
                'ASC',
            ],
            [
                [
                    '{module}_catalogue_language as tb2',
                    'tb2.{module}_catalogue_id',
                    '=',
                    '{module}_catalogues.id'
                ]
            ],
            [],
            [
                ['tb2.language_id', '=', $languageId]
            ]


        );


        return ${module}Catalogue;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            ${module}Catalogue = $this->createCatalogue($request);
            if (${module}Catalogue->id > 0) {
                $this->updateLanguageForCatalogue(${module}Catalogue, $request, $languageId);
                $this->createRouter(${module}Catalogue, $request, $this->controllerName);
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
            ${module}Catalogue = $this->{module}CatalogueRepository->findById($id);
            // dd(${module}Catalogue);

            $flag = $this->updateCatalogue(${module}Catalogue, $request);
            // dd($flag);
            $this->nestedsetbie = new Nestedsetbie([
                'table' => '{module}_catalogues',
                'foreignkey' => '{module}_catalogue_id',
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

    public function destroy($id, $languageId)
    {
        DB::beginTransaction();
        try {
            ${module}Catalogue = $this->{module}CatalogueRepository->forceDelete($id);
            $this->nestedsetbie = new Nestedsetbie([
                'table' => '{module}_catalogues',
                'foreignkey' => '{module}_catalogue_id',
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

    public function updateStatus(${module} = [])
    {
        DB::beginTransaction();
        try {
            $payload[${module}['field']] = ((${module}['value'] == 1) ? 0 : 1);

            ${module}Catalogue = $this->{module}CatalogueRepository->update(${module}['modelId'], $payload);
            // $this->changeLangueStatus(${module}, $payload[${module}['field']]);
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

    public function updateStatusAll(${module})
    {
        DB::beginTransaction();
        try {
            $payload[${module}['field']] = ${module}['value'];

            $flag = $this->{module}CatalogueRepository->updateByWhereIn('id', ${module}['id'], $payload);
            // $this->changeLangueStatus(${module}, ${module}['value']);
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
        ${module}Catalogue = $this->{module}CatalogueRepository->create($payload);
        return ${module}Catalogue;
    }

    private function updateLanguageForCatalogue(${module}Catalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload(${module}Catalogue, $request);
        // dd($payload);
        ${module}Catalogue->languages()->detach([$languageId, ${module}Catalogue->id]);
        // dd($this->language);
        $language = $this->{module}CatalogueRepository->createPivot(${module}Catalogue, $payload, 'languages');

        return $language;
    }
    private function updateCatalogue(${module}Catalogue, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->{module}CatalogueRepository->update(${module}Catalogue->id, $payload);
        return $flag;
    }

    private function formatLanguagePayload(${module}Catalogue, $request)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $this->currentLanguage();
        $payload['{module}_catalogue_id'] = ${module}Catalogue->id;

        return $payload;
    }











    private function paginateSelect()
    {
        return [
            '{module}_catalogues.id',
            '{module}_catalogues.publish',
            '{module}_catalogues.image',
            '{module}_catalogues.level',
            '{module}_catalogues.order',

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
