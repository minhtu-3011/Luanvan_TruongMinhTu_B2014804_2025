<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserCatalogueServiceInterface as UserCatalogueService;
use App\Repositories\Interfaces\UserCatalogueRepositoryInterface as UserCatalogueRepository;
use App\Repositories\Interfaces\PermissionRepositoryInterface as PermissionRepository;
use App\Http\Requests\StoreUserCatalogueRequest;
use Illuminate\Support\Facades\Auth;

class UserCatalogueController extends Controller
{

    protected $userCatalogueService;
    protected $userCatalogueRepository;
    protected $permissionRepository;
    public function __construct(
        UserCatalogueService $userCatalogueService,
        UserCatalogueRepository $userCatalogueRepository,
        PermissionRepository $permissionRepository
    ) {
        $this->userCatalogueService = $userCatalogueService;
        $this->userCatalogueRepository = $userCatalogueRepository;
        $this->permissionRepository = $permissionRepository;
    }

    public function index(Request $request)
    {
        $this->authorize('modules', 'user.catalogue.index');


        $userCatalogues = $this->userCatalogueService->paginate($request);
        // $users = User::paginate(10);

        $config = $this->config();
        $config["seo"] = config('apps.usercatalogue');
        $template = 'backend.user.catalogue.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'userCatalogues'));
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
            'model' => 'UserCatalogue'
        ];
    }


    public function create()
    {
        $this->authorize('modules', 'user.catalogue.create');

        $config = [
            'js' => [
                '/backend/library/location.js',

            ]
        ];
        $config["seo"] = config('apps.usercatalogue');
        $config["method"] = 'create';
        $template = 'backend.user.catalogue.store';
        return view('backend.dashboard.layout', compact('template', 'config',));
    }


    public function store(StoreUserCatalogueRequest $request)
    {
        if ($this->userCatalogueService->create($request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $this->authorize('modules', 'user.catalogue.update');

        $userCatalogue = $this->userCatalogueRepository->findById($id);

        $config = [
            'js' => [
                '/backend/library/location.js',

            ]
        ];
        $config["seo"] = config('apps.usercatalogue');
        $config["method"] = 'edit';
        $template = 'backend.user.catalogue.store';
        return view('backend.dashboard.layout', compact('template', 'config', 'userCatalogue'));
    }

    public function update($id, StoreUserCatalogueRequest $request)
    {
        if ($this->userCatalogueService->update($id, $request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $this->authorize('modules', 'user.catalogue.destroy');

        $template = 'backend.user.catalogue.delete';
        $config["seo"] = config('apps.usercatalogue');
        $userCatalogue = $this->userCatalogueRepository->findById($id);
        return view('backend.dashboard.layout', compact('template', 'userCatalogue', 'config'));
    }

    public function destroy($id)
    {
        if ($this->userCatalogueService->destroy($id)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }


    public function permission()
    {
        $this->authorize('modules', 'user.catalogue.permission');

        $userCatalogues = $this->userCatalogueRepository->all(['permissions']);
        $permissions = $this->permissionRepository->all();

        // dd($userCatalogues);

        $template = 'backend.user.catalogue.permission';
        $config["seo"] = __('messages.permission');

        return view('backend.dashboard.layout', compact(
            'template',
            'userCatalogues',
            'permissions',
            'config'
        ));
    }

    public function updatePermission(Request $request)
    {
        if ($this->userCatalogueService->setPermission($request)) {
            return redirect()->route('user.catalogue.index')->with('success', 'Cập nhật quyền thành công');
        }
        return redirect()->route('user.catalogue.index')->with('error', 'Có vấn đề xảy ra, Hãy thử lại');
    }
}
