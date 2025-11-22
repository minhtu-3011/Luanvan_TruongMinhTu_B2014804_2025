<?php

namespace App\Services;

use App\Services\Interfaces\AttributeServiceInterface;
use App\Services\BaseService;
// use App\Repositories\AttributeRepository;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class AttributeService extends BaseService implements AttributeServiceInterface
{
    protected $attributeRepository;


    public function __construct(
        AttributeRepository $attributeRepository,
        RouterRepository $routerRepository
    ) {
        $this->routerRepository = $routerRepository;
        $this->attributeRepository = $attributeRepository;
        $this->controllerName = 'AttributeController';
    }

    public function paginate($request, $languageId)
    {



        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish', -1);
        $condition['where'] = [
            ['tb2.language_id', '=', $languageId],
        ];
        $perpage = $request->integer('perpage', 10);

        $attributes = $this->attributeRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'attribute/index', 'groupBy' => $this->paginateSelect()],
            [
                'attributes.id',
                'DESC',
            ],
            [
                ['attribute_language as tb2', 'tb2.attribute_id', '=', 'attributes.id'],
                ['attribute_catalogue_attribute as tb3', 'attributes.id', '=', 'tb3.attribute_id'],

            ],
            ['attribute_catalogues'],
            $this->whereRaw($request)

        );

        // dd($attributes);
        return $attributes;
    }

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            // dd($this->routerRepository);    
            $attribute = $this->createAttribute($request);


            if ($attribute->id > 0) {
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                $this->createRouter($attribute, $request, $this->controllerName, $languageId);
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
            $attribute = $this->attributeRepository->findById($id);

            $payload['user_id'] = Auth::id();
            if ($this->uploadAttribute($attribute, $request)) {
                $this->updateLanguageForAttribute($attribute, $request, $languageId);
                $this->updateCatalogueForAttribute($attribute, $request);
                $this->updateRouter($attribute, $request, $this->controllerName, $languageId);
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
            $attribute = $this->attributeRepository->delete($id);

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



    private function createAttribute($request)
    {
        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $attribute = $this->attributeRepository->create($payload);
        return $attribute;
    }

    private function uploadAttribute($attribute, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        return  $this->attributeRepository->update($attribute->id, $payload);
    }

    private function updateLanguageForAttribute($attribute, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $attribute->id, $languageId);
        $attribute->languages()->detach([$languageId, $attribute->id]);
        return $this->attributeRepository->createPivot($attribute, $payload, 'languages');
    }

    private function updateCatalogueForAttribute($attribute, $request)
    {
        $attribute->attribute_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $attributeId, $languageId)
    {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['attribute_id'] = $attributeId;
        return $payload;
    }





    public function updateStatus($attribute = [])
    {
        DB::beginTransaction();
        try {
            $payload[$attribute['field']] = (($attribute['value'] == 1) ? 0 : 1);

            $attribute = $this->attributeRepository->update($attribute['modelId'], $payload);
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

            $flag = $this->attributeRepository->updateByWhereIn('id', $attribute['id'], $payload);
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

    private function catalogue($request)
    {
        $ids = array_merge(
            $request->input('catalogue', []),
            [$request->attribute_catalogue_id]
        );

        // loại bỏ null, trùng lặp và re-index
        return array_values(array_filter(array_unique($ids)));
    }


    private function whereRaw($request)
    {
        $rawCondition = [];
        if ($request->integer('attribute_catalogue_id') > 0) {
            $rawCondition['whereRaw'] = [
                [
                    'tb3.attribute_catalogue_id IN (
                        SELECT id
                        FROM attribute_catalogues
                        WHERE lft >= (SELECT lft FROM attribute_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM attribute_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$request->integer('attribute_catalogue_id'), $request->integer('attribute_catalogue_id')]
                ],
            ];
        }
        return $rawCondition;
    }


    private function paginateSelect()
    {
        return [
            'attributes.id',
            'attributes.publish',
            'attributes.image',
            'attributes.order',
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
            'album',
            'attribute_catalogue_id'
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
