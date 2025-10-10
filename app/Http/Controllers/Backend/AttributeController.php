<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\AttributeServiceInterface as AttributeService;
use App\Repositories\Interfaces\AttributeRepositoryInterface as AttributeRepository;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Language;


use App\Classes\Nestedsetbie;
use App\Http\Requests\DeleteAttributeRequest;

class AttributeController extends Controller
{

    protected $attributeService;
    protected $attributeRepository;
    protected $languageRepository;

    protected $nestedsetbie;
    protected $language;
    public function __construct(
        AttributeService $attributeService,
        AttributeRepository $attributeRepository,

    ) {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // 
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->attributeService = $attributeService;
        $this->attributeRepository = $attributeRepository;

        // $this->language = $this->currentLanguage();

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
        $this->authorize('modules', 'attribute.index');

        $attributes = $this->attributeService->paginate($request, $this->language);
        // $attribute:paginate(10);

        $config = $this->config();
        $config["seo"] = __('messages.attribute');
        $template = 'backend.attribute.attribute.index';
        $dropdown = $this->nestedsetbie->Dropdown();
        // $language = $this->languageRepository->all();
        // dd($language);

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'attributes'));
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
            'model' => 'Attribute'
        ];
    }


    public function create()
    {
        $this->authorize('modules', 'attribute.create');

        $config = $this->configData();
        $config["seo"] = __('messages.attribute');

        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.attribute.attribute.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config',));
    }


    public function store(StoreAttributeRequest $request)
    {
        if ($this->attributeService->create($request, $this->language)) {
            return redirect()->route('attribute.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('attribute.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'attribute.update');

        $attribute = $this->attributeRepository->getAttributeById($id, $this->language);



        $config = $config = $this->configData();
        $config["seo"] = __('messages.attribute');
        $config["method"] = 'edit';
        $template = 'backend.attribute.attribute.store';
        $dropdown = $this->nestedsetbie->Dropdown();
        $album = json_decode($attribute->album);
        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', 'attribute', 'album'));
    }

    public function update($id, UpdateAttributeRequest $request)
    {
        if ($this->attributeService->update($id, $request, $this->language)) {
            return redirect()->route('attribute.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('attribute.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'attribute.destroy');

        $template = 'backend.attribute.attribute.delete';
        $config["seo"] = __('messages.attribute');

        $attribute = $this->attributeRepository->getAttributeById($id, $this->language);
        // dd($attribute);
        return view('backend.dashboard.layout', compact('template', 'attribute', 'config'));
    }

    public function destroy($id)
    {
        // echo 123;
        // die();
        if ($this->attributeService->destroy($id, $this->language)) {
            return redirect()->route('attribute.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('attribute.index')->with('error', 'Xoá ban ghi khong thanh cong');
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
