<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\PermissionServiceInterface as PermissionService;
use App\Repositories\Interfaces\PermissionRepositoryInterface as PermissionRepository;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Support\Facades\App;

class PermissionController extends Controller
{

    protected $permissionService;
    protected $permissionRepository;
    public function __construct(
        PermissionService $permissionService,
        PermissionRepository $permissionRepository
    ) {
        $this->permissionService = $permissionService;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'permission.index');

        $permissions = $this->permissionService->paginate($request);
        // $permission:paginate(10);

        $config = $this->config();
        $config["seo"] = __('messages.permission');
        $template = 'backend.permission.index';
        $config['model'] = 'Permission';

        return view('backend.dashboard.layout', compact('template', 'config', 'permissions'));
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
        $this->authorize('modules', 'permission.create');

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = __('messages.permission');
        $config["method"] = 'create';
        $template = 'backend.permission.store';
        return view('backend.dashboard.layout', compact('template', 'config',));
    }


    public function store(StorePermissionRequest $request)
    {
        if ($this->permissionService->create($request)) {
            return redirect()->route('permission.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('permission.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'permission.update');

        $permission = $this->permissionRepository->findById($id);

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = __('messages.permission');
        $config["method"] = 'edit';
        $template = 'backend.permission.store';
        return view('backend.dashboard.layout', compact('template', 'config', 'permission'));
    }

    public function update($id, UpdatePermissionRequest $request)
    {
        if ($this->permissionService->update($id, $request)) {
            return redirect()->route('permission.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('permission.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'permission.destroy');

        $template = 'backend.permission.delete';
        $config["seo"] = __('messages.permission');
        $permission = $this->permissionRepository->findById($id);
        return view('backend.dashboard.layout', compact('template', 'permission', 'config'));
    }

    public function destroy($id)
    {
        if ($this->permissionService->destroy($id)) {
            return redirect()->route('permission.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('permission.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }
}
