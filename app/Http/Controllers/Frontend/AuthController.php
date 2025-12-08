<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Interfaces\ProvinceRepositoryInterface as ProvinceRepository;
use App\Repositories\Interfaces\SystemRepositoryInterface  as SystemRepository;
use App\Http\Controllers\FrontendController;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

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


    public function loginview()
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

    public function registerview()
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

        return view('frontend.customer.register', compact('config', 'provinces', 'system', 'seo'));
    }

    // Xử lý đăng nhập
    public function login(AuthRequest $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials)) {
            $request->session()->regenerate();
            $customer = Auth::guard('customer')->user();
            $customerId = $customer->id;

            // dd($customerId);
            session([
                'customer_logged_in' => true,
                'customer_id' => $customerId
            ]);


            return redirect()->route('home.index')
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

    public function register(Request $request)
    {
        // Validate trước
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:customers,email',
            'password' => 'required|min:6',
            'phone' => 'required|digits_between:9,11|unique:customers,phone',
            're_password' => 'required|same:password',
            'customer_catalogue_id' => 'required|integer'
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email đã tồn tại.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            're_password.same' => 'Mật khẩu nhập lại không khớp.',
        ]);

        // Tạo customer
        $customer = Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'customer_catalogue_id' => $request->customer_catalogue_id,
            'ip' => $request->ip(),
            'code' => $request->code,
            'user_agent' => $request->userAgent(),
            'publish' => 1,
            'source_id' => $request->source_id,

        ]);

        if ($customer) {


            return redirect()->route('home.index')
                ->with('success', 'Đăng ký thành công!');
        }

        return back()->with('error', 'Đăng ký thất bại. Vui lòng thử lại!');
    }
}
