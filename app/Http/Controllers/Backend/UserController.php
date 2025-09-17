<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Interfaces\UserServiceInterface as UserService;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\UserRepositoryInterface as UserRepository;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{

    protected $userService;
    protected $provinceRepository;
    protected $userRepository;
    public function __construct(
        UserService $userService,
        ProvinceRepository $provinceRepository,
        UserRepository $userRepository,
    ) {
        $this->userService = $userService;
        $this->provinceRepository = $provinceRepository;
        $this->userRepository = $userRepository;
    }

    public function index(Request $request)
    {

        $users = $this->userService->paginate($request);
        // $users = User::paginate(10);

        $config = $this->config();
        $config["seo"] = config('apps.user');
        $template = 'backend.user.user.index';
        return view('backend.dashboard.layout', compact('template', 'config', 'users'));
    }


    public function create()
    {
        $provinces = $this->provinceRepository->all();

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = config('apps.user');
        $config["method"] = 'create';
        $template = 'backend.user.user.store';
        return view('backend.dashboard.layout', compact('template', 'config', 'provinces'));
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
            'model' => 'User'
        ];
    }

    public function store(StoreUserRequest $request)
    {
        if ($this->userService->create($request)) {
            return redirect()->route('user.index')->with('success', 'Them moi ban ghi thanh cong');
        }
        return redirect()->route('user.index')->with('error', 'them moi ban ghi khong thanh cong');
    }

    public function edit($id)
    {
        $user = $this->userRepository->findById($id);
        $provinces = $this->provinceRepository->all();

        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ]
        ];
        $config["seo"] = config('apps.user');
        $config["method"] = 'edit';
        $template = 'backend.user.user.store';
        return view('backend.dashboard.layout', compact('template', 'config', 'provinces', 'user'));
    }

    public function update($id, UpdateUserRequest $request)
    {
        if ($this->userService->update($id, $request)) {
            return redirect()->route('user.index')->with('success', 'Cap nhat ban ghi thanh cong');
        }
        return redirect()->route('user.index')->with('error', 'Cap nhat ban ghi khong thanh cong');
    }

    public function delete($id)
    {
        $template = 'backend.user.user.delete';
        $config["seo"] = config('apps.user');
        $user = $this->userRepository->findById($id);
        return view('backend.dashboard.layout', compact('template', 'user', 'config'));
    }

    public function destroy($id)
    {
        if ($this->userService->destroy($id)) {
            return redirect()->route('user.index')->with('success', 'Xoá bản ghi thành công');
        }
        return redirect()->route('user.index')->with('error', 'Xoá ban ghi khong thanh cong');
    }
}
