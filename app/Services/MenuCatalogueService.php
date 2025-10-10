<?php

namespace App\Services;

use App\Services\Interfaces\MenuCatalogueServiceInterface;
use App\Services\BaseService;
// use App\Repositories\MenuCatalogueRepository;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class UserCatalogueService
 * @package App\Services
 */
class MenuCatalogueService extends BaseService implements MenuCatalogueServiceInterface
{
    protected $menuCatalogueRepository;


    public function __construct(
        MenuCatalogueRepository $menuCatalogueRepository,
    ) {
        $this->menuCatalogueRepository = $menuCatalogueRepository;
    }

    public function paginate($request)
    {

        $condition['keyword'] = addslashes($request->input('keyword'));
        $condition['publish'] = $request->integer('publish');
        $perPage = $request->integer('perpage');
        $menuCatalogues = $this->menuCatalogueRepository->pagination(
            $this->paginateSelect(),
            $condition,
            $perPage,
            ['path' => 'menu/index'],
        );
        return $menuCatalogues;
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $payload = $request->only('name', 'keyword');
            $payload['keyword'] = Str::slug($payload['keyword']);
            $menuCatalogue = $this->menuCatalogueRepository->create($payload);
            // dd($menuCatalogue->id);


            DB::commit();
            return [
                'name' => $menuCatalogue->name,
                'id' => $menuCatalogue->id,
            ];
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
            'id',
            'name',
            'keyword',
            'publish'

        ];
    }
}
