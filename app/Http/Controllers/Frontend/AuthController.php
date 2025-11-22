<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Http\Controllers\FrontendController;

class AuthController extends FrontendController
{
    protected $systemRepository;
    protected $provinceRepository;


    public function __construct(
        SystemRepository $systemRepository,
        ProvinceRepository $provinceRepository,
    ) {
        $this->systemRepository = $systemRepository;
        $this->provinceRepository = $provinceRepository;
        parent::__construct(
            $systemRepository,
        );
    }


    public function index()
    {
        $provinces = $this->provinceRepository->all();
        $system = $this->system;
        $seo = [
            'meta_title' => $this->system['seo_meta_title'],
            'meta_keyword' => $this->system['seo_meta_keyword'],
            'meta_description' => $this->system['seo_meta_description'],
            'meta_image' => $this->system['seo_meta_images'],
            'canonical' => config('app.url'),
        ];
        $config = [
            'js' => [
                '/backend/library/location.js',
                '/backend/library/finder.js',
                '/backend/plugin/ckfinder/ckfinder.js'

            ],
            'css' => [
                '/frontend/core/css/customer.css',
                '/frontend/resources/style.css',
            ]
        ];

        return view('frontend.customer.login', compact('config', 'provinces', 'system', 'seo'));
    }

    // Xử lý đăng nhập
    public function login(AuthRequest $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate(); // giữ session
            $customer = Auth::guard('customer')->user();
            // Thêm session flag nếu muốn (có thể dùng Auth::guard()->check() cũng được)
            $customerId = $customer->id;

            // dd($customerId);
            session([
                'customer_logged_in' => true,
                'customer_id' => $customerId
            ]);


            return redirect()->route('home.index') // quay về home
                ->with('success', 'Đăng nhập thành công');
        }

        return redirect()->route('customer.loginview')
            ->with('error', 'Email hoặc mật khẩu không chính xác');
    }


    // Đăng xuất
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('customer.loginview');
    }
}
