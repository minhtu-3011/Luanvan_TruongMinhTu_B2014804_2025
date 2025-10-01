<?php

namespace App\Services;

use App\Services\Interfaces\ProductCatalogueServiceInterface;
use App\Services\BaseService;
// use App\Repositories\ProductCatalogueRepository;
use App\Repositories\Interfaces\ProductCatalogueRepositoryInterface as ProductCatalogueRepository;
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
class ProductCatalogueService extends BaseService implements ProductCatalogueServiceInterface
{
    protected $productCatalogueRepository;
    protected $routerRepository;
    protected $language;
    protected $nestedsetbie;
    protected $controllerName = 'ProductCatalogueController';
    public function __construct(
        ProductCatalogueRepository $productCatalogueRepository,
        RouterRepository $routerRepository
    ) {
        $this->productCatalogueRepository = $productCatalogueRepository;
        $this->routerRepository = $routerRepository;
        $this->language = session('app_locale');
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
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
        $productCatalogue = $this->productCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'product/catalogue/index'],
            [
                'product_catalogues.lft',
                'ASC',
            ],
            [
                [
                    'product_catalogue_language as tb2',
                    'tb2.product_catalogue_id',
                    '=',
                    'product_catalogues.id'
                ]
            ],
            [],


        );


        return $productCatalogue;
    }

    public function create($request, $languageId)
    {

        DB::beginTransaction();
        try {
            $productCatalogue = $this->createCatalogue($request);
            if ($productCatalogue->id > 0) {
                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                $this->createRouter($productCatalogue, $request, $this->controllerName);
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
            $productCatalogue = $this->productCatalogueRepository->findById($id);
            // dd($productCatalogue);

            $flag = $this->updateCatalogue($productCatalogue, $request, $languageId);
            // dd($flag);
            if ($flag == true) {

                $this->updateLanguageForCatalogue($productCatalogue, $request, $languageId);
                $this->updateRouter($productCatalogue, $request, $this->controllerName);
                $this->nestedsetbie = new Nestedsetbie([
                    'table' => 'product_catalogues',
                    'foreignkey' => 'product_catalogue_id',
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
            $productCatalogue = $this->productCatalogueRepository->forceDelete($id);
            $this->nestedsetbie = new Nestedsetbie([
                'table' => 'product_catalogues',
                'foreignkey' => 'product_catalogue_id',
                'language_id' =>  $languageId,
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

    public function updateStatus($product = [])
    {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = (($product['value'] == 1) ? 0 : 1);

            $productCatalogue = $this->productCatalogueRepository->update($product['modelId'], $payload);
            // $this->changeLangueStatus($product, $payload[$product['field']]);
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

    public function updateStatusAll($product)
    {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = $product['value'];

            $flag = $this->productCatalogueRepository->updateByWhereIn('id', $product['id'], $payload);
            // $this->changeLangueStatus($product, $product['value']);
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
        $productCatalogue = $this->productCatalogueRepository->create($payload);
        return $productCatalogue;
    }

    private function updateLanguageForCatalogue($productCatalogue, $request, $languageId)
    {
        $payload = $this->formatLanguagePayload($productCatalogue, $request);
        // dd($payload);
        $productCatalogue->languages()->detach([$languageId, $productCatalogue->id]);
        // dd($this->language);
        $language = $this->productCatalogueRepository->createPivot($productCatalogue, $payload, 'languages');
        // dd($language);
        return $language;
    }

    private function updateCatalogue($productCatalogue, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $flag = $this->productCatalogueRepository->update($productCatalogue->id, $payload);
        return $flag;
    }

    private function formatLanguagePayload($productCatalogue, $request)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $this->currentLanguage();
        $payload['product_catalogue_id'] = $productCatalogue->id;

        return $payload;
    }



    private function paginateSelect()
    {
        return [
            'product_catalogues.id',
            'product_catalogues.publish',
            'product_catalogues.image',
            'product_catalogues.level',
            'product_catalogues.order',

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
