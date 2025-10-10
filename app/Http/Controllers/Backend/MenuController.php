<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\MenuServiceInterface as MenuService;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use App\Repositories\Interfaces\MenuCatalogueRepositoryInterface as MenuCatalogueRepository;
use App\Services\Interfaces\MenuCatalogueServiceInterface as MenuCatalogueService;
use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Language;

class MenuController extends Controller
{

    protected $menuService;
    protected $menuRepository;
    protected $menuCatalogueRepository;
    protected $menuCatalogueService;

    public function __construct(
        MenuService $menuService,
        MenuRepository $menuRepository,
        MenuCatalogueRepository $menuCatalogueRepository,
        MenuCatalogueService $menuCatalogueService,
    ) {
        $this->menuService = $menuService;
        $this->menuRepository = $menuRepository;
        $this->menuCatalogueRepository = $menuCatalogueRepository;
        $this->menuCatalogueService = $menuCatalogueService;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // 
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'menu.index');

        $menuCatalogues = $this->menuCatalogueService->paginate($request, 5);
        // $menus = Menu::paginate(10);

        $config = $this->config();
        $config["seo"] = __('messages.menu');
        $template = 'backend.menu.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'menuCatalogues'));
    }


    public function create()
    {
        $this->authorize('modules', 'menu.create');
        $menuCatalogues = $this->menuCatalogueRepository->all();
        $config = $this->config();
        $config["seo"] = __('messages.menu');
        $config["method"] = 'create';
        $template = 'backend.menu.store';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'menuCatalogues',
        ));
    }

    private function config()
    {
        return [
            'js' => [
                '/backend/js/plugins/switchery/switchery.js',
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js',
                '/backend/library/menu.js'
            ],
            'css' => [
                '/backend/css/plugins/switchery/switchery.css',
            ],
            'model' => 'Menu'
        ];
    }

    public function store(StoreMenuRequest $request)
    {
        if ($this->menuService->create($request, $this->language)) {
            return redirect()->route('menu.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('menu.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'menu.update');
        $menuCatalogues = $this->menuCatalogueRepository->all();
        $menu = $this->menuRepository->findById($id);
        $provinces = $this->provinceRepository->all();

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js',
                '/backend/library/menu.js'

            ]
        ];
        $config["seo"] = __('messages.menu');
        $config["method"] = 'edit';
        $template = 'backend.menu.store';
        return view('backend.dashboard.layout', compact('template', 'config', 'provinces', 'menu', 'menuCatalogues'));
    }

    public function update($id, UpdateMenuRequest $request)
    {
        if ($this->menuService->update($id, $request)) {
            return redirect()->route('menu.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('menu.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'menu.destroy');

        $template = 'backend.menu.delete';
        $config["seo"] = __('messages.menu');
        $menu = $this->menuRepository->findById($id);
        return view('backend.dashboard.layout', compact('template', 'menu', 'config'));
    }

    public function destroy($id)
    {
        if ($this->menuService->destroy($id)) {
            return redirect()->route('menu.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('menu.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }
}
