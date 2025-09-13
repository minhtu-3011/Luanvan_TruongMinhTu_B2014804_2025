<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\LanguageServiceInterface as LanguageService;
use App\Repositories\Interfaces\LanguageRepositoryInterface as LanguageRepository;
use App\Http\Requests\StoreLanguageRequest;
use App\Http\Requests\UpdateLanguageRequest;

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

        $languages = $this->languageService->paginate($request);
        // $language:paginate(10);

        $config = $this->config();
        $config["seo"] = config('apps.language');
        $template = 'backend.language.index';
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
            return redirect()->route('language.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('language.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
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
}
