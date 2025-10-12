<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Services\Interfaces\MenuCatalogueServiceInterface as MenuCatalogueService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Services\Interfaces\MenuServiceInterface as MenuService;
use App\Http\Requests\StoreMenuCatalogueRequest;
use App\Classes\Nestedsetbie;




class MenuController extends Controller
{

    protected $language;
    protected $menuRepository;
    protected $menuService;
    protected $menuCatalogueService;
    protected $nestedsetbie;

    public function __construct(
        MenuRepository $menuRepository,
        MenuCatalogueService $menuCatalogueService,
        MenuService $menuService
    ) {
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueService = $menuCatalogueService;
        $this->menuService = $menuService;
        $this->nestedsetbie = new Nestedsetbie([
            'table' => 'product_catalogues',
            'foreignkey' => 'product_catalogue_id',
            'language_id' => $this->currentLanguage(),
        ]);
    }

    public function createCatalogue(StoreMenuCatalogueRequest $request)
    {
        $menuCatalogue = $this->menuCatalogueService->create($request);
        if ($menuCatalogue !== FALSE) {
            return response()->json([
                'code' => 0,
                'message' => 'Tạo nhóm menu thành công!',
                'data' => $menuCatalogue
            ]);
        }

        return response()->json([
            'message' => 'Có vấn đề xảy ra, hãy thử lại',
            'code' => 1
        ]);
    }

    public function drag(Request $request)
    {
        $json = json_decode($request->string('json'), TRUE);
        $menuCatalogueId = $request->integer('menuCatalogueId');

        $flag = $this->menuService->dragUpdate($json, $menuCatalogueId);
    }
}
