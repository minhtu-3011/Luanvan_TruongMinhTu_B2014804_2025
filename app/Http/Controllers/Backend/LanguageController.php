<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;

use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;
use App\Http\Requests\TranslateRequest;

use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{

    protected $languageService;
    protected $languageRepository;
    public function __construct(
        LanguageService $languageService,
        LanguageRepository $languageRepository
    ) {
        $this->languageService = $languageService;
        $this->languageRepository = $languageRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'language.index');
        $perpage = max(1, (int) $request->input('perpage', 10));

        $languages = $this->languageService->paginate($request);
        // dd(get_class($languages));
        // $language:paginate(10);

        $config = $this->config();
        $config["seo"] = config('apps.language');
        $template = 'backend.language.index';
        $config['model'] = 'Language';

        return view('backend.dashboard.layout', compact('template', 'config', 'languages'));
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
        ];
    }


    public function create()
    {
        $this->authorize('modules', 'language.create');

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = config('apps.language');
        $config["method"] = 'create';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout', compact('template', 'config',));
    }


    public function store(StoreLanguageRequest $request)
    {
        if ($this->languageService->create($request)) {
            return redirect()->route('language.index')->with('success', 'Thêm mới bản ghi thành công');
        }
        return redirect()->route('language.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'language.update');

        $language = $this->languageRepository->findById($id);

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = config('apps.language');
        $config["method"] = 'edit';
        $template = 'backend.language.store';
        return view('backend.dashboard.layout', compact('template', 'config', 'language'));
    }

    public function update($id, UpdateLanguageRequest $request)
    {
        if ($this->languageService->update($id, $request)) {
            return redirect()->route('language.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('language.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'language.destroy');

        $template = 'backend.language.delete';
        $config["seo"] = config('apps.language');
        $language = $this->languageRepository->findById($id);
        return view('backend.dashboard.layout', compact('template', 'language', 'config'));
    }

    public function destroy($id)
    {
        if ($this->languageService->destroy($id)) {
            return redirect()->route('language.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('language.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }

    public function switchBackendLanguage($id)
    {
        $language = $this->languageRepository->findById($id);
        if ($this->languageService->switch($id)) {
            session(['app_locale' => $language->canonical]);
            App::setLocale($language->canonical);
        }
        // dd($language->canonical);
        return redirect()->back();
    }
    // public function switchBackendLanguage($id)
    // {
    //     $language = $this->languageRepository->findById($id);

    //     if ($this->languageService->switch($id)) {
    //         // Lưu vào session thôi, middleware sẽ lo phần còn lại
    //         session(['app_locale' => strtolower($language->canonical)]);
    //     }

    //     return back();
    // }


    public function translate($id = 0, $languageId = 0, $model = '')
    {

        $repositoryInstance = $this->repositoryInstance($model);
        $languageInstance = $this->repositoryInstance('Language');
        $currentLanguage = $languageInstance->findByCondition([
            ['canonical', '=', session('app_locale')]
        ]);
        // dd($currentLanguage);

        $method = 'get' . $model . 'ById';
        $object = $repositoryInstance->{$method}($id, $currentLanguage->id);
        $objectTranslate = $repositoryInstance->{$method}($id, $languageId);
        // dd($objectTranslate);


        $this->authorize('modules', 'language.translate');
        $config = [
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
        $option = [
            'id' => $id,
            'languageId' => $languageId,
            'model' => $model
        ];

        // dd($languageId);

        $config['seo'] = config('apps.language');
        $template = 'backend.language.translate';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'object',
            'objectTranslate',
            'option'
        ));
    }

    public function storeTranslate(TranslateRequest $request)
    {
        $option = $request->input('option');
        // dd($option);
        if ($this->languageService->saveTranslate($option, $request)) {
            return redirect()->back()->with('success', 'cap nhat ban ghi thanh cong');
        }
        return redirect()->back()->with('error', 'cap nhat ban ghi khong thanh cong');
    }

    private function repositoryInstance($model)
    {
        $RepositoryNamespace = '\App\Repositories\\' . ucfirst($model) . 'Repository';

        if (class_exists($RepositoryNamespace)) {
            $RepositoryInstance = app($RepositoryNamespace);
        }

        return $RepositoryInstance;
    }
}
