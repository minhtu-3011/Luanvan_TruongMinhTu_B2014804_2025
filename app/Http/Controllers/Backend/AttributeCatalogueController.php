<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\AttributeCatalogueServiceInterface as AttributeCatalogueService;
use App\Repositories\Interfaces\AttributeCatalogueRepositoryInterface as AttributeCatalogueRepository;
use App\Http\Requests\StoreAttributeCatalogueRequest;
use App\Http\Requests\UpdateAttributeCatalogueRequest;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\DeteleAttributeCatalogueRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;
use App\Models\Language;

use App\Classes\Nestedsetbie;
use App\Http\Requests\DeleteAttributeCatalogueRequest;

class AttributeCatalogueController extends Controller
{

    protected $attributeCatalogueService;
    protected $attributeCatalogueRepository;
    protected $nestedsetbie;
    protected $language;
    public function __construct(
        AttributeCatalogueService $attributeCatalogueService,
        AttributeCatalogueRepository $attributeCatalogueRepository
    ) {
        $this->attributeCatalogueService = $attributeCatalogueService;
        $this->attributeCatalogueRepository = $attributeCatalogueRepository;
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
            'table' => 'attribute_catalogues',
            'foreignkey' => 'attribute_catalogue_id',
            'language_id' => $this->language,
        ]);
    }

    public function index(Request $request)
    {
        // dd(session('app_locale'));
        $this->authorize('modules', 'attribute.catalogue.index');


        $attributeCatalogues = $this->attributeCatalogueService->paginate($request, $this->language);
        // $attributeCatalogue:paginate(10);
        $config = $this->config();
        $config["seo"] = __('messages.attributeCatalogue');
        $template = 'backend.attribute.catalogue.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'attributeCatalogues'));
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
            'model' => 'AttributeCatalogue'
        ];
    }


    public function create()
    {
        $this->authorize('modules', 'attribute.catalogue.create');

        $config = $this->configData();
        $config["seo"] = __('messages.attributeCatalogue');
        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.attribute.catalogue.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config',));
    }


    public function store(StoreAttributeCatalogueRequest $request)
    {
        if ($this->attributeCatalogueService->create($request, $this->language)) {
            return redirect()->route('attribute.catalogue.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('attribute.catalogue.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'attribute.catalogue.update');

        $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id, $this->language);
        // dd($attributeCatalogue);

        foreach ($attributeCatalogue->attribute_catalogue_language as $language) {
            echo $language->name . '<br>';
        }
        $config = $config = $this->configData();
        $config["seo"] = __('messages.attributeCatalogue');
        $config["method"] = 'edit';
        $template = 'backend.attribute.catalogue.store';
        $dropdown = $this->nestedsetbie->Dropdown();
        $album = json_decode($attributeCatalogue->album);

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'attributeCatalogue', 'album'));
    }

    public function update($id, UpdateAttributeCatalogueRequest $request)
    {
        if ($this->attributeCatalogueService->update($id, $request, $this->language)) {
            return redirect()->route('attribute.catalogue.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('attribute.catalogue.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'attribute.catalogue.destroy');

        $template = 'backend.attribute.catalogue.delete';
        $config["seo"] = __('messages.attributeCatalogue');
        $attributeCatalogue = $this->attributeCatalogueRepository->getAttributeCatalogueById($id, $this->language);
        // dd($attributeCatalogue);
        return view('backend.dashboard.layout', compact('template', 'attributeCatalogue', 'config'));
    }

    public function destroy($id, DeleteAttributeCatalogueRequest $request)
    {
        // echo 123;
        // die();
        if ($this->attributeCatalogueService->destroy($id, $this->language)) {
            return redirect()->route('attribute.catalogue.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('attribute.catalogue.index')->with('error', 'Xoá ban ghi khong thanh cong');
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
