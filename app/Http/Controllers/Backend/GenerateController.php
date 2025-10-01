<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\GenerateServiceInterface as GenerateService;
use App\Repositories\Interfaces\GenerateRepositoryInterface as GenerateRepository;

use App\Http\Requests\StoreGenerateRequest;
use App\Http\Requests\UpdateGenerateRequest;
use App\Http\Requests\TranslateRequest;

use Illuminate\Support\Facades\App;

class GenerateController extends Controller
{

    protected $generateService;
    protected $generateRepository;
    public function __construct(
        GenerateService $generateService,
        GenerateRepository $generateRepository
    ) {
        $this->generateService = $generateService;
        $this->generateRepository = $generateRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'generate.index');

        $generates = $this->generateService->paginate($request);
        // $generate:paginate(10);

        $config = $this->config();
        $config["seo"] = __('messages.generate');
        $template = 'backend.generate.index';
        $config['model'] = 'Generate';

        return view('backend.dashboard.layout', compact('template', 'config', 'generates'));
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
        $this->authorize('modules', 'generate.create');

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = __('messages.generate');
        $config["method"] = 'create';
        $config['model'] = 'Generate';
        $template = 'backend.generate.store';
        return view('backend.dashboard.layout', compact('template', 'config',));
    }


    public function store(StoreGenerateRequest $request)
    {
        if ($this->generateService->create($request)) {
            return redirect()->route('generate.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('generate.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'generate.update');

        $generate = $this->generateRepository->findById($id);

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = __('messages.generate');
        $config["method"] = 'edit';
        $config['model'] = 'Generate';
        $template = 'backend.generate.store';
        return view('backend.dashboard.layout', compact('template', 'config', 'language'));
    }

    public function update($id, UpdateGenerateRequest $request)
    {
        if ($this->generateService->update($id, $request)) {
            return redirect()->route('generate.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('generate.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'generate.destroy');

        $template = 'backend.generate.delete';
        $config["seo"] = __('messages.generate');
        $config['model'] = 'Generate';
        $generate = $this->generateRepository->findById($id);
        return view('backend.dashboard.layout', compact('template', 'language', 'config'));
    }

    public function destroy($id)
    {
        if ($this->generateService->destroy($id)) {
            return redirect()->route('generate.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('generate.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }
}
