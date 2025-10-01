<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\{$class}ServiceInterface as {$class}Service;
use App\Repositories\Interfaces\{$class}RepositoryInterface as {$class}Repository;
use App\Http\Requests\Store{$class}Request;
use App\Http\Requests\Update{$class}Request;
use App\Models\Language;


use App\Classes\Nestedsetbie;
use App\Http\Requests\Delete{$class}Request;

class {$class}Controller extends Controller
{

    protected ${module}Service;
    protected ${module}Repository;
    protected $languageRepository;

    protected $nestedsetbie;
    protected $language;
    public function __construct(
        {$class}Service ${module}Service,
        {$class}Repository ${module}Repository,

    ) {
        $this->middleware(function ($request, $next) {
            $locale = app()->getLocale(); // 
            $language = Language::where('canonical', $locale)->first();
            $this->language = $language->id;
            $this->initialize();
            return $next($request);
        });
        $this->{module}Service = ${module}Service;
        $this->{module}Repository = ${module}Repository;

        // $this->language = $this->currentLanguage();

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
        $this->authorize('modules', '{module}.index');

        ${module}s = $this->{module}Service->paginate($request, $this->language);
        // ${module}:paginate(10);

        $config = $this->config();
        $config["seo"] = config('apps.{module}');
        $template = 'backend.{module}.{module}.index';
        $dropdown = $this->nestedsetbie->Dropdown();
        // $language = $this->languageRepository->all();
        // dd($language);

        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', '{module}s'));
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
            'model' => '{$class}'
        ];
    }


    public function create()
    {
        $this->authorize('modules', '{module}.create');

        $config = $this->configData();
        $config["seo"] = config('apps.{module}');
        $config["method"] = 'create';
        $dropdown = $this->nestedsetbie->Dropdown();
        $template = 'backend.{module}.{module}.store';
        return view('backend.dashboard.layout', compact('template', 'dropdown', 'config',));
    }


    public function store(Store{$class}Request $request)
    {
        if ($this->{module}Service->create($request, $this->language)) {
            return redirect()->route('{module}.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('{module}.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', '{module}.update');

        ${module} = $this->{module}Repository->get{$class}ById($id, $this->language);



        $config = $config = $this->configData();
        $config["seo"] = config('apps.{module}');
        $config["method"] = 'edit';
        $template = 'backend.{module}.{module}.store';
        $dropdown = $this->nestedsetbie->Dropdown();
        $album = json_decode(${module}->album);
        return view('backend.dashboard.layout', compact('template', 'config', 'dropdown', '{module}', 'album'));
    }

    public function update($id, Update{$class}Request $request)
    {
        if ($this->{module}Service->update($id, $request, $this->language)) {
            return redirect()->route('{module}.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('{module}.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', '{module}.destroy');

        $template = 'backend.{module}.{module}.delete';
        $config["seo"] = config('apps.{module}');
        ${module} = $this->{module}Repository->get{$class}ById($id, $this->language);
        // dd(${module});
        return view('backend.dashboard.layout', compact('template', '{module}', 'config'));
    }

    public function destroy($id)
    {
        // echo 123;
        // die();
        if ($this->{module}Service->destroy($id, $this->language)) {
            return redirect()->route('{module}.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('{module}.index')->with('error', 'Xoá ban ghi khong thanh cong');
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
