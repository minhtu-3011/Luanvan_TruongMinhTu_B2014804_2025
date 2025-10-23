<?php

namespace App\Services;

use App\Services\Interfaces\MenuServiceInterface;
use App\Services\BaseService;
// use App\Repositories\MenuRepository;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{
    protected $menuRepository;
    protected $menuCatalogueRepository;
    protected $routerRepository;
    protected $nestedsetbie;


    public function __construct(
        MenuRepository $menuRepository,
        RouterRepository $routerRepository,
        MenuCatalogueRepository $menuCatalogueRepository,
    ) {
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->routerRepository = $routerRepository;

        // $this->nestedsetbie = new Nestedsetbie([
        //     'table' => 'product_catalogues',
        //     'foreignkey' => 'product_catalogue_id',
        //     'language_id' => $this->currentLanguage(),
        // ]);
    }

    public function paginate($request, $languageId)
    {

        return [];
    }

    public function save($request, $languageId)
    {
        DB::beginTransaction();
        try {

            $payload = $request->only('menu', 'menu_catalogue_id');
            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id' => $payload['menu_catalogue_id'],
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::id()

                    ];

                    if ($menuId == 0) {
                        $menuSave = $this->menuRepository->create($menuArray);
                    } else {
                        $menuSave = $this->menuRepository->update($menuId, $menuArray);
                        if ($menuSave->rgt - $menuSave->lft > 1) {
                            $this->menuRepository->updateByWhere(
                                [
                                    ['lft', '>', $menuSave->lft],
                                    ['rgt', '<', $menuSave->rgt],
                                ],
                                ['menu_catalogue_id' => $payload['menu_catalogue_id']]
                            );
                        }
                    }

                    if ($menuSave->id > 0) {
                        $menuSave->languages()->detach([$languageId, $menuSave->id]);
                        $payLoadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key],
                        ];

                        $this->menuRepository->createPivot($menuSave, $payLoadLanguage, 'languages');
                    }
                }
                $this->nestedsetbie = new Nestedsetbie([
                    'table' => 'menus',
                    'foreignkey' => 'menu_id',
                    'isMenu' => TRUE,
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

    public function saveChildren($request, $languageId, $menu)
    {
        DB::beginTransaction();
        try {

            $payload = $request->only('menu');
            // dd($payload);
            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id' => $menu->menu_catalogue_id,
                        'parent_id' => $menu->id,
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::id(),
                    ];

                    if ($menuId == 0) {
                        $menuSave = $this->menuRepository->create($menuArray);
                    } else {
                        $menuSave = $this->menuRepository->update($menuId, $menuArray);
                    }

                    // dd($menuSave);



                    if ($menuSave->id > 0) {
                        $menuSave->languages()->detach([$languageId, $menuSave->id]);
                        $payLoadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key],
                        ];

                        $this->menuRepository->createPivot($menuSave, $payLoadLanguage, 'languages');
                    }
                }

                // dd($payload['menu_catalogue_id']);

                $this->nestedsetbie = new Nestedsetbie([
                    'table' => 'menus',
                    'foreignkey' => 'menu_id',
                    'isMenu' => TRUE,
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



    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $this->menuRepository->forceDeleteByCondition([
                ['menu_catalogue_id', '=', $id],
            ]);

            $this->menuCatalogueRepository->forceDelete($id);



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




    public function getAndConvertMenu($menu = null,  $language = 5): array
    {

        $menuList = $this->menuRepository->findByCondition([
            ['parent_id', '=', $menu->id]
        ], TRUE, [
            'languages' => function ($query) use ($language) {
                $query->where('language_id', $language);
            }
        ]);
        return $this->convertMenu($menuList);
    }


    public function convertMenu($menuList = null)
    {
        $temp = [];
        $fields = ['name', 'canonical', 'order', 'id'];
        if (count($menuList)) {
            foreach ($menuList as $key => $val) {
                foreach ($fields as $field) {
                    if ($field == 'name' || $field == 'canonical') {
                        $temp[$field][] = $val->languages->first()->pivot->{$field};
                    } else {
                        $temp[$field][] = $val->{$field};
                    }
                }
            }
        }
        return $temp;
    }


    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, int $languageId = 5, $parentId = 0)
    {
        if (count($json)) {
            foreach ($json as $key => $val) {
                $update = [
                    'order' => count($json) - $key,
                    'parent_id' => $parentId,
                ];

                $menu = $this->menuRepository->update($val['id'], $update);
                if (isset($val['children']) && count($val['children'])) {
                    $this->dragUpdate($val['children'], $menuCatalogueId, $languageId, $val['id']);
                }
            }
        }
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => TRUE,
            'language_id' => $languageId,
        ]);
        $this->nestedset();
    }


    public function findMenuItemTranslate($menus, int $currentLanguage = 5, int $languageId = 5)
    {
        $output = [];
        if (count($menus)) {
            foreach ($menus as $key =>  $menu) {
                $canonical = $menu->languages->first()->pivot->canonical;
                $detailMenu = $this->menuRepository->findById($menu->id, ['*'], [
                    'languages' => function ($query) use ($languageId) {
                        $query->where('language_id',  $languageId);
                    }
                ]);
                if ($detailMenu) {
                    if ($detailMenu->languages->isNotEmpty()) {
                        $menu->translate_name = $detailMenu->languages->first()->pivot->name;
                        $menu->translate_canonical = $detailMenu->languages->first()->pivot->canonical;
                    } else {
                        $router = $this->routerRepository->findByCondition([
                            ['canonical', '=', $canonical]
                        ]);

                        if ($router) {
                            $controller = explode('\\', $router->controllers);
                            $model = str_replace('Controller', '', end($controller));

                            $serviceInterfaceNamespace = '\App\Repositories\\' . $model . 'Repository';
                            if (class_exists($serviceInterfaceNamespace)) {
                                $serviceInstance = app($serviceInterfaceNamespace);
                            }
                            $alias = Str::snake($model) . '_language';
                            $object = $serviceInstance->findByWhereHas([
                                'canonical' => $canonical,
                                'language_id' => $currentLanguage
                            ], 'languages', $alias);

                            // dd($object);

                            if ($object) {
                                $translateObject = $object->languages()->where('language_id', $languageId)->first([$alias . '.name', $alias . '.canonical']);
                                if (!is_null($translateObject)) {
                                    $menu->translate_name = $translateObject->name;
                                    $menu->translate_canonical = $translateObject->canonical;
                                }
                            }
                        }
                    }
                }
                $output[] = $menu;
            }
        }
        return $output;
    }

    public function saveTranslateMenu($request, int $languageId = 5)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('translate');
            if (count($payload['translate']['name'])) {
                foreach ($payload['translate']['name'] as $key => $val) {
                    if ($val == null) continue;
                    $temp = [
                        'language_id' => $languageId,
                        'name' => $val,
                        'canonical' => $payload['translate']['canonical'][$key],
                    ];
                    $menu = $this->menuRepository->findById($payload['translate']['id'][$key]);
                    $menu->languages()->detach([$languageId, $payload['translate']['id'][$key]]);
                    $this->menuRepository->createPivot($menu, $temp, 'languages');
                }
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
}
