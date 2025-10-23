<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
// use App\Repositories\ProductRepository;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface as ProductVariantLanguageRepository;
use App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface as ProductVariantAttributeRepository;
use App\Services\Interfaces\ProductCatalogueServiceInterface as ProductCatalogueService;
use App\Repositories\Interfaces\PromotionRepositoryInterface as PromotionRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    protected $productVariantLanguageRepository;
    protected $productCatalogueService;
    protected $promotionRepository;
    protected $productVariantAttributeRepository;


    public function __construct(
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
        ProductVariantLanguageRepository $productVariantLanguageRepository,
        ProductCatalogueService $productCatalogueService,
        PromotionRepository $promotionRepository,
        ProductVariantAttributeRepository $productVariantAttributeRepository,
    ) {
        $this->routerRepository = $routerRepository;
        $this->promotionRepository = $promotionRepository;
        $this->productRepository = $productRepository;
        $this->productVariantLanguageRepository = $productVariantLanguageRepository;
        $this->productVariantAttributeRepository = $productVariantAttributeRepository;
        $this->productCatalogueService = $productCatalogueService;
        $this->controllerName = 'ProductController';
    }

    public function paginate($request, $languageId)
    {



        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish', -1);
        $condition['where'] = [
            ['tb2.language_id', '=', $languageId],
        ];
        $perpage = $request->integer('perpage', 10);

        $products = $this->productRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perpage,
            ['path' => 'product.index', 'groupBy' => $this->paginateSelect()],
            [
                'products.id',
                'DESC',
            ],
            [
                ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
                ['product_catalogue_product as tb3', 'products.id', '=', 'tb3.product_id'],

            ],
            ['product_catalogues'],
            $this->whereRaw($request)

        );

        // dd($products);
        return $products;
    }




    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {
            // dd($this->routerRepository);    
            $product = $this->createProduct($request);


            if ($product->id > 0) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->createRouter($product, $request, $this->controllerName, $languageId);

                $product->product_variants()->delete();
                $this->createVariant($product, $request, $languageId);
            }
            DB::commit();
            // die();
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
            $product = $this->uploadProduct($id, $request);
            if ($product) {
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter(
                    $product,
                    $request,
                    $this->controllerName,
                    $languageId
                );


                $product->product_variants()->each(function ($variant) {
                    $variant->languages()->detach();
                    $variant->attributes()->detach();
                    $variant->delete();
                });
                if ($request->input('attribute')) {
                    $this->createVariant($product, $request, $languageId);
                }

                $this->productCatalogueService->setAttribute($product);
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
            $product = $this->productRepository->delete($id);
            $this->routerRepository->deletedByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\ProductController'],

            ]);
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



    private function createProduct($request)
    {


        $payload = $request->only($this->payload());
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price(($payload['price']) ?? 0);
        $payload['attributeCatalogue'] = $this->formatJson($request, 'attributeCatalogue');
        $payload['attribute'] = $this->formatJson($request, 'attribute');
        $payload['variant'] = $this->formatJson($request, 'variant');
        $product = $this->productRepository->create($payload);
        return $product;
    }

    private function uploadProduct($id, $request)
    {
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = convert_price($payload['price']);
        if (!isset($payload['attribute'])) {
            $payload['attribute'] = null;
        }
        return $this->productRepository->update($id, $payload);
    }

    private function updateLanguageForProduct($product, $request, $languageId)
    {
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $product->id, $languageId);
        $product->languages()->detach([$languageId, $product->id]);
        return $this->productRepository->createPivot($product, $payload, 'languages');
    }

    private function updateCatalogueForProduct($product, $request)
    {
        $product->product_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $productId, $languageId)
    {
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] = $languageId;
        $payload['product_id'] = $productId;
        return $payload;
    }





    public function updateStatus($product = [])
    {
        DB::beginTransaction();
        try {
            $payload[$product['field']] = (($product['value'] == 1) ? 0 : 1);

            $product = $this->productRepository->update($product['modelId'], $payload);
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

            $flag = $this->productRepository->updateByWhereIn('id', $product['id'], $payload);
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

    private function catalogue($request)
    {
        $ids = array_merge(
            $request->input('catalogue', []),
            [$request->product_catalogue_id]
        );

        // loại bỏ null, trùng lặp và re-index
        return array_values(array_filter(array_unique($ids)));
    }


    private function whereRaw($request)
    {
        $rawCondition = [];
        if ($request->integer('product_catalogue_id') > 0) {
            $rawCondition['whereRaw'] = [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$request->integer('product_catalogue_id'), $request->integer('product_catalogue_id')]
                ],
            ];
        }
        return $rawCondition;
    }


    private function createVariant($product, $request, $languageId)
    {
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload, $product);
        $variants = $product->product_variants()->createMany($variant);
        $variantsId = $variants->pluck('id');
        $productVariantLanguage = [];
        $variantAttribute = [];

        $attributeCombines = $this->comebineAttribute(array_values($payload['attribute']));
        if (count($variantsId)) {
            foreach ($variantsId as $key => $val) {
                $productVariantLanguage[] = [
                    'product_variant_id' => $val,
                    'language_id' => $languageId,
                    'name' => $payload['productVariant']['name'][$key]
                ];

                if (count($attributeCombines)) {
                    foreach ($attributeCombines[$key] as $attributeId) {
                        $variantAttribute[] = [
                            'product_variant_id' => $val,
                            'attribute_id' => $attributeId
                        ];
                    }
                }
            }
        }
        // dd($variantAttribute);
        $variantLanguage = $this->productVariantLanguageRepository->createBatch($productVariantLanguage);
        $variantAttribute = $this->productVariantAttributeRepository->createBatch($variantAttribute);
    }


    private function comebineAttribute($attributes = [], $index = 0)
    {
        if ($index === count($attributes)) return [[]];

        $subCombines = $this->comebineAttribute($attributes, $index + 1);
        $combines = [];
        foreach ($attributes[$index] as $key => $val) {
            foreach ($subCombines as $keySub => $valSub) {
                $combines[] = array_merge([$val], $valSub);
            }
        }
        return $combines;
    }



    private function createVariantArray($payload, $product): array
    {
        $variant = [];
        if (isset($payload['variant']['sku']) && count($payload['variant']['sku'])) {
            foreach ($payload['variant']['sku'] as $key => $val) {

                $vId = ($payload['productVariant']['id'][$key]) ?? '';
                $productVariantId = sortString($vId);
                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $product->id . ', ' . $payload['productVariant']['id'][$key]);
                $variant[] = [
                    'uuid' => $uuid,
                    'code' => $productVariantId,
                    'quantity' => ($payload['variant']['quantity'][$key]) ?? '',
                    'sku' => $val,
                    'price' => ($payload['variant']['price'][$key]) ? convert_price($payload['variant']['price'][$key]) : '',
                    'barcode' => ($payload['variant']['barcode'][$key]) ?? '',
                    'file_name' => ($payload['variant']['file_name'][$key]) ?? '',
                    'file_url' => ($payload['variant']['file_url'][$key]) ?? '',
                    'album' => ($payload['variant']['album'][$key]) ?? '',
                    'user_id' => Auth::id(),
                ];
            }
        }
        return $variant;
    }

    public function combineProductAndPromotion($productId, $products, $flag = false)
    {

        $promotions = $this->promotionRepository->findByProduct($productId);

        if ($promotions) {

            if ($flag == true) {
                $products->promotions = ($promotions[0]) ?? [];
                return $products;
            }

            foreach ($products as $index => $product) {
                foreach ($promotions as $key => $promotion) {
                    if ($promotion->product_id == $product->id) {
                        $products[$index]->promotions = $promotion;
                    }
                }
            }
        }
        return $products;
    }


    public function getAttribute($product, $language)
    {
        $product->attributeCatalogue = [];
        if (isset($product->attribute) && !is_null($product->attribute)) {
            $attributeCatalogueId = array_keys($product->attribute);
            $attrCatalogues = $this->attributeCatalogueRepository->getAttributeCatalogueWhereIn($attributeCatalogueId, 'attribute_catalogues.id', $language);
            /* ---- */
            $attributeId = array_merge(...$product->attribute);
            $attrs = $this->attributeRepository->findAttributeByIdArray($attributeId, $language);
            if (!is_null($attrCatalogues)) {
                foreach ($attrCatalogues as $key => $val) {
                    $tempAttributes = [];
                    foreach ($attrs as $attr) {
                        if ($val->id == $attr->attribute_catalogue_id) {
                            $tempAttributes[] = $attr;
                        }
                    }
                    $val->attributes = $tempAttributes;
                }
            }
            $product->attributeCatalogue = $attrCatalogues;
        }
        return $product;
    }

    public function filter($request)
    {

        $perpage = $request->input('perpage');
        $param['priceQuery'] = $this->priceQuery($request);
        $param['attributeQuery'] = $this->attributeQuery($request);
        $param['rateQuery'] = $this->rateQuery($request);
        $param['productCatalogueQuery'] = $this->productCatalogueQuery($request);


        $query = $this->combineFilterQuery($param);
        $orderBy = $this->orderByQuery($query['join'], $request);

        $products = $this->productRepository->filter($query, $perpage, $orderBy);
        $productId = $products->pluck('id')->toArray();
        if (count($productId) && !is_null($productId)) {
            $products = $this->combineProductAndPromotion($productId, $products);
        }

        return $products;
    }

    private function orderByQuery($joins, $request)
    {
        $flag = false;
        $attributes = $request->input('attributes');
        if (is_array($joins) && count($joins)) {

            foreach ($joins as $key => $val) {
                if (is_null($val)) continue;
                if (count($val) && in_array('product_variants as pv', $val)) {
                    $flag = true;
                }
            }
        }
        // return ($flag == true && count($attributes) > 1) ? 'variant_id' : 'products.id';
        return 'products.id';
    }

    private function combineFilterQuery($param)
    {
        $query = [];

        foreach ($param as $array) {
            foreach ($array as $key => $value) {
                if (!isset($query[$key])) {
                    $query[$key] = [];
                }

                if (is_array($value)) {
                    $query[$key] = array_merge($query[$key], $value);
                } else {
                    $query[$key][] = $value;
                }
            }
        }
        return $query;
    }

    private function productCatalogueQuery($request)
    {

        $productCatalogueId = $request->input('productCatalogueId');
        $query['join'] = null;
        $query['whereRaw'] = null;
        if ($productCatalogueId > 0) {
            $query['join'] = [
                ['product_catalogue_product as pcp', 'pcp.product_id', '=', 'products.id']
            ];
            $query['whereRaw'] = [
                [
                    'pcp.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                    )',
                    [$productCatalogueId, $productCatalogueId]
                ]
            ];
        }
        return $query;
    }


    private function rateQuery($request)
    {
        $rates = $request->input('rate');
        $query['join'] = null;
        $query['having'] = null;

        if (!is_null($rates) && count($rates)) {
            $query['join'] = [
                ['reviews', 'reviews.reviewable_id', '=', 'products.id']
            ];
            $rateCondition = [];
            $bindings = [];

            foreach ($rates as $rate) {
                if ($rate != 5) {
                    $minRate = $rate;
                    $maxRate = $rate . '.9';
                    $rateCondition[] = '(AVG(reviews.score) >= ? AND AVG(reviews.score) <= ?)';
                    $bindings[] = $minRate;
                    $bindings[] = $maxRate;
                } else {
                    $rateCondition[] = 'AVG(reviews.score) = ?';
                    $bindings[] = 5;
                }
            }

            $query['where'] = function ($query) {
                $query->where('reviews.reviewable_type', '=', 'App\\Models\\Product');
            };
            $query['having'] = function ($query) use ($rateCondition, $bindings) {
                $query->havingRaw(implode(' OR ', $rateCondition), $bindings);
            };
        }
        return $query;
    }

    private function attributeQuery($request)
    {
        $attributes = $request->input('attributes');
        $query['select'] = null;
        $query['join'] = null;
        $query['where'] = null;

        if (!is_null($attributes) && count($attributes)) {


            $concatExpression = 'CONCAT(';
            $first = true;

            $query['join'] = [
                ['product_variants as pv', 'pv.product_id', '=', 'products.id'],
            ];
            foreach ($attributes as $key => $attribute) {
                $joinKey = 'tb' . $key;
                $query['join'][] = [
                    "product_variant_attribute as {$joinKey}",
                    "$joinKey.product_variant_id",
                    '=',
                    'pv.id'
                ];
                $query['where'][] = function ($query) use ($joinKey, $attribute) {
                    foreach ($attribute as $attr) {
                        $query->orWhere("$joinKey.attribute_id", '=', $attr);
                    }
                };

                if (!$first) {
                    $concatExpression .= ', ';
                } else {
                    $first = false;
                }

                $concatExpression .= "GROUP_CONCAT(DISTINCT $joinKey.attribute_id, ',')";
            }

            $concatExpression .= ' ) as attribute_concat';


            $query['select'] = "pv.price as variant_price, pv.sku as variant_sku, pv.id as variant_id, $concatExpression";
        }

        return $query;
    }


    private function priceQuery($request)
    {
        $price = $request->input('price');
        $priceMin = str_replace('đ', '', convert_price($price['price_min']));
        $priceMax = str_replace('đ', '', convert_price($price['price_max']));
        $query['select'] = null;
        $query['join'] = null;
        $query['having'] = null;

        if ($priceMax > $priceMin) {
            $query['join'] = [
                ['promotion_product_variant as ppv', 'ppv.product_id', '=', 'products.id'],
                ['promotions', 'ppv.promotion_id', '=', 'promotions.id']
            ];
            $query['select'] = "
                (products.price - MAX(
                    IF(promotions.maxDiscountValue != 0,
                        LEAST(
                            CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN products.price * discountValue / 100
                            ELSE 0
                            END,
                            promotions.maxDiscountValue 
                        ),
                        CASE 
                                WHEN discountType = 'cash' THEN discountValue
                                WHEN discountType = 'percent' THEN products.price * discountValue / 100
                        ELSE 0
                        END
                    )
                )) as discounted_price
            ";

            $query['having'] = function ($query) use ($priceMin, $priceMax) {
                $query->havingRaw('discounted_price >= ? AND discounted_price <= ?', [$priceMin, $priceMax]);
            };
        }
        return $query;
    }



    private function paginateSelect()
    {
        return [
            'products.id',
            'products.publish',
            'products.image',
            'products.order',
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
            'price',
            'made_id',
            'code',
            'product_catalogue_id',
            'attributeCatalogue',
            'attribute',
            'variant'
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
