<?php

namespace App\Services;

use App\Services\Interfaces\MenuServiceInterface;
use App\Services\BaseService;
// use App\Repositories\MenuRepository;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{
    protected $menuRepository;
    protected $nestedsetbie;


    public function __construct(
        MenuRepository $menuRepository,
    ) {
        $this->menuRepository = $menuRepository;
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

    public function create($request, $languageId)
    {
        DB::beginTransaction();
        try {

            $payload = $request->only('menu', 'menu_catalogue_id', 'type');
            if (count($payload['menu']['name'])) {
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuArray = [
                        'menu_catalogue_id' => $payload['menu_catalogue_id'],
                        'type' => $payload['type'],
                        'order' => $payload['menu']['order'][$key],
                        'user_id' => Auth::id()

                    ];
                    $menu = $this->menuRepository->create($menuArray);
                    if ($menu->id > 0) {
                        $menu->languages()->detach([$languageId, $menu->id]);
                        $payLoadLanguage = [
                            'language_id' => $languageId,
                            'name' => $val,
                            'canonical' => $payload['menu']['canonical'][$key],
                        ];

                        $this->menuRepository->createPivot($menu, $payLoadLanguage, 'languages');
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

    public function update($id, $request, $languageId)
    {
        DB::beginTransaction();
        try {




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
            'menus.id',
            'menus.publish',
            'menus.image',
            'menus.order',
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
            'menu_catalogue_id'
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
