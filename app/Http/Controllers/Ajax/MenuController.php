<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Language;
use App\Services\Interfaces\MenuCatalogueServiceInterface as MenuCatalogueService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Http\Requests\StoreMenuCatalogueRequest;




class MenuController extends Controller
{

    protected $language;
    protected $menuRepository;
    protected $menuCatalogueService;

    public function __construct(
        MenuRepository $menuRepository,
        MenuCatalogueService $menuCatalogueService
    ) {
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueService = $menuCatalogueService;
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
}
