<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\{$class}CatalogueServiceInterface as {$class}CatalogueService;
use App\Repositories\Interfaces\{$class}CatalogueRepositoryInterface as {$class}CatalogueRepository;
use App\Http\Requests\Store{$class}CatalogueRequest;
use App\Http\Requests\Update{$class}CatalogueRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Detele{$class}CatalogueRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\Language;

use App\Classes\Nestedsetbie;
use App\Http\Requests\Delete{$class}CatalogueRequest;

class {$class}CatalogueController extends Controller
{

    protected ${module}CatalogueService;
    protected ${module}CatalogueRepository;
    protected $nestedsetbie;
    protected $language;
    public function __construct(
        {$class}CatalogueService ${module}CatalogueService,
        {$class}CatalogueRepository ${module}CatalogueRepository
    ) {
        $this->{module}CatalogueService = ${module}CatalogueService;
        $this->{module}CatalogueRepository = ${module}CatalogueRepository;
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // 
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
    }
    private function initialize()
    {
        $this->nestedsetbie = new Nestedsetbie([
            'table' => '{module}_catalogues',
            'foreignkey' => '{module}_catalogue_id',
            'language_id' => $this->language,
        ]);
    }

    public function index(Request $request)
    {
        // dd(session('app_locale'));
        $this->authorize('modules', '{module}.catalogue.index');


        ${module}Catalogues = $this->{module}CatalogueService->paginate($request, $this->language);
        // ${module}Catalogue:paginate(10);
        $config = $this->config();
        $config["seo"] = __('messages.{module}Catalogue');
        $template = 'backend.{module}.catalogue.index';
        return view('backend.dashboard.layout', compact('template', 'config', '{module}Catalogues'));
    }

    private function config()
    {
        return [
            'js' => [
                '/backend/js/plugins/switchery/switchery.js'
            ],
            'css' => [
                '/backend/css/plugins/switchery/switchery.css'
            ],
            'model' => '{$class}Catalogue'
        ];
    }


    public function create()
    {
        $this->authorize('modules', '{module}.catalogue.create');

        $config = $this->configData();
        $config["seo"] = __('messages.{module}Catalogue');
        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.{module}.catalogue.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config',));
    }


    public function store(Store{$class}CatalogueRequest $request)
    {
        if ($this->{module}CatalogueService->create($request, $languageId)) {
            return redirect()->route('{module}.catalogue.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('{module}.catalogue.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', '{module}.catalogue.update');

        ${module}Catalogue = $this->{module}CatalogueRepository->get{$class}CatalogueById($id, $this->language);
        // dd(${module}Catalogue);

        foreach (${module}Catalogue->{module}_catalogue_language as $language) {
            echo $language->name . '<br>';
        }
        $config = $config = $this->configData();
        $config["seo"] = __('messages.{module}Catalogue');
        $config["method"] = 'edit';
        $template = 'backend.{module}.catalogue.store';
        $dropdown = $this->nestedsetbie->Dropdown();
        $album = json_decode(${module}Catalogue->album);

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', '{module}Catalogue', 'album'));
    }

    public function update($id, Update{$class}CatalogueRequest $request)
    {
        if ($this->{module}CatalogueService->update($id, $request, $languageId)) {
            return redirect()->route('{module}.catalogue.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('{module}.catalogue.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', '{module}.catalogue.destroy');

        $template = 'backend.{module}.catalogue.delete';
        $config["seo"] = __('messages.{module}Catalogue');
        ${module}Catalogue = $this->{module}CatalogueRepository->get{$class}CatalogueById($id, $this->language);
        // dd(${module}Catalogue);
        return view('backend.dashboard.layout', compact('template', '{module}Catalogue', 'config'));
    }

    public function destroy($id, Delete{$class}CatalogueRequest $request)
    {
        // echo 123;
        // die();
        if ($this->{module}CatalogueService->destroy($id, $languageId)) {
            return redirect()->route('{module}.catalogue.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('{module}.catalogue.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }

    private function configData()
    {
        return [
            'js' => [
                '/backend/plugin/ckeditor/ckeditor.js',
                '/backend/plugin/ckfinder/ckfinder.js',
                '/backend/library/finder.js', // nếu file này chỉ cấu hình thêm thì cho sau cùng
                '/backend/js/plugins/switchery/switchery.js',
                '/backend/library/seo.js',


            ],
            'css' => [
                '/backend/css/plugins/switchery/switchery.css'
            ],
        ];
    }
}
